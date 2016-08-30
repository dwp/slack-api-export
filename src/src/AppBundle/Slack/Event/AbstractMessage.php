<?php
/**
 * Created by PhpStorm.
 * User: jontyb
 * Date: 30/08/2016
 * Time: 15:00
 */

namespace AppBundle\Slack\Event;

/**
 * Class AbstractMessage for storing all event data.
 *
 * @package AppBundle\Slack\Event
 */
abstract class AbstractMessage
{
    /**
     * @var string
     */
    protected $data;

    /**
     * AbstractMessage constructor to store the message data
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * General accessor for message data.
     *
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }
}