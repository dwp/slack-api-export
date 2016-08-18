<?php

namespace AppBundle\Service;

use AppBundle\Document\Team;
use AppBundle\Document\Repository\TeamRepository;

/**
 * Basic service for interacting with Teams.
 */
class TeamService
{
    /**
     * @var SlackClient
     */
    protected $slack;

    /**
     * @var TeamRepository
     */
    protected $repository;

    /**
     * Slack constructor to setup the service.
     *
     * @param SlackClient $slack
     * @param TeamRepository $repository
     */
    public function __construct(SlackClient $slack, TeamRepository $repository)
    {
        $this->slack = $slack;
        $this->repository = $repository;
    }

    /**
     * List all teams in mongodb.
     */
    public function findAll()
    {
        return $this->repository->findAll();
    }

    /**
     * Method to sync a user with the slack api data.
     *
     * @param array $data
     * @return Team
     */
    public function updateFromApi($data)
    {
        // check for existing user
        /** @var Team $team */
        $team = $this->repository->find($data['id']);
        if (is_null($team)) {
            // none found so create
            $team = new Team($data);
            $this->repository->getDocumentManager()->persist($team);
        } else {
            // otherwise just update
            $team->updateFromApiData($data);
        }
        return $team;
    }

}