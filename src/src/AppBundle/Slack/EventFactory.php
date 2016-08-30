<?php

namespace AppBundle\Slack;

use AppBundle\Slack\Event\EventInterface;

/**
 * Class EventFactory which allows us to create event objects
 *
 * @package AppBundle\Slack
 */
class EventFactory
{
    const EVENT_NAMESPACE_PREFIX = 'AppBundle\\Slack\\Event';

    /**
     * @param $data decoded json data from Slack
     * @return EventInterface
     * @throws \InvalidArgumentException
     */
    static public function factory($data)
    {
        // check if we have a valid event
        $type = self::parseMessageType($data);
        $name = self::normalizeEventName($type);
        $class = self::EVENT_NAMESPACE_PREFIX . '\\' . $name;
        if(!class_exists($class)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Unknown event type of %s supplied to the EventFactory resulting in unknown class %s.',
                    $type,
                    $class
                )
            );
        }
        // ok so init and return
        return new $class($data);
    }

    /**
     * Method to normalise a class name.
     *
     * @param string $name
     * @return string
     */
    static public function normalizeEventName($name)
    {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $name)));
    }

    /**
     * Message type parser which deals with the double "type" formatting used by Slack events.
     *
     * @param $data
     * @return string
     * @throws \InvalidArgumentException
     */
    static private function parseMessageType($data)
    {
        // need to parse if this is a "special" event
        switch($data->type)
        {
            case 'event_callback':
                return (string) $data->event->type;
            case 'url_verification':
                return (string) $data->type;
            default:
                throw new \InvalidArgumentException(sprintf('Unknown envelope type of %s.', $data->type));
        }
    }
}