<?php

namespace AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Representation of a Slack user.
 *
 * @package AppBundle
 * @ODM\Document(collection="user",repositoryClass="AppBundle\Document\Repository\UserRepository")
 */
class User
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
     * @ODM\Field(type="string")
     * @var string
     */
    protected $name;
    /**
     * @ODM\Field(type="boolean")
     * @var string
     */
    protected $deleted;
    /**
     * @ODM\Field(type="string")
     * @var string
     */
    protected $color;
    /**
     * @ODM\Field(type="string", name="first_name")
     * @var string
     */
    protected $firstName;
    /**
     * @ODM\Field(type="string", name="last_name")
     * @var string
     */
    protected $lastName;
    /**
     * @ODM\Field(type="string", name="real_name")
     * @var string
     */
    protected $realName;
    /**
     * @ODM\Field(type="string")
     * @var string
     */
    protected $email;
    /**
     * @ODM\Field(type="string")
     * @var string
     */
    protected $skype;
    /**
     * @ODM\Field(type="string")
     * @var string
     */
    protected $phone;
    /**
     * @ODM\Field(type="boolean", name="is_admin")
     * @var string
     */
    protected $isAdmin;
    /**
     * @ODM\Field(type="boolean", name="is_owner")
     * @var string
     */
    protected $isOwner;
    /**
     * @ODM\Field(type="boolean", name="has_2fa")
     * @var string
     */
    protected $has2fa;
    /**
     * @ODM\Field(type="boolean", name="is_bot")
     * @var string
     */
    protected $isBot;

    /**
     * User constructor to setup the object
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->updateFromApiData($data);
    }

    /**
     * Method to sync with API data
     *
     * @param array $data
     */
    public function updateFromApiData(array $data)
    {
        # always present
        $this->name = $data['name'];
        $this->deleted = (bool) $data['deleted'];

        # optional fields
        $this->color = $this->returnIfPresent($data, 'color');
        $this->email = strtolower($this->returnIfPresent($data['profile'], 'email'));
        $this->firstName = $this->returnIfPresent($data['profile'], 'first_name');
        $this->lastName = $this->returnIfPresent($data['profile'], 'last_name');
        $this->realName = $this->returnIfPresent($data['profile'], 'real_name');
        $this->skype = $this->returnIfPresent($data['profile'], 'skype');
        $this->phone = $this->returnIfPresent($data['profile'], 'phone');
        $this->isAdmin = (bool) $this->returnIfPresent($data, 'is_admin');
        $this->isOwner = (bool) $this->returnIfPresent($data, 'is_owner');
        $this->has2fa = (bool) $this->returnIfPresent($data, 'has_2fa');
        $this->isBot = (bool) $this->returnIfPresent($data, 'is_bot');
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
     * @return string
     */
    public function getId()
    {
        return $this->id;
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
     * @return string
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param string $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param string $color
     */
    public function setColor($color)
    {
        $this->color = $color;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getRealName()
    {
        return $this->realName;
    }

    /**
     * @param string $realName
     */
    public function setRealName($realName)
    {
        $this->realName = $realName;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getSkype()
    {
        return $this->skype;
    }

    /**
     * @param string $skype
     */
    public function setSkype($skype)
    {
        $this->skype = $skype;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getIsAdmin()
    {
        return $this->isAdmin;
    }

    /**
     * @param string $isAdmin
     */
    public function setIsAdmin($isAdmin)
    {
        $this->isAdmin = $isAdmin;
    }

    /**
     * @return string
     */
    public function getIsOwner()
    {
        return $this->isOwner;
    }

    /**
     * @param string $isOwner
     */
    public function setIsOwner($isOwner)
    {
        $this->isOwner = $isOwner;
    }

    /**
     * @return string
     */
    public function getHas2fa()
    {
        return $this->has2fa;
    }

    /**
     * @param string $has2fa
     */
    public function setHas2fa($has2fa)
    {
        $this->has2fa = $has2fa;
    }

    /**
     * @return mixed
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * @param mixed $team
     */
    public function setTeam($team)
    {
        $this->team = $team;
    }

    /**
     * @return string
     */
    public function getIsBot()
    {
        return $this->isBot;
    }

    /**
     * @param string $isBot
     */
    public function setIsBot($isBot)
    {
        $this->isBot = $isBot;
    }

}