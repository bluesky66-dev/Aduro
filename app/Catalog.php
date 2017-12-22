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

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Torrent;

class Catalog extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = "catalogs";

    /**
     * Mass assignment fields
     *
     */
    protected $fillable = [
        'name', 'slug'
    ];
}
