<?php
/**
 * Created by Aleksandr Berdnikov.
 * Copyright 2016 Onix-Systems.
*/

return [
    'directories' => [
        'content' => __DIR__.'/content',
    ],
    'birdzillaSiteUrl' => 'http://www.birdzilla.com',
    'skillHttpsUrl' => 'https://ff800e5e.ngrok.io/alexa/demo',
    'allowedContentTypes' => 'jpg|jpeg|gif|mp3',
    'authorization' => require(__DIR__.'/private/netatmo.auth.php'),
    'authorizationRequestMessage' => 'You must have a Netatmo account to use this skill. Please use the Alexa app to link your Amazon account with your Netatmo Account.',
    'authorizationRedirectUrl' => 'https://pitangui.amazon.com/spa/skill/account-linking-status.html?vendorId=M3FVQUHK7SUU19',
];

