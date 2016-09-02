<?php

namespace AppBundle\Service;

use AppBundle\Document\Channel;
use AppBundle\Document\Message;
use AppBundle\Document\Team;
use AppBundle\Document\Repository\ChannelRepository;
use AppBundle\Slack\Event\EventEntityMappingException;
use AppBundle\Slack\Event\EventInterface;
use Doctrine\ODM\MongoDB\DocumentManager;
use Psr\Log\LoggerInterface;

/**
 * Basic service for interacting with Channels.
 */
class EventService
{

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @var ChannelService
     */
    protected $channelService;

    /**
     * @var MessageService
     */
    protected $messageService;

    /**
     * @var DocumentManager
     */
    protected $documentManager;

    /**
     * Slack constructor to setup the service.
     *
     * @param UserService $users
     * @param ChannelService $channels
     * @param MessageService $messages
     * @param DocumentManager $documentManager
     */
    public function __construct(
        LoggerInterface $logger,
        UserService $users,
        ChannelService $channels,
        MessageService $messages,
        DocumentManager $documentManager
    )
    {
        $this->logger = $logger;
        $this->userService = $users;
        $this->channelService = $channels;
        $this->messageService = $messages;
        $this->documentManager = $documentManager;
    }

    /**
     * Primary method to process an event.
     * @param EventInterface $event
     */
    public function process(EventInterface $event)
    {
        if ($event instanceof \AppBundle\Slack\Event\Message) {
            $channel = $this->channelService->get($event->getData()['event']['channel']);
            $message = $this->messageService->updateFromApi($channel, $event->getData()['event']);
            $this->documentManager->flush($message);
            $this->logger->info(sprintf("Message logged %s from Event API.", $message->getId()));
        }
    }

}