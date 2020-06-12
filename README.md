# expo-notification-pusher

This is a utility library that makes sending push notifications from your server to your expo app easily.


## Features
* Send notifications to specific channels (Android only).
* Smart sending of push notifications to different expo projects.
* Auto batching of push notification if receivers are more than the recommended 100.

## Requirements
* PHP 7.1+
* [Unirest-php](https://packagist.org/packages/mashape/unirest-php)

## Installation
### Using Composer

To install expo-notification-pusher with Composer, add the following to your composer.json file:

```
{
    "require": {
        "codeliter/expo-notification-pusher": "1.*"
    }
}
```

or by running the following command:

```
composer require mashape/unirest-php
```

* Install from source
Clone the repository by running the following command:
```
git clone https://github.com/codeliter/expo-notification-pusher.git
```

At the top of your code add:
```
require_once '/path/to/expo-notification-pusher/src/Expo/PushNotification.php';
```

## Usage
### Sending a basic Push
```
$send = PushNotification::send(['ExponentPushToken[oj4iK4CRA7Ry8gDCrtawef]'], 'Test','Test body');
```

* The first argument must be an array of valid expo tokens. E.g `['ExponentPushToken[oj4iK4CRA7Ry8gDCrtawef]']`

* The Second argument is the title of the push. This is required.

* The Third argument is the body of the push. This is required.

### Sending a standard push
```
// The channel we want this notification to be pushed to (Android Only). This allows for grouping of notifications.
// The channel must have been initialized inside the expo app already.
$channel = "notifications";

// This contains data we need inside the app to move the user to a specific screen or handle some other things.
$data  = [
    "intent"=>'notification',
    'type'=>'chat',
    'message'=>'Hello'
];

$send = PushNotification::send(['ExponentPushToken[oj4iK4CRA7Ry8gDCrtawef]'], 'Test','Test body', $channel, $data);
```


Made with ♥ from [**Codeliter**](https://github.com/codeliter)