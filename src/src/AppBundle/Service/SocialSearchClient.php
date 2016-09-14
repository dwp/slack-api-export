<?php

namespace AppBundle\Service;

use AppBundle\Document\Message;
use GuzzleHttp\Client AS GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use Psr\Log\LoggerInterface;

/**
 * Basic service for interacting with the social search api
 */
class SocialSearchClient
{

    /**
     * @var GuzzleClient
     */
    protected $guzzleClient;

    /**
     * @var string
     */
    protected $serviceUri;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Slack constructor to setup the service.
     *
     * @param GuzzleClient $guzzleClient
     * @param string $uri
     * @param LoggerInterface $logger
     */
    public function __construct(
        GuzzleClient $guzzleClient,
        $uri,
        LoggerInterface $logger
    )
    {
        $this->guzzleClient = $guzzleClient;
        $this->serviceUri = $uri;
        $this->logger = $logger;
        // if env var set then use that
        if (is_null($this->serviceUri)) {
            $this->serviceUri = getenv("SOCIAL_SEARCH_API");
        }
    }

    /**
     * Method to push to the remote api.
     *
     * @param Message $message
     */
    public function createMessage(Message $message)
    {
        $this->logger->info("Creating new message in the social search API.");
        if ($this->invalidService()) return; // do nothing

        try {
            // otherwise try and make our request with low timeout settings to ensure no issues.
            $this->guzzleClient->post(
                $this->serviceUri . '/messages',
                [
                    'json' => $message->eventArray(),
                    'timeout' => 5,
                    'connect_timeout' => 1
                ]
            );
        } catch (RequestException $e) {
            $this->logger->error("Unable to POST data to social search API: " . $e->getMessage());
        }
    }

    /**
     * BAsic helper to determine if the search service is valid.
     * @return bool
     */
    private function invalidService()
    {
        if (is_null($this->serviceUri)) {
            $this->logger->info("Invalid service URI configuration in the social search client - unable to proceed.");
            return true;
        }
        return false;
    }
}