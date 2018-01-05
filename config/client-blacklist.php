<?php
/**
 * NOTICE OF LICENSE
 *
 * UNIT3D is open-sourced software licensed under the GNU General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D
 * @license    https://choosealicense.com/licenses/gpl-3.0/  GNU General Public License v3.0
 * @author     HDVinnie
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Enabled
    |--------------------------------------------------------------------------
    |
    | Blacklist On/Off
    |
    */

    'enabled' => false,

    /*
    |--------------------------------------------------------------------------
    | Blacklist Clients
    |--------------------------------------------------------------------------
    | An array of clients to be blacklisted which will reject them from announcing
    | to the sites tracker.
    |
    |
    */
    'clients' => [
        "Transmission/2.93"
    ],

    /*
    |--------------------------------------------------------------------------
    | Blacklist Browsers
    |--------------------------------------------------------------------------
    | An array of browsers to be blacklisted which will reject them from announcing
    | to the sites tracker.
    |
    |
    */
    'browsers' => [
        "Mozilla", "AppleWebKit", "Safari", "Chrome", "Lynx", "Opera"
    ],

];
