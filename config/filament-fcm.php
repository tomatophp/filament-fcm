<?php

return [
    /*
     * ---------------------------------------------------------------
     * Firebase Project Configuration
     * ---------------------------------------------------------------
     *
     */
    "project" => [
        "apiKey"=> env("FIREBASE_API_KEY"),
        "authDomain"=> env("FIREBASE_AUTH_DOMAIN"),
        "databaseURL"=> env("FIREBASE_DATABASE_URL"),
        "projectId"=> env("FIREBASE_PROJECT_ID"),
        "storageBucket"=> env("FIREBASE_STORAGE_BUCKET"),
        "messagingSenderId"=> env("FIREBASE_MESSAGING_SENDER_ID"),
        "appId"=> env("FIREBASE_APP_ID"),
        "measurementId" => env("FIREBASE_MEASUREMENT_ID"),
    ],

    /*
     * ---------------------------------------------------------------
     * Firebase Cloud Messaging Configuration
     * ---------------------------------------------------------------
     *
     */
    "vapid" => env("FIREBASE_VAPID"),

    /*
     * ---------------------------------------------------------------
     * Firebase Alert Sound when notification received
     * ---------------------------------------------------------------
     *
     */
    "alert" => [
        "sound" => env("FCM_ALERT_SOUND"),
    ]
];
