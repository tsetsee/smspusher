<?php

namespace Tsetsee\SmsPusher;

use Tsetsee\SmsPusher\Client\SmsClientInterface;
use Tsetsee\SmsPusher\Exception\SmsClientNotFoundException;
use Tsetsee\SmsPusher\Exception\SmsFailedException;

class SmsPusher
{
    /**
     * @param array<SmsClientInterface> $clients
     */
    public function __construct(
        private array $clients = [],
    ){
    }

    /**
     * @throws SmsClientNotFoundException
     * @throws SmsFailedException
     */
    public function setSms(string $phoneNumber, string $text): void 
    {
        foreach($this->clients as $client) {
            if(!$client->supports($phoneNumber)) {
                continue;
            }

            $client->sendSms($phoneNumber, $text);
        }

        throw new SmsClientNotFoundException();
    }

    public function addClient(SmsClientInterface $client): void
    {
        $this->clients[] = $client;
    }
}
