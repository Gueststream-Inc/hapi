<?php
/**
 * HAPI (HomeAway Payment Island) Tokenizer.
 */
namespace Gueststream\HomeAway;

use Gueststream\HomeAway\Exception\TokenizerException;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ClientException as GuzzleClientException;

class Tokenizer
{
    /**
     * Token type for all tokenizations requests is always CC.
     */
    const TOKEN_TYPE = "CC";
    /**
     * Tokenization Server URL
     */
    const SERVER_URL = "https://sensei.homeaway.com/tokens";
    /**
     * HAPI Secure API Key.
     *
     * @var string
     */
    private $apiKey;
    /**
     * HAPI Client ID.
     *
     * @var string
     */
    private $clientId;
    /**
     * @var GuzzleClient
     */
    private $guzzleClient;
    /**
     * Unix Timestamp for generating the digest hash.
     *
     * @var string
     */
    private $timestamp;

    /**
     * HAPI constructor.
     *
     * @param $apiKey
     * @param $clientId
     */
    public function __construct($apiKey, $clientId, GuzzleClient $guzzleClient = null)
    {
        $this->setApiKey($apiKey);
        $this->setClientId($clientId);
        $this->guzzleClient = !is_null($guzzleClient) ? $guzzleClient : new GuzzleClient();
    }

    /**
     * @param mixed $apiKey
     */
    private function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @param mixed $clientId
     */
    private function setClientId($clientId)
    {
        $this->clientId = $clientId;
    }

    /**
     * @param $creditCardNumber
     * @throws TokenizerException
     */
    public function tokenize($creditCardNumber)
    {
        try {
            $tokenData = $this->performTokenizationRequest($creditCardNumber);
        } catch (GuzzleClientException $exception) {
            /**
             * Convert Guzzle Exceptions to Tokenizer Exception.
             * It seems every actual error ties to a non-200 response which
             * causes Guzzle to throw an Exception with the JSON body content as
             * the message anyway.
             */
            throw new TokenizerException('something went wrong');
        }

        return $tokenData->{'@id'};
    }

    private function performTokenizationRequest($creditCardNumber)
    {
        $httpResponse = $this->guzzleClient->request(
            'POST',
            self::SERVER_URL,
            [
                'query' => [
                    'time' => $this->getTimestamp(),
                    'digest' => $this->generateDigest(),
                    'clientId' => $this->clientId
                ],
                'json' => [
                    'tokenType' => 'CC',
                    'value' => $creditCardNumber
                ],
                'allow_redirects' => false,
                'headers' => [
                    'User-Agent' => 'Gueststream/1.0',
                    'Accept' => 'application/json'
                ]
            ]
        );

        return json_decode($httpResponse->getBody());
    }

    /**
     * @return string
     */
    private function generateDigest()
    {
        return hash("sha256", $this->getTimestamp() . $this->apiKey);
    }

    /**
     * @return string
     */
    private function getTimestamp()
    {
        if (!$this->timestamp) {
            $epoch_milliseconds = time() * 1000;
            $this->timestamp = number_format($epoch_milliseconds, 0, '.', '');
        }

        return $this->timestamp;
    }
}
