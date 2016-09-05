<?php

namespace AppBundle\Service;

use AppBundle\Document\Team AS Team;
use AppBundle\Service\Exception\SlackException;
use AppBundle\Slack\Event\EventInterface;
use GuzzleHttp\Client AS GuzzleClient;
use League\OAuth2\Client\Token\AccessToken;

/**
 * Basic service for interacting with the Slack API.
 */
class SlackClient
{

    /**
     * @var GuzzleClient
     */
    protected $guzzleClient;

    /**
     * @var string
     */
    protected $slackUri;

    /**
     * @var string
     */
    protected $token;

    /**
     * Slack constructor to setup the service.
     *
     * @param GuzzleClient $guzzleClient
     * @param string $slackUri
     */
    public function __construct(GuzzleClient $guzzleClient, $slackUri)
    {
        $this->guzzleClient = $guzzleClient;
        $this->slackUri = $slackUri;
    }

    /**
     * Helper function to make get requests to the api.
     *
     * @param string $method
     * @param array $options
     * @return \Psr\Http\Message\ResponseInterface
     * @throws SlackException
     */
    public function get($method, array $options = [])
    {
        $options = array_merge_recursive($options, ['query' => ['token' => $this->token]]);
        $response = $this->guzzleClient->get($this->formatRequestUrl($method), $options);
        $jsonResponse = json_decode($response->getBody(), true);
        if (false === $jsonResponse['ok']) {
            // we have an error, format some information
            throw new SlackException($jsonResponse['error']);
        }
        return $jsonResponse;
    }

    /**
     * Helper function to format a full URL for the request
     *
     * @param $method
     * @return string
     */
    private function formatRequestUrl($method)
    {
        return $this->slackUri . $method;
    }

    /**
     * Set the token - parametric polymporhism to deal with multiple sources.
     *
     * @param object $object
     */
    public function setToken($object)
    {
        if ($object instanceof Team) {
            /** @var Team $object */
            $this->token = $object->getAuth()->getToken();
        } else if ($object instanceof AccessToken) {
            $this->token = $object->getToken();
        } else {
            throw new \InvalidArgumentException('Unknown object type being used to setToken.');
        }
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

}