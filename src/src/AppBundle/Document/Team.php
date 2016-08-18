<?php

namespace AppBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Representation of a Slack team.
 *
 * @package AppBundle
 * @ODM\Document(collection="team",repositoryClass="AppBundle\Document\Repository\TeamRepository")
 */
class Team
{
    /**
     * @ODM\Id(strategy="NONE")
     * @var string
     */
    protected $id;

    /**
     * @ODM\Field(type="string")
     * @var string
     */
    protected $name;

    /**
     * @ODM\Field(type="string")
     * @var string
     */
    protected $domain;

    /**
     * @ODM\Field(type="string", name="email_domain")
     * @var string
     */
    protected $emailDomain;

    /**
     * @ODM\ReferenceOne(targetDocument="Auth")
     * @var Auth
     */
    protected $auth;

    /**
     * Team constructor.
     *
     * @param array $data
     * @param Auth $auth
     */
    public function __construct(array $data, Auth $auth = null)
    {
        $this->id = $data['id'];
        $this->updateFromApiData($data);
        $this->auth = $auth;
    }

    /**
     * Method to sync document with data form the API.
     *
     * @param array $data
     */
    public function updateFromApiData(array $data)
    {
        $this->name = $data['name'];
        $this->domain = $data['domain'];
        $this->emailDomain = $data['email_domain'];
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
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * @param string $domain
     */
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    /**
     * @return string
     */
    public function getEmailDomain()
    {
        return $this->emailDomain;
    }

    /**
     * @param string $emailDomain
     */
    public function setEmailDomain($emailDomain)
    {
        $this->emailDomain = $emailDomain;
    }

    /**
     * @return Auth
     */
    public function getAuth()
    {
        return $this->auth;
    }

    /**
     * @param Auth $auth
     */
    public function setAuth(Auth $auth = null)
    {
        $this->auth = $auth;
    }

}
