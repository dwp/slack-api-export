<?php

namespace AppBundle\Controller;

use AppBundle\Service\EventService;
use AppBundle\Service\SlackClient;
use AppBundle\Service\TeamService;
use AppBundle\Slack\EventFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class WebhookController extends Controller
{
    /**
     * @Route("/webhook", name="app.webhook")
     * @Method({"POST"})
     */
    public function indexAction(Request $request)
    {
        // prepare our data
        $data = json_decode($request->getContent(), true);
        if (empty($data)) {
            throw new BadRequestHttpException("Invalid request format - no JSON found.");
        }

        // validate request token and then ensure our event can be created
        if (getenv('SLACK_TOKEN') !== $data['token']) {
            throw new BadRequestHttpException('Invalid token.');
        }

        // ensure valid team is injected if present
        if (array_key_exists('team_id', $data)) {
            $team = $this->getTeamService()->get($data['team_id']);
            $this->getSlackClient()->setToken($team);
        }

        // and proces the event
        $event = EventFactory::factory($data);
        $this->getEventService()->process($event);

        // return our response from the message
        return new JsonResponse($event);
    }

    /**
     * Helper function to type hint the container.
     * @return EventService
     */
    private function getEventService()
    {
        return $this->get('app.service.event');
    }

    /**
     * Helper function to type hint the container.
     * @return TeamService
     */
    private function getTeamService()
    {
        return $this->get('app.service.team');
    }
    /**
     * Helper function to type hint the container.
     * @return SlackClient
     */
    private function getSlackClient()
    {
        return $this->get('app.service.slack_client');
    }
}