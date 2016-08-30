<?php
/**
 * Created by PhpStorm.
 * User: jontyb
 * Date: 30/08/2016
 * Time: 15:00
 */

namespace AppBundle\Slack\Event;


use AppBundle\Service\EventService;

class UrlVerification implements EventInterface
{
    /**
     * @var string
     */
    protected $challenge;

    public function __construct($data)
    {
        $this->challenge = $data->challenge;
        $this->token = $data->token;
    }

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
              'challenge' => $this->challenge
        ];
    }
}