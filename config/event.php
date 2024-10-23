<?php

return [
    'user.login'  => [
        [app\event\UserActionLogEvent::class, 'logLogin'],
    ],
    'user.action' => [
        [app\event\UserActionLogEvent::class, 'logAction'],
    ],

];
