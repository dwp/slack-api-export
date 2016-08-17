<?php

namespace AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Representation of a security token and associated data.
 *
 * @package AppBundle
 * @ODM\Document(collection="auth",repositoryClass="AppBundle\Document\Repository\AuthRepository")
 */
class Auth implements \JsonSerializable
{
    /**
     * @ODM\Id
     */
    protected $id;

    /**
     * @ODM\Field(type="string")
     */
    protected $url;

    /**
     * @ODM\Field(type="string", name="team_id")
     */
    protected $teamId;

    /**
     * @ODM\Field(type="string")
     */
    protected $team;

    /**
     * @ODM\Field(type="string", name="user_id")
     */
    protected $userId;

    /**
     * @ODM\Field(type="string")
     */
    protected $user;

    /**
     * @ODM\Field(type="string")
     */
    protected $token;

    /**
     * Auth constructor.
     * @param array $data
     */
    public function __construct($data, $token)
    {
        $this->updateFromApiData($data, $token);
    }

    /**
     * Method to sync document with data form the API.
     *
     * @param array $data
     * @param string $token
     */
    public function updateFromApiData(array $data, $token)
    {
        $this->url = $data['url'];
        $this->teamId = $data['team_id'];
        $this->team = $data['team'];
        $this->userId = $data['user_id'];
        $this->user = $data['user'];
        $this->token = $token;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return mixed
     */
    public function getTeamId()
    {
        return $this->teamId;
    }

    /**
     * @return mixed
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }


    /**
     * Set url
     *
     * @param string $url
     * @return $this
     */
    public function setUrl($url)
    {
        $this->url = $url;
        return $this;
    }

    /**
     * Set teamId
     *
     * @param string $teamId
     * @return $this
     */
    public function setTeamId($teamId)
    {
        $this->teamId = $teamId;
        return $this;
    }

    /**
     * Set team
     *
     * @param string $team
     * @return $this
     */
    public function setTeam($team)
    {
        $this->team = $team;
        return $this;
    }

    /**
     * Set userId
     *
     * @param string $userId
     * @return $this
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * Set user
     *
     * @param string $user
     * @return $this
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Set token
     *
     * @param string $token
     * @return $this
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
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
            'team_id' => $this->getTeamId(),
            'team'    => $this->getTeam(),
            'token'   => $this->getToken()
        ];
    }
}
