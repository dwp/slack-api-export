<?php

namespace AppBundle\Service;

use AppBundle\Document\Team;
use AppBundle\Document\User;
use AppBundle\Document\Repository\UserRepository;
use AppBundle\Service\Exception\ServiceException;

/**
 * Basic service for interacting with users.
 */
class UserService
{
    /**
     * @var SlackClient
     */
    protected $slack;

    /**
     * @var UserRepository
     */
    protected $repository;

    /**
     * Slack constructor to setup the service.
     *
     * @param SlackClient $slack
     * @param UserRepository $userRepository
     */
    public function __construct(
        SlackClient $slack,
        UserRepository $userRepository
    )
    {
        $this->slack = $slack;
        $this->repository = $userRepository;
    }

    /**
     * Proxy method to sync all users in a team with the remote API.
     *
     * @param Team $team
     * @return User[]
     */
    public function syncTeam(Team $team)
    {
        $this->slack->setToken($team);
        $members = $this->slack->get('/users.list')['members'];
        $users = [];
        foreach ($members AS $userData) {
            $user = $this->updateFromApi($userData);
            $user->setTeam($team);
            $users[] = $user;
        }
        $this->repository->getDocumentManager()->flush();
        return $users;
    }

    /**
     * Method to sync a user with the slack api data.
     *
     * @param array $data
     * @return User
     */
    public function updateFromApi($data)
    {
        // check for existing user
        /** @var User $user */
        $user = $this->repository->find($data['id']);
        if (is_null($user)) {
            // none found so create
            $user = new User($data);
            $this->repository->getDocumentManager()->persist($user);
        } else {
            // otherwise just update
            $user->updateFromApiData($data);
        }
        return $user;
    }

    /**
     * Method to retrieve a user from mongodb.
     *
     * @param $userId
     * @return User
     * @throws ServiceException
     */
    public function get($userId)
    {
        $user = $this->repository->find($userId);
        if (is_null($user)) {
            throw new ServiceException(sprintf('Unable to locate user %s.', $userId));
        }
        return $user;
    }
}