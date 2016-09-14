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
        $this->savePath = $savePath;
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

        // now push to our storage systems
        $this->logToDisk($document);

    }

    private function logToDisk(Message $message)
    {
        // ensure that the team log directory is present
        if (!is_dir($this->getSavePath($message))) {
            mkdir($this->getSavePath($message), 0777, true);
        }

        // need to emit the event - writing to filesystem at the moment - one log per day with one entry per line
        $handle = fopen(
            $this->getSavePath($message) . "/" . $message->getTimestampDateTime()->format('Y-m-d') . ".log",
            "a"
        );
        fwrite($handle, json_encode($message->eventArray()) . PHP_EOL );
        fclose($handle);
    }

    /**
     * Helper to get team save path.
     *
     * @param Message $message
     * @return string
     */
    private function getSavePath(Message $message)
    {
        return $this->savePath . DIRECTORY_SEPARATOR . $message->getUser()->getTeam()->getDomain();
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