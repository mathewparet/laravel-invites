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
];