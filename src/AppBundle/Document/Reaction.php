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
     *
     * @var array
     */
    protected $name;
    protected $count;
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
}