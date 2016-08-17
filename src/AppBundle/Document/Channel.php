<?php

namespace AppBundle\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Representation of a Slack channel.
 *
 * @package AppBundle
 * @ODM\Document(collection="channel",repositoryClass="AppBundle\Document\Repository\ChannelRepository")
 */
class Channel
{
    /**
     * @ODM\Id(strategy="NONE")
     * @var string
     */
    protected $id;
    /**
     * @ODM\ReferenceOne(targetDocument="Team")
     * @var Team
     */
    protected $team;
    /**
     * @ODM\ReferenceMany(targetDocument="User")
     * @var ArrayCollection;
     */
    protected $members;
    /**
     * @ODM\Field(type="string")
     * @var string
     */
    protected $name;
    /**
     * @ODM\Field(type="boolean", name="is_channel")
     * @var boolean
     */
    protected $isChannel;
    /**
     * @ODM\ReferenceOne(targetDocument="User")
     * @var User
     */
    protected $creator;
    /**
     * @ODM\Field(type="boolean", name="is_archived")
     * @var boolean
     */
    protected $isArchived;
    /**
     * @ODM\Field(type="boolean", name="is_general")
     * @var boolean
     */
    protected $isGeneral;
    /**
     * @ODM\Field(type="boolean", name="is_member")
     * @var boolean
     */
    protected $isMember;
    /**
     * @ODM\Field(type="string")
     * @var string
     */
    protected $topic;
    /**
     * @ODM\Field(type="string")
     * @var string
     */
    protected $purpose;
    /**
     * @ODM\Field(type="int", name="num_members")
     * @var integer
     */
    protected $numMebers = 0;
    /**
     * @ODM\Field(type="date", name="created_at")
     * @var DateTime
     */
    protected $createdAt;

    /**
     * Channel constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->updateFromApiData($data);
        $this->members = new ArrayCollection;
    }

    /**
     * Method to sync document with data form the API.
     *
     * @param array $data
     */
    public function updateFromApiData(array $data)
    {
        $this->name = $data['name'];
        $this->isChannel = $data['is_channel'];
        $this->isArchived = $data['is_archived'];
        $this->isGeneral = $data['is_general'];
        $this->isMember = $data['is_member'];
        $this->numMebers = $data['num_members'];
        $this->createdAt = new \DateTime("@" . $data['created']);
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Team
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * @param Team $team
     */
    public function setTeam($team)
    {
        $this->team = $team;
    }

    /**
     * @return ArrayCollection
     */
    public function getMembers()
    {
        return $this->members;
    }

    /**
     * @param User $user
     */
    public function addMember(User $user)
    {
        // add if not present
        $this->members->add($user);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return boolean
     */
    public function isIsChannel()
    {
        return $this->isChannel;
    }

    /**
     * @param boolean $isChannel
     */
    public function setIsChannel($isChannel)
    {
        $this->isChannel = $isChannel;
    }

    /**
     * @return User
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * @param User $creator
     */
    public function setCreator($creator)
    {
        $this->creator = $creator;
    }

    /**
     * @return boolean
     */
    public function isIsArchived()
    {
        return $this->isArchived;
    }

    /**
     * @param boolean $isArchived
     */
    public function setIsArchived($isArchived)
    {
        $this->isArchived = $isArchived;
    }

    /**
     * @return boolean
     */
    public function isIsGeneral()
    {
        return $this->isGeneral;
    }

    /**
     * @param boolean $isGeneral
     */
    public function setIsGeneral($isGeneral)
    {
        $this->isGeneral = $isGeneral;
    }

    /**
     * @return boolean
     */
    public function isIsMember()
    {
        return $this->isMember;
    }

    /**
     * @param boolean $isMember
     */
    public function setIsMember($isMember)
    {
        $this->isMember = $isMember;
    }

    /**
     * @return string
     */
    public function getTopic()
    {
        return $this->topic;
    }

    /**
     * @param string $topic
     */
    public function setTopic($topic)
    {
        $this->topic = $topic;
    }

    /**
     * @return string
     */
    public function getPurpose()
    {
        return $this->purpose;
    }

    /**
     * @param string $purpose
     */
    public function setPurpose($purpose)
    {
        $this->purpose = $purpose;
    }

    /**
     * @return int
     */
    public function getNumMebers()
    {
        return $this->numMebers;
    }

    /**
     * @param int $numMebers
     */
    public function setNumMebers($numMebers)
    {
        $this->numMebers = $numMebers;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

}