<?php
declare(strict_types=1);

namespace Codeliter\ExpoPush\Expo;

use Codeliter\ExpoPush\Exceptions\InvalidPushReceiverException;
use Unirest\Request;
use Unirest\Response;

/**
 * Class PushNotification
 * @package Codeliter\ExpoPush\Expo
 * @author  Abolarin Stephen <hackzlord@gmail.com>
 */
final class PushNotification
{
    // all endpoints
    const ENDPOINTS = [
        'push' => 'https://expo.io/--/api/v2/push/send'
    ];

    // Max Number of users that can receive the push in one batch
    const MAX_RECEIVERS_PER_BATCH = 100;

    /**
     * Send Push Notifications
     * @param array $expoTokens
     * @param string $title
     * @param string $body
     * @param string|null $channel
     * @param array|null $data
     * @return array
     * @throws \Exception
     */
    public static function send(array $expoTokens,
                                string $title,
                                string $body,
                                ?string $channel = '',
                                ?array $data = []): array
    {
        // Validate the expo tokens
        $expoTokens = static::validateTokens($expoTokens);

        if (count($expoTokens) == 0)
            throw new InvalidPushReceiverException("Please specify at least one receiver.");

        $payload = [
            'priority' => 'high',
            'title' => $title,
            'body' => $body,
            'sound' => "default"
        ];

        // If the channel was specified
        if (strlen($channel) > 0)
            $payload['channelId'] = $channel;

        // If the data was specified
        if (count($data) > 0)
            $payload['data'] = $data;

        $response = [];

        // Chunk it into a batch of 100
        array_map(function ($batch) use ($payload, &$response) {
            $payload['to'] = $batch;
            $send = static::doPush($payload);
            array_push($response, ...$send);
        }, array_chunk($expoTokens, static::MAX_RECEIVERS_PER_BATCH));

        return (count($response) === 1) ? current($response) : $response;
    }

    /**
     * Validate if the token is a valid expo token
     * @param array $expoTokens
     * @return array
     */
    private static function validateTokens(array $expoTokens): array
    {
        return array_values(array_unique(
            array_filter(
                $expoTokens, function ($token) {
                return stripos($token, 'ExponentPushToken') !== false;
            })
        ));
    }

    /**
     * @param array $payload
     * @return array
     * @throws \ErrorException
     */
    private static function doPush(array $payload)
    {
        $send = static::curler(self::ENDPOINTS['push'], $payload);


        // If the push was sent successfully
        if ($send->code === 200)
            return array_values(json_decode(json_encode($send->body->data), true));

        // If the error os too many experience IDS let's handle it smartly
        if ($send->code === 400 && current($send->body->errors)->code === 'PUSH_TOO_MANY_EXPERIENCE_IDS') {
            $response = [];

            foreach (current($send->body->errors)->details ?? [] as $project => $tokens) {
                // Send for each project separately
                array_push($response, ...static::doPush(array_merge($payload, ['to' => $tokens])));
            }

            return $response;
        }
    }

    /**
     * Send the curl request
     * @param string $url
     * @param array $payload
     * @return Response
     * @throws \ErrorException
     */
    private static function curler(string $url, array $payload): Response
    {
        Request::verifyHost(false);

        try {
            $body = Request\Body::Json($payload);
        } catch (\Throwable $throwable) {
            throw new \ErrorException($throwable);
        }

        return Request::post($url, ["Content-Type" => "application/json", 'accept-encoding' => 'gzip, deflate'], $body);
    }
}