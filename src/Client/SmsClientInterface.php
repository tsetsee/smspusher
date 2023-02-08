<?php

namespace Tsetsee\SmsPusher\Client;

interface SmsClientInterface
{
    public function sendSms(string $phoneNumber, string $text): void;
    public function supports(string $phoneNumber): bool;
}
