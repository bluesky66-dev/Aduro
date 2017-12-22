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
 
namespace App\Services\Data;

use Carbon\Carbon;

class Person
{
    public $imdb;

    public $tmdb;

    public $name;

    public $aka;

    public $gender;

    public $birthday;

    public $deathday;

    public $placeOfBirth;

    public $biography;

    public $photo;

    public $photos;

    public $character;
    public $order;
    public $job;

    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                if (is_array($value) && !count($value)) {
                    $value = null;
                }
                $this->$key = $value;
            }
        }

        $this->birthday = $this->birthday ? new Carbon($this->birthday) : null;
        $this->deathday = $this->deathday ? new Carbon($this->deathday) : null;
    }
}
