<?php

namespace AppBundle\Service;

use AppBundle\Document\Channel;
use AppBundle\Document\Message;
use AppBundle\Document\Team;
use AppBundle\Document\Repository\ChannelRepository;
use AppBundle\Service\Exception\ServiceException;

/**
 * Basic service for interacting with Channels.
 */
class ChannelService
{
    /**
     * @var SlackClient
     */
    protected $slack;

    /**
     * @var ChannelRepository
     */
    protected $repository;

    /**
     * @var MessageService
     */
    protected $messages;

    /**
     * Slack constructor to setup the service.
     *
     * @param SlackClient $slack
     * @param ChannelRepository $repository
     * @param MessageService $messages
     */
    public function __construct(SlackClient $slack, ChannelRepository $repository, MessageService $messages)
    {
        $this->slack = $slack;
        $this->repository = $repository;
        $this->messages = $messages;
    }

    /**
     * Method to sync a user with the slack api data.
     *
     * @param array $data
     * @return Channel
     */
    public function updateFromApi($data)
    {
        // check for existing user
        /** @var Channel $channel */
        $channel = $this->repository->find($data['id']);
        if (is_null($channel)) {
            // none found so create
            $channel = new Channel($data);
            $this->repository->getDocumentManager()->persist($channel);
        } else {
            // otherwise just update
            $channel->updateFromApiData($data);
        }
        // now associate members correctly
        foreach ($data['members'] AS $memberId) {
            $user = $this->repository->getDocumentManager()->getReference('AppBundle:User', $memberId);
            if (!$channel->getMembers()->contains($user)) {
                $channel->addMember($user);
            }
        }
        return $channel;
    }


    /**
     * Proxy method to sync all channels in a team with the remote API.
     *
     * @param Team $team
     * @return Channel[]
     */
    public function syncTeam(Team $team)
    {
        $this->slack->setToken($team);
        $rawData = $this->slack->get('/channels.list')['channels'];
        $channels = [];
        foreach ($rawData AS $channelData) {
            $channel = $this->updateFromApi($channelData);
            $channel->setTeam($team);
            $channels[] = $channel;
        }
        $this->repository->getDocumentManager()->flush();
        return $channels;
    }

    /**
     * Method to sync historical messages for a channel
     *
     * @param Channel $channel
     * @param mixed $latest
     * @param mixed $oldest
     */
    public function syncHistory(Channel $channel, $latest = null, $oldest = 0)
    {
        $this->slack->setToken($channel->getTeam());
        $messages = $this->slack->get(
            '/channels.history',
            [
                'query' => [
                    'channel' => $channel->getId(),
                    'latest' => $latest,
                    'oldest' => $oldest
                ]
            ]
        );

        // store timestamp of the first message, if it is earlier than the current value
        if (count(array_values($messages['messages'])) > 0) {
            $lastMessageDateTime = new \DateTime("@" . intval(array_values($messages['messages'])[0]['ts']));
            if (
                (
                    $channel->getLastMessage() instanceof \DateTime &&
                    $channel->getLastMessage() < $lastMessageDateTime
                ) ||
                is_null($channel->getLastMessage())
            ) {
                $channel->setLastMessage($lastMessageDateTime);
            }
        }

        // try and find the message based on timestamp and user
        foreach ($messages['messages'] AS $messageData) {
            $message = $this->messages->updateFromApi($channel, $messageData);
        }
        $this->repository->getDocumentManager()->flush();

        // if we have more then make a recursive query
        if (true === (bool) $messages['has_more']) {
           $this->syncHistory($channel, $message->getTimestamp(), $oldest);
        }
    }

    /**
     * Method to lookup a channel in the local database.
     *
     * @param string $id
     * @return Channel
     * @throws ServiceException
     */
    public function get($id)
    {
        $channel = $this->repository->find($id);
        if (empty($channel)) {
            throw new ServiceException('Unknown channel with id of %s.', $id);
        }
        return $channel;
    }
}