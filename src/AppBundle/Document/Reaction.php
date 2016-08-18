<?php

namespace AppBundle\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;


/**
 * Representation of a Slack message reaction.
 *
 * @package AppBundle
 * @ODM\EmbeddedDocument()
 */
class Reaction
{
    /**
     * @ODM\Field(type="string")
     * @var string
     */
    protected $name;
    /**
     * @ODM\Field(type="integer")
     * @var int
     */
    protected $count;
    /**
     * @ODM\ReferenceMany(targetDocument="User")
     * @var ArrayCollection;
     */
    protected $users;

    /**
     * Reaction constructor.
     *
     * @param $data
     */
    public function __construct($data)
    {
        $this->name = $data['name'];
        $this->count = $data['count'];
        $this->users = new ArrayCollection();
    }

    /**
     * Add a user to the user collection if they are not already present.
     *
     * @param User $user
     */
    public function addUser(User $user)
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @param int $count
     */
    public function setCount($count)
    {
        $this->count = $count;
    }

    /**
     * @return ArrayCollection
     */
    public function getUsers()
    {
        return $this->users;
    }
}