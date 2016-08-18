<?php

namespace AppBundle\Service;

use AppBundle\Document\Auth;
use AppBundle\Document\Team;
use AppBundle\Document\Repository\AuthRepository;

/**
 * Basic service for authenticating with Slack
 */
class AuthService
{
    /**
     * @var SlackClient
     */
    protected $slack;

    /**
     * @var AuthRepository
     */
    protected $repository;

    /**
     * @var TeamService
     */
    protected $teamService;

    /**
     * Auth constructor.
     *
     * @param SlackClient $slack
     * @param AuthRepository $repository
     * @param TeamService $teamService
     */
    public function __construct(
        SlackClient $slack,
        AuthRepository $repository,
        TeamService $teamService
    )
    {
        $this->slack = $slack;
        $this->repository = $repository;
        $this->teamService = $teamService;
    }

    /**
     * Method to make an inital call to the slack API to setup and store the token for future use.
     *
     * @return Auth
     */
    public function save()
    {
        // hit the test endpoint
        $authData = $this->slack->get('/auth.test');
        /** @var Auth $auth */
        $auth = $this->repository->findOneByTeamId($authData['team_id']);
        if (is_null($auth)) {
            $auth = new Auth($authData, $this->slack->getToken());
            $this->repository->getDocumentManager()->persist($auth);
        } else {
            $auth->updateFromApiData($authData, $this->slack->getToken());
        }
        $this->updateTeam($auth);
        $this->repository->getDocumentManager()->flush();
        return $auth;
    }

    /**
     * Return a team after checking with the API that it is up to date.
     *
     * @param Auth $auth
     * @return Team
     */
    public function updateTeam(Auth $auth)
    {
        $teamData = $this->slack->get('/team.info')['team'];
        $team = $this->teamService->updateFromApi($teamData);
        $team->setAuth($auth);
        return $team;
    }
}