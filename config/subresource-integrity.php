<?php
/**
 * NOTICE OF LICENSE.
 *
 * UNIT3D is open-sourced software licensed under the GNU Affero General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D
 *
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 * @author     HDVinnie
 */

return [
    'base_path' => base_path('/public'),

    'algorithm' => env('SRI_ALGORITHM', 'sha256'),

    'mix_sri_path' => public_path('mix_sri.json'),

    'hashes' => [
        //
    ],
];
