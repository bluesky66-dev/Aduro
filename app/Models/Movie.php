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

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    protected $guarded = [];
    protected $primaryKey = 'id';
    public $table = 'movie';

    public function genres()
    {
        return $this->belongsToMany(Genre::class);
    }
    public function cast()
    {
        return $this->belongsToMany(Cast::class, 'cast_movie', 'cast_id', 'movie_id')
            ->orderBy('movie_id')
            ->take(6);
    }
    public function crew()
    {
        return $this->belongsToMany(Crew::class, 'crew_movie', 'person_id', 'movie_id');
    }
    public function companies()
    {
        return $this->belongsToMany(Company::class);
    }
    public function countries()
    {
        return $this->belongsToMany(Company::class);
    }
    public function collection()
    {
        return $this->belongsToMany(Collection::class)->take(1);
    }
    public function torrents()
    {
        return $this->hasMany(Torrent::class, 'tmdb', 'id');
    }
}
