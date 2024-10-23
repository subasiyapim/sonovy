<?php

namespace App\Services;

use App\Models\Integration;

use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Facade;

class TwilioServices extends Facade
{
    private $sid;
    private $token;
    private $number;
    private $client;

    public function __construct()
    {
        $integrasion = Integration::where('id', 5)->first();
        $this->sid = $integrasion->key;
        $this->token = $integrasion->secret;
        $this->number = $integrasion->url;

        try {

            $this->client = new Client($this->sid, $this->token);
            return true;
        } catch (TwilioException $exception) {

            return $exception->getMessage();
        }
    }

    public function send($receiver, $message)
    {
        try {
            $sms = $this->client->messages->create(

                $receiver,
                [
                    'from' => $this->number,
                    'body' => $message
                ]
            );

            return $sms;
        } catch (TwilioException $exception) {

            return $exception->getMessage();
        }
    }
}
