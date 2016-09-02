<?php

namespace AppBundle\Service;

use AppBundle\Document\Channel;
use AppBundle\Document\Message;
use AppBundle\Document\Repository\MessageRepository;
use AppBundle\Document\Team;
use AppBundle\Document\User;
use AppBundle\Service\Exception\ServiceException;
use Doctrine\Common\Collections\Criteria;
use Monolog\Logger;

/**
 * Basic service for interacting with Messages.
 */
class MessageService
{
    /**
     * @var SlackClient
     */
    protected $slack;

    /**
     * @var MessageRepository
     */
    protected $repository;

    /**
     * @var UserService
     */
    protected $users;

    /**
     * Slack constructor to setup the service.
     *
     * @param SlackClient $slack
     * @param MessageRepository $repository
     * @param UserService $users
     */
    public function __construct(SlackClient $slack, MessageRepository $repository, UserService $users)
    {
        $this->slack = $slack;
        $this->repository = $repository;
        $this->users = $users;
    }

    /**
     * Method to sync a user with the slack api data.
     *
     * @param Channel $channel
     * @param array $data
     * @return Message
     */
    public function updateFromApi(Channel $channel, $data)
    {
        // does this message already exist?
        try {
            $message = $this->findUniqueMessage($channel, $data['ts']);
            $message->updateFromApiData($data);
        } catch (ServiceException $e) {
            // none found so create
            $message = new Message($channel, $data);
            if (array_key_exists('user', $data)) {
                $message->setUser($this->users->get($data['user']));
            }
            if (array_key_exists('inviter', $data)) {
                $message->setInviter($this->users->get($data['inviter']));
            }
            $this->repository->getDocumentManager()->persist($message);
        }
        // if we have reactions, also ensure user data is present
        $this->updateReactions($data, $message);
        return $message;
    }

    /**
     * @param Channel $channel
     * @param $ts
     * @return Message
     * @throws ServiceException
     */
    public function findUniqueMessage(Channel $channel, $ts)
    {
        /** @var Message $message */
        $message = $this->repository->findOneBy([
            'channel' => $channel,
            'ts' => $ts
        ]);
        if (is_null($message)) {
            throw new ServiceException(
                sprintf('No message found for channel %s and timestamp $%s', $channel->getName(), $ts)
            );
        }
        return $message;
    }

    private function updateReactions($data, Message $message)
    {
        // if no reactions then do nothing
        if (!array_key_exists('reactions', $data)) return;
        // otherwise process and update the message
        foreach ($data['reactions'] AS $reactionData) {
            $reaction = $message->getReactionByName($reactionData['name']);
            foreach ($reactionData['users'] AS $userId) {
                $reaction->addUser($this->users->get($userId));
            }
        }
    }

    /**
     * Method to retrieve messages from a channel since a defined date.
     *
     * @param Channel $channel
     * @param \DateTime $since
     * @return Message[]
     */
    public function findByChannelSince(Channel $channel, \DateTime $since)
    {
        $qb = $this->repository->createQueryBuilder()
            ->field('channel')->equals($channel)
            ->field('createdAt')->gte($since);
        $cursor = $qb->getQuery()->execute();
        $result = [];
        foreach ($cursor AS $message) {
            $result[] = $message;
        }
        return $result;
    }

    /**
     * @param User $user
     * @param \DateTime $since
     * @return Message[]
     */
    public function findByUserSince(User $user, \DateTime $since)
    {
        $qb = $this->repository->createQueryBuilder()
            ->field('user')->equals($user)
            ->field('createdAt')->gte($since);
        $cursor = $qb->getQuery()->execute();
        $result = [];
        foreach ($cursor AS $message) {
            $result[] = $message;
        }
        return $result;
    }
}