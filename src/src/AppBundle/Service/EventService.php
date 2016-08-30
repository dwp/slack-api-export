<?php

namespace AppBundle\Service;

use AppBundle\Document\Channel;
use AppBundle\Document\Message;
use AppBundle\Document\Team;
use AppBundle\Document\Repository\ChannelRepository;
use AppBundle\Slack\Event\EventInterface;

/**
 * Basic service for interacting with Channels.
 */
class EventService
{

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
     * Slack constructor to setup the service.
     *
     * @param UserService $users
     * @param ChannelService $channels
     * @param MessageService $messages
     */
    public function __construct(UserService $users, ChannelService $channels, MessageService $messages)
    {
        $this->userService = $users;
        $this->channelService = $channels;
        $this->messageService = $messages;
    }

    /**
     * Primary method to process an event.
     * @param EventInterface $event
     */
    public function process(EventInterface $event)
    {

    }

}