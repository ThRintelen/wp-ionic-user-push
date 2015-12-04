<?php

include_once  '../../../wp-load.php' ;
include_once __DIR__ . '/includes/class-iup-send-push.php';

$sendPush = new Ionic_User_Send_Push();
$sendPush->send_scheduled_push_notification();
