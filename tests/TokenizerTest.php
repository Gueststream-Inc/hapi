<?php

namespace Gueststream\HomeAway;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;

class TokenizerTest extends \PHPUnit_Framework_TestCase
{
    public function testReturnsToken()
    {
        // The token we should get back from the tokenizer.
        $expectedTokenValue = "01234567890";

        // Generating a PSR7 response for Guzzle to pass back to the tokenizer as a successful response.
        $streamResource = fopen('php://temp', 'w+');
        $stream = new Stream($streamResource);
        $stream->write('{"@id":"'.$expectedTokenValue.'"}');
        $response = new Response(200,[],$stream);

        // Generating our mock Guzzle Client.
        $mockGuzzleClient = $this
            ->getMockBuilder(GuzzleClient::class)
            ->disableOriginalConstructor()
            ->setMethods(['request'])
            ->getMock();
        $mockGuzzleClient->method('request')->willReturn($response);

        // No need for legit data here as the response is already determined.
        $tokenizer = new Tokenizer('fake-api-key', 'fake-client-id', $mockGuzzleClient);

        $realTokenValue = $tokenizer->tokenize('fake-credit-card-number');

        $this->assertSame($expectedTokenValue, $realTokenValue);
    }

    /**
     * @expectedException \Gueststream\HomeAway\Exception\TokenizerException
     */
    public function testConvertsGuzzleExceptionToTokenizerException()
    {
        /**
         * We'll hit the real API with fake credentials and get a 401 not authorized
         * which usually results in a Guzzle Exception but we should get a TokenizerException
         * to allow our app to be more precise about the problem that's occurring.
         */
        $tokenizer = new Tokenizer('fake-api-key','fake-client-id');

        $tokenizer->tokenize('fake-credit-card-number');
    }
}
