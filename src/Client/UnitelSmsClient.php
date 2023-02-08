<?php

namespace Tsetsee\SmsPusher\Client;

use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;
use Tsetsee\SmsPusher\Exception\SmsFailedException;
use Tsetsee\TseGuzzle\TseGuzzle;

class UnitelSmsClient implements SmsClientInterface
{
    private const URL = 'http://sms.unitel.mn/sendSMS.php';

    private TseGuzzle $client;

    public function __construct(
        private string $uname,
        private string $upass,
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
                    'uname' => $this->uname,
                    'upass' => $this->upass,
                    'from' => $this->from,
                    'mobile' => $phoneNumber,
                    'sms' => $text,
                ],
            ]);

            $body = (string)$response->getBody();

            if('SUCCESS' !== $body) {
                throw new SmsFailedException($body);
            }
        }
        catch(GuzzleException $e) {
            throw new SmsFailedException('Connection failed', 0, $e);
        }
    }

    public function supports(string $phoneNumber): bool
    {
        return preg_match('/^(80|86|88|89)\d{6}$/', $phoneNumber) > 0;
    }
}
