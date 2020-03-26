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

use App\Helpers\StringHelper;
use Illuminate\Database\Eloquent\Model;

class Subtitle extends Model
{
    /**
     * Belongs To A User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withDefault([
            'username' => 'System',
            'id'       => '1',
        ]);
    }

    /**
     * Belongs To A Torrent.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function torrent()
    {
        return $this->belongsTo(Torrent::class);
    }

    /**
     * Belongs To A Media Language.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function language()
    {
        return $this->belongsTo(MediaLanguage::class);
    }

    /**
     * Returns The Size In Human Format.
     *
     * @param null $bytes
     * @param int  $precision
     *
     * @return string
     */
    public function getSize($bytes = null, $precision = 2)
    {
        $bytes = $this->file_size;

        return StringHelper::formatBytes($bytes, 2);
    }
}
