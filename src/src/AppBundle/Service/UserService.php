<?php

namespace AppBundle\Service;

use AppBundle\Document\Team;
use AppBundle\Document\User;
use AppBundle\Document\Repository\UserRepository;
use AppBundle\Service\Exception\ServiceException;
use Psr\Log\LoggerInterface;

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
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Slack constructor to setup the service.
     *
     * @param SlackClient $slack
     * @param UserRepository $userRepository
     */
    public function __construct(
        SlackClient $slack,
        UserRepository $userRepository,
        LoggerInterface $logger
    )
    {
        $this->slack = $slack;
        $this->repository = $userRepository;
        $this->logger = $logger;
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
     * Method to retrieve a user from mongodb, will populate from the API if possible.
     *
     * @param $id
     * @return User
     * @throws ServiceException
     */
    public function get($id)
    {
        $user = $this->repository->find($id);
        if (is_null($user)) {
            // try and populate
            try {
                $user = $this->populate($id);
            } catch (\Exception $e) {
                throw new ServiceException(sprintf('Unable to locate user for %s.', $id), 0, $e);
            }
        }
        return $user;
    }

    /**
     * Method to call the Slack API for user credentials, and then populate the Document.  Have to assume that the
     * applications is in the correct slack api context and has been pre-configured with the correct OAuth token.
     *
     * @param $id
     * @return User
     * @throws ServiceException
     */
    public function populate($id)
    {
        $response = $this->slack->get(
            '/users.info',
            [
                'query' => [
                    'user' => $id
                ]
            ]
        );
        $this->logger->info('Retrieving users.info from Slack API.', $response);
        return $this->updateFromApi($response['user']);
    }
}