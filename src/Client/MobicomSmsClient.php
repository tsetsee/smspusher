<?php

namespace Tsetsee\SmsPusher\Client;

use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;
use Tsetsee\SmsPusher\Exception\SmsFailedException;
use Tsetsee\TseGuzzle\TseGuzzle;

class MobicomSmsClient implements SmsClientInterface
{
    private const URL = 'http://27.123.214.168/smsmt/mt';

    private TseGuzzle $client;

    public function __construct(
        private string $serviceName,
        private string $username,
        private string $from,
        ?LoggerInterface $logger = null,
    )
    {
        $this->client = new TseGuzzle([
            'logger' => $logger,
        ]); 
    }

    public function sendSms(string $phoneNumber, string $text): void
    {
        try {
            $response = $this->client->getClient()->request('GET', self::URL, [
                'query' => [
                    'servicename' => $this->serviceName,
                    'username' => $this->username,
                    'from' => $this->from,
                    'to' => $phoneNumber,
                    'msg' => $text,
                ],
            ]);

            $body = (string)$response->getBody();

            if('Sent' !== $body) {
                throw new SmsFailedException($body);
            }
        }
        catch(GuzzleException $e) {
            throw new SmsFailedException('Connection failed', 0, $e);
        }
    }

    public function supports(string $phoneNumber): bool
    {
        return preg_match('/^(85|94|95|99)\d{6}$/', $phoneNumber) > 0;
    }
}
