<?php

return [
    /**
     * ---------------------------------------------
     * table
     * ---------------------------------------------
     * Name of the database table that stores all 
     * the invites.
     */
    'table' => 'invites',
    'expiry' => [
        /**
         * -----------------------------------------------
         * type
         * -----------------------------------------------
         * Decide how the expiry is calculated
         * 
         * Acceptable values: never, hours, days
         */
        'type' => 'hours',
        /**
         * -----------------------------------------------
         * value
         * -----------------------------------------------
         * The value that decides the expiry. This field 
         * is ignored if type = never.
         */
        'value' => 24,
    ],

    'routes' => [
        /**
         * -----------------------------------------------
         * follow
         * -----------------------------------------------
         * The url that this package should handle when 
         * user clicks on the invitation link.
         */
        'follow' => 'invitation/accept',

        /**
         * -----------------------------------------------
         * register
         * -----------------------------------------------
         * The url to the user registration page. All 
         * requests handled bu the aboe follow path will
         * try to automatically populate the invitation 
         * code and email field in the registration form
         * in this page.
         * 
         * This will be used as a parameter for route() to
         * retrieve the url to the registration form
         */
        'register' => 'register',
    ],

    /**
     * ------------------------------------------------
     * fields
     * ------------------------------------------------
     * These are the names of the objects in your 
     * registration form. These are used to populate
     * the email and invitation code fields in your 
     * form when user clicks on the invitation mail link
     */
    'fields' => [
        'email' => 'email',
        'code' => 'code',
    ],

    'mail' => [
        /**
         * -----------------------------------------------
         * enabled
         * -----------------------------------------------
         * If enabled, invitation mails will be auto sent
         * when generating the invites (if they have an 
         * email ID).
         */
        'enabled' => env('LI_MAIL_ENABLED', true),
        
        /**
         * -----------------------------------------------
         * subject
         * -----------------------------------------------
         * The subject of the invitation mail. :app will 
         * be replaced by the Application Name as defined
         * in config/app.php in your project route.
         */
        'subject' => 'Your invitation to :app',
    ]
];