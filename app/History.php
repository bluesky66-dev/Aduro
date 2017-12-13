<?php
/**
 * NOTICE OF LICENSE
 *
 * UNIT3D is open-sourced software licensed under the GNU General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D
 * @license    https://choosealicense.com/licenses/gpl-3.0/  GNU General Public License v3.0
 * @author     Bruzer
 */

namespace App;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class History extends Model
{
    use Sortable;

    public $sortable = ['id', 'agent', 'active', 'seeder', 'uploaded', 'downloaded', 'seedtime', 'created_at', 'updated_at', 'completed_at'];

    //
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'history';

    protected $fillable = ['user_id', 'info_hash'];

    protected $dates = ['completed_at'];

    public function user()
    {
        return $this->belongsTo(\App\User::class);
    }

    public function torrent()
    {
        return $this->belongsTo(\App\Torrent::class, "info_hash", "info_hash");
    }
}
