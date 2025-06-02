<?php

return [
    'user.login'  => [
        [\app\common\event\UserActionLogEvent::class, 'logLogin'],
    ],
    'user.action' => [
        [\app\common\event\UserActionLogEvent::class, 'logAction'],
    ],

];
