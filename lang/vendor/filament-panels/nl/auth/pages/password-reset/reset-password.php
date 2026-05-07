<?php

return [

    'title' => 'Wachtwoord opnieuw instellen',

    'heading' => 'Wachtwoord opnieuw instellen',

    'form' => [

        'email' => [
            'label' => 'E-mailadres',
        ],

        'password' => [
            'label' => 'Nieuw wachtwoord',
            'validation_attribute' => 'wachtwoord',
        ],

        'password_confirmation' => [
            'label' => 'Nieuw wachtwoord bevestigen',
        ],

        'actions' => [

            'reset' => [
                'label' => 'Wachtwoord opnieuw instellen',
            ],

        ],

    ],

    'notifications' => [

        'throttled' => [
            'title' => 'Te veel resetpogingen',
            'body' => 'Probeer het opnieuw over :seconds seconden.',
        ],

    ],

];
