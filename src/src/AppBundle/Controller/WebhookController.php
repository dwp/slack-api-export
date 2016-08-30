<?php

namespace AppBundle\Controller;

use AdamPaterson\OAuth2\Client\Provider\Slack AS SlackOauthProvider;
use AppBundle\Document\Auth;
use AppBundle\Service\AuthService;
use AppBundle\Service\SlackClient;
use AppBundle\Slack\EventFactory;
use CL\Slack\Payload\AuthTestPayload;
use Monolog\Logger;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class WebhookController extends Controller
{
    /**
     * @Route("/webhook", name="app.webhook")
     */
    public function indexAction(Request $request)
    {
        // validate the request - always have a token
        $data = json_decode($request->getContent());
        $event = EventFactory::factory($data);

        // return our response from the message
        return new JsonResponse($event);
    }
}