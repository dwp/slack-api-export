<?php

namespace AppBundle\Controller;

use AdamPaterson\OAuth2\Client\Provider\Slack AS SlackOauthProvider;
use AppBundle\Document\Auth;
use AppBundle\Service\AuthService;
use AppBundle\Service\SlackClient;
use CL\Slack\Payload\AuthTestPayload;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class OauthController extends Controller
{
    /**
     * @Route("/oauth", name="oauth")
     */
    public function indexAction(Request $request)
    {
        $provider = new SlackOauthProvider([
            'clientId'     => getenv('SLACK_CLIENT_ID'),
            'clientSecret' => getenv('SLACK_CLIENT_SECRET'),
            'redirectUri'  => getenv('SLACK_CLIENT_REDIRECT_URI')
        ]);

        // If we don't have an authorization code then get one
        if (false == $request->query->has('code')) {

            // we have no code, so lets start the oauth handshake
            // do it early to force the configuration of state etc
            $authUrl = $provider->getAuthorizationUrl([
                'scope' => [
                    'channels:history',
                    'channels:read',
                    'dnd:read',
                    'groups:history',
                    'groups:read',
                    'im:history',
                    'im:read',
                    'mpim:history',
                    'mpim:read',
                    'team:read',
                    'usergroups:read',
                    'users.profile:read',
                    'users:read'
                ]
            ]);

            $this->getSession()->set('oauth2state', $provider->getState());
            return $this->redirect($authUrl);

        } elseif (
            false == $request->query->has('state') ||
            $this->getSession()->get('oauth2state') !== $request->query->get('state')
        ) {

            // invalid session so destroy the session and throw
            $this->getSession()->clear();
            throw new BadRequestHttpException('Invalid state');

        } else {

            // good oauth redirect process, now get our token
            $this->getSlackClient()->setToken(
                $provider->getAccessToken(
                    'authorization_code',
                    [ 'code' => $request->query->get('code') ]
                )
            );

            $auth = $this->getAuthService()->save();

            return new JsonResponse($auth);
        }
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Session\Session
     */
    private function getSession()
    {
        return $this->get('session');
    }

    /**
     * @return SlackClient
     */
    private function getSlackClient()
    {
        return $this->get('app.service.slack_client');
    }

    /**
     * @return AuthService
     */
    private function getAuthService()
    {
        return $this->get('app.service.auth');
    }
}
