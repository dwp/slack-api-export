<?php

namespace AppBundle\Controller;

use AppBundle\Service\EventService;
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
}