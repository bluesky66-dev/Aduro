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
 
namespace App\Services\Contracts;

interface MovieTvInterface
{
    /**
     * Find Movie or Tv using IMDB id
     *
     * @param array $keys
     * @param null|string $type
     * @return array
     */
    public function find($keys, $type = null);

    /**
     * @param $id
     * @return \Bhutanio\Movietvdb\Data\Movie
     */
    public function movie($id);

    /**
     * @param $id
     * @return \Bhutanio\Movietvdb\Data\Tv
     */
    public function tv($id);

    /**
     * @param $id
     * @return array
     */
    public function person($id);
}
