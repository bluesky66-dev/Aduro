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

namespace App\Achievements;

use Gstt\Achievements\Achievement;

class UserMade600Uploads extends Achievement
{
    /*
     * The achievement name
     */
    public $name = "600Uploads";

    /*
     * A small description for the achievement
     */
    public $description = "You have made 600 torrent uploads!";

    /*
    * The amount of "points" this user need to obtain in order to complete this achievement
    */
    public $points = 600;
}
