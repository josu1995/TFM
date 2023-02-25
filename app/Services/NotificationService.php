<?php

namespace App\Services;

// FCM
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;
use Log;

class NotificationService
{

    public function sendDownstreamMessage($token, $title = null, $message = null, $data = null)
    {

        Log::info('Enviada notificacion con titulo "'. $title .'" y mensaje '. $message);

        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60*20);

        $notificationBuilder = new PayloadNotificationBuilder($title);
        $notificationBuilder->setBody($message)
            ->setSound('default');

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData($data);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();


        return FCM::sendTo($token, $option, $notification, $data);
    }
}
