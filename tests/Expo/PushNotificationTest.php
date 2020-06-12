<?php
declare(strict_types=1);

namespace Codeliter\Tests\Expo;

use Codeliter\ExpoPush\Exceptions\InvalidPushReceiverException;
use Codeliter\ExpoPush\Expo\PushNotification;
use Codeliter\Tests\BaseTestClass;

class PushNotificationTest extends BaseTestClass
{
    public function test_can_send_push_notifications()
    {
        $send = PushNotification::send(
            ['ExponentPushToken[oj4iK4CRA7Ry8gDCrtawef]'],
            'Test',
            'Test body'
        );

        $this->assertArrayHasKey('id', $send);
        $this->assertArrayHasKey('status', $send);
    }

    public function test_can_send_push_notifications_when_too_many_experience_ids()
    {
        $send = PushNotification::send(
            ['ExponentPushToken[oj4iK4CRA7Ry8gDCrtawe1]', 'ExponentPushToken[oj4iK4CRA7Ry8gDCrtawef]'],
            'Test',
            'Test body'
        );


        $this->assertCount(2, $send);
    }

    public function test_can_send_throws_invalid_push_receiver_exceptions()
    {
        $this->expectException(InvalidPushReceiverException::class);
        $send = PushNotification::send(
            [],
            'Test',
            'Test body'
        );
    }
}