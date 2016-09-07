<?php

namespace AppBundle\EventListener;

use AppBundle\Document\Message;
use Doctrine\Common\EventSubscriber;
use Doctrine\ODM\MongoDB\Event\LifecycleEventArgs;

/**
 * Subscriber which listens to the Doctrine postPersist event for new Message Documents.
 *
 * @package AppBundle\EventListener
 */
class NewMessageSubscriber implements EventSubscriber
{
    /**
     * @var string
     */
    protected $savePath;

    /**
     * NewMessageSubscriber constructor to inject our configuration information.
     * @param string $savePath
     */
    public function __construct($savePath)
    {
        // ensure directory is present
        $this->savePath = realpath($savePath);
        if (!is_dir($this->savePath)) {
            mkdir($this->savePath, 0777, true);
        }
    }

    /**
     * Handler for our postPersist events.
     *
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        // only procced if we have a new message
        $document = $args->getDocument();
        if (!$document instanceof Message) return;
        // just return if the user is a  bot
        if ($document->getUser()->getIsBot()) return;

        // need to emit the event - writing to filesystem at the moment - one log per day with one entry per line
        $data = $document->eventArray();
        $handle = fopen($this->savePath . "/" . $document->getTimestampDateTime()->format('Y-m-d') . ".log", "a");
        fwrite($handle, json_encode($data) . PHP_EOL );
        fclose($handle);
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            'postPersist'
        ];
    }
}