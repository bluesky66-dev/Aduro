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

namespace App\Achievements;

use Gstt\Achievements\Achievement;

class UserMadeTenComments extends Achievement
{
    /*
     * The achievement name
     */
    public $name = "10Comments";

    /*
     * A small description for the achievement
     */
    public $description = "Wow! You have already made 10 comments!";

    /*
    * The amount of "points" this user need to obtain in order to complete this achievement
    */
    public $points = 10;

    /*
     * Triggers whenever an Achiever makes progress on this achievement
    */
    public function whenProgress($progress)
    {
    }

    /*
     * Triggers whenever an Achiever unlocks this achievement
    */
    public function whenUnlocked($progress)
    {
    }
}
