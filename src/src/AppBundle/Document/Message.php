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
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Representation of a Slack message.
 *
 * @package AppBundle
 * @ODM\Document(collection="message",repositoryClass="AppBundle\Document\Repository\MessageRepository")
 */
class Message implements \JsonSerializable
{
    /**
     * @ODM\Id
     */
    protected $id;
    /**
     * @ODM\ReferenceOne(targetDocument="Channel")
     * @var Channel;
     */
    protected $channel;
    /**
     * @ODM\ReferenceOne(targetDocument="User", inversedBy="messages")
     * @var User;
     */
    protected $user;
    /**
     * @ODM\EmbedMany(targetDocument="Reaction")
     * @var ArrayCollection[Reaction];
     */
    protected $reactions;
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
     * @ODM\Field(type="date", name="created_at")
     * @var DateTime
     */
    protected $createdAt;
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
        $this->createdAt = new \DateTime("@" . intval($data['ts']));
        $this->text = $data['text'];
        $this->isUpload = $this->returnIfPresent($data, 'upload');
        $this->isBot = $this->returnIfPresent($data, 'is_bot');

        // now do embedded reactions
        if (array_key_exists('reactions', $data) && is_array($data['reactions'])) {
            foreach ($data['reactions'] AS $reactionData) {
                // do we have a dupe
                $reaction = $this->getReactionByName($reactionData['name']);
                if (is_null($reaction)) {
                    $reaction = new Reaction($reactionData);
                    $this->addReaction($reaction);
                } else {
                    $reaction->setCount($reactionData['count']);
                }
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
     * Method to fetch a unique reaction.
     *
     * @param $name
     * @return Reaction|null
     */
    public function getReactionByName($name)
    {
        /** @var Reaction $reaction */
        foreach ($this->reactions AS $reaction) {
            if ($reaction->getName() === $name) {
                return $reaction;
            }
        }
        return null;
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
     * @return \DateTime
     */
    public function getTimestampDateTime()
    {
        return new \DateTime("@" . intval($this->getTimestamp()));
    }

    /**
     * @param float $timestamp
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
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
    public function setCreatedAt($createdAt = null)
    {
        $this->createdAt = $createdAt;
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
     * @return Reaction[]
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
    public function getIsBot()
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

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            'id' => (string) $this->getId(),
            'timestamp' => (string) $this->getTimestampDateTime()->format(\DateTime::ISO8601),
            'type' => (string) $this->getType(),
            'sub_type' => $this->getSubType(),
            'channel' => (string) $this->getChannel()->getName(),
            'text' => (string) $this->getText(),
            'reactions' => (array) $this->getReactions()->toArray(),
            'is_bot' => (bool) $this->getIsBot(),
        ];
    }

    /**
     * Method to get custom format required for event emission - need to flatten to a single object, not the best to
     * have two methods which do a similar thing... hay ho...
     *
     * @return array
     */
    public function eventArray()
    {
        return [
            'id' => (string) $this->getId(),
            'channel' => (string) $this->getChannel()->getName(),
            'user' => [
                'id' => (string) $this->getUser()->getId(),
                'name' => (string) $this->getUser()->getName()
            ],
            'type' => (string) $this->getType(),
            'sub_type' => $this->getSubType(),
            'text' => (string) $this->getText(),
            'timestamp' => (string) $this->getTimestampDateTime()->format(\DateTime::ISO8601),
        ];
    }
}