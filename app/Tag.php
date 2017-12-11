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
 
namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{

    public $timestamps = false;

    public $rules = [
        'content' => 'required|unique:tags',
        'slug' => 'required|unique:tags',
    ];

    /**
     * HABTM Torrent
     *
     *
     */
    public function torrents()
    {
        return $this->belongsToMany(\App\Torrent::class);
    }
}
