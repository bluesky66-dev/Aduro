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

class Thank extends Model
{
  protected $fillable = [
    'user_id', 'torrent_id'
  ];

  /**
   * Belongs to Torrent
   *
   */
  public function torrent()
  {
      return $this->belongsTo(\App\Torrent::class);
  }
}
