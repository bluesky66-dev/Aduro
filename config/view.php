<?php
/**
 * NOTICE OF LICENSE.
 *
 * UNIT3D Community Edition is open-sourced software licensed under the GNU Affero General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D Community Edition
 *
 * @author     HDVinnie <hdinnovations@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

 return [

     /*
     |--------------------------------------------------------------------------
     | View Storage Paths
     |--------------------------------------------------------------------------
     |
     | Most templating systems load templates from disk. Here you may specify
     | an array of paths that should be checked for your views. Of course
     | the usual Laravel view path has already been registered for you.
     |
     */

     'paths' => [
         resource_path('views'),
     ],

     /*
     |--------------------------------------------------------------------------
     | Compiled View Path
     |--------------------------------------------------------------------------
     |
     | This option determines where all the compiled Blade templates will be
     | stored for your application. Typically, this is within the storage
     | directory. However, as usual, you are free to change this value.
     |
     */

     'compiled' => env(
         'VIEW_COMPILED_PATH',
         realpath(storage_path('framework/views'))
     ),

 ];
