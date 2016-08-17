<?php
/**
 * Created by PhpStorm.
 * User: jontyb
 * Date: 17/08/2016
 * Time: 11:15
 */

namespace AppBundle\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Representation of a Slack message.
 *
 * @package AppBundle
 * @ODM\Document(collection="message",repositoryClass="AppBundle\Document\Repository\MessageRepository")
 */
class Message
{
    /**
     * @ODM\Id
     */
    protected $id;
    /**
     * @ODM\ReferenceOne(targetDocument="User")
     * @var Channel;
     */
    protected $channel;
    /**
     * @ODM\Field(type="string")
     * @var string
     */
    protected $type;
    /**
     * @ODM\Field(type="string", name="subtype")
     * @var string
     */
    protected $subType;
    /**
     * @ODM\ReferenceOne(targetDocument="User")
     * @var User;
     */
    protected $user;
    /**
     * @ODM\ReferenceOne(targetDocument="User")
     * @var User;
     */
    protected $inviter;
    /**
     * @ODM\Field(type="string")
     * @var string
     */
    protected $text;
    /**
     * @ODM\Field(type="string", name="ts")
     * @var string
     */
    protected $timestamp;
    /**
     * @ODM\Field(type="boolean", name="upload")
     * @var boolean
     */
    protected $isUpload = false;
    /**
     * @ODM\Field(type="boolean", name="is_bot")
     * @var boolean
     */
    protected $isBot = false;
    /**
     * @ODM\EmbedMany(targetDocument="Reaction")
     * @var ArrayCollection[Reaction];
     */
    protected $reactions;

    /**
     * Message constructor.
     * @param Channel $channel
     * @param array $data
     */
    public function __construct(Channel $channel, $data)
    {
        $this->channel = $channel;
        $this->reactions = new ArrayCollection();
        $this->updateFromApiData($data);
    }

    /**
     * Method to sync document with data form the API.
     *
     * @param array $data
     */
    public function updateFromApiData($data)
    {
        $this->type = $data['type'];
        $this->subType = $this->returnIfPresent($data, 'subtype');
        $this->timestamp = $data['ts'];
        $this->text = $data['text'];
        $this->isUpload = $this->returnIfPresent($data, 'upload');
        $this->isBot = $this->returnIfPresent($data, 'is_bot');

        // now do embedded reactions
        if (array_key_exists('reactions', $data) && is_array($data['reactions'])) {
            foreach ($data['reactions'] AS $reaction) {
                $reaction = new Reaction($reaction);
                $this->addReaction($reaction);
            }
        }
    }

    /**
     * Method to only assign data if present, could be nicer.
     *
     * @param array $data
     * @param string $key
     * @return mixed
     */
    private function returnIfPresent($data, $key)
    {
        if (array_key_exists($key, $data)) {
            return $data[$key];
        } else {
            return null;
        }
    }

    /**
     * Helper function to add unique reactions to the collection.
     *
     * @param Reaction $reaction
     */
    public function addReaction(Reaction $reaction)
    {
        if (!$this->reactions->contains($reaction)) {
            $this->reactions->add($reaction);
        }
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Channel
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getSubType()
    {
        return $this->subType;
    }

    /**
     * @param string $subType
     */
    public function setSubType($subType)
    {
        $this->subType = $subType;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getInviter()
    {
        return $this->inviter;
    }

    /**
     * @param User $inviter
     */
    public function setInviter($inviter)
    {
        $this->inviter = $inviter;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @return float
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param float $timestamp
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }

    /**
     * @return string
     */
    public function getIsUpload()
    {
        return $this->isUpload;
    }

    /**
     * @param string $isUpload
     */
    public function setIsUpload($isUpload)
    {
        $this->isUpload = $isUpload;
    }

    /**
     * @return ArrayCollection
     */
    public function getReactions()
    {
        return $this->reactions;
    }

    /**
     * @param ArrayCollection $reactions
     */
    public function setReactions($reactions)
    {
        $this->reactions = $reactions;
    }

    /**
     * @return boolean
     */
    public function isIsBot()
    {
        return $this->isBot;
    }

    /**
     * @param boolean $isBot
     */
    public function setIsBot($isBot)
    {
        $this->isBot = $isBot;
    }

}