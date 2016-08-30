<?php
/**
 * Created by PhpStorm.
 * User: jontyb
 * Date: 30/08/2016
 * Time: 15:00
 */

namespace AppBundle\Slack\Event;

use AppBundle\Service\EventService;

/**
 * Class Message representing an actual message in channel.
 *
 * @package AppBundle\Slack\Event
 */
class Message extends AbstractMessage implements EventInterface
{
    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    function jsonSerialize()
    {
        return [
            'type' => 'message'
        ];
    }
}