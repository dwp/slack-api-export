<?php
/**
 * Created by PhpStorm.
 * User: jontyb
 * Date: 30/08/2016
 * Time: 14:25
 */

namespace AppBundle\Slack\Event;

use AppBundle\Service\EventService;

interface EventInterface extends \JsonSerializable
{
    /**
     * Returns a standard object representing our event data.
     *
     * @return object
     */
    public function getData();
}