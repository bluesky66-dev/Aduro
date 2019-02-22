<?php
/**
 * NOTICE OF LICENSE.
 *
 * UNIT3D is open-sourced software licensed under the GNU General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D
 *
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 * @author     singularity43
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rss extends Model
{
    use SoftDeletes;

    /**
     * The Database Table Used By The Model.
     *
     * @var string
     */
    protected $table = 'rss';

    /**
     * Indicates If The Model Should Be Timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The Attributes That Should Be Cast To Native Types.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'string',
        'json_torrent' => 'array',
        'expected_fields' => 'array',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

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
     * Belongs To A Staff Member.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function staff()
    {
        // Not needed yet. Just added for future extendability.
        return $this->belongsTo(User::class, 'staff_id');
    }

    /**
     * Get the RSS feeds JSON Torrent as object.
     *
     * @return string
     */
    public function getObjectTorrentAttribute()
    {
        // Went with attribute to avoid () calls in views. Uniform ->object_torrent vs ->json_torrent.
        if ($this->json_torrent) {
            $expected = $this->expected_fields;

            return (object) array_merge($expected, $this->json_torrent);
        }

        return false;
    }

    /**
     * Get the RSS feeds expected fields for form validation.
     *
     * @return array
     */
    public function getExpectedFieldsAttribute()
    {
        // Just Torrents for now... extendable to check on feed type in future.
        $expected_fields = ['search' => null, 'description' => null, 'uploader' => null, 'imdb' => null,
            'mal' => null, 'categories' => null, 'types' => null, 'genres' => null, 'freeleech' => null,
            'doubleupload' => null, 'featured' => null, 'stream' => null, 'highspeed' => null, 'internal' => null,
            'alive' => null, 'dying' => null, 'dead' => null, 'sd' => null, ];

        return $expected_fields;
    }
}
