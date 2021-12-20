<?php


return [
    'tables' => [
        'periodic_sent_entries' => 'periodic_sent_entries',
    ],

    'defaults' => [
        'queue'        => null, // default
        'connection'   => null,  // default
        'notification' => \PeriodicNotice\Notifications\PeriodicPublicationNotification::class,
    ],
];
