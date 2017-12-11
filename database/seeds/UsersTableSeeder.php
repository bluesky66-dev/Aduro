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

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('users')->delete();

        \DB::table('users')->insert(array (
            0 =>
            array (
                'id' => 0,
                'username' => 'System',
                'email' => 'system@none.com',
                'group_id' => 9,
                'password' => \Hash::make(env('DEFAULT_OWNER_PASSWORD')),
            ),
            1 =>
            array (
                'id' => 1,
                'username' => 'Bot',
                'email' => 'bot@none.com',
                'group_id' => 9,
                'password' => \Hash::make(env('DEFAULT_OWNER_PASSWORD')),
            ),
            2 =>
            array (
                'id' => 2,
                'username' => env('DEFAULT_OWNER_NAME'),
                'email' => env('DEFAULT_OWNER_EMAIL'),
                'group_id' => 10,
                'password' => \Hash::make(env('DEFAULT_OWNER_PASSWORD')),
            ),
        ));
    }
}
