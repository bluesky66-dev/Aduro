<?php
/**
 * NOTICE OF LICENSE
 *
 * UNIT3D is open-sourced software licensed under the GNU General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D
 * @license    https://choosealicense.com/licenses/gpl-3.0/  GNU General Public License v3.0
 * @author     BluCrew
 */

/*
|--------------------------------------------------------------------------
| HTML Encrypt
|--------------------------------------------------------------------------
|
| Settings
|
*/
return [
    // By default, the ecnvryption is set to true
    'encrypt' => env('HTML_ENCRYPT', true),

    'disable_right_click' => true,
    'disable_ctrl_and_F12_key' => true,
];
