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
 * @author     HDVinnie
 */

namespace App\Models;

use App\Helpers\Bbcode;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    /**
     * Belongs To A Topic.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

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
     * A Post Has Many Likes.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    /**
     * A Post Has Many Tips.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tips()
    {
        return $this->hasMany(BonTransactions::class);
    }

    /**
     * Parse Content And Return Valid HTML.
     *
     * @return string Parsed BBCODE To HTML
     */
    public function getContentHtml()
    {
        return Bbcode::parse($this->content);
    }

    /**
     * Post Trimming.
     *
     * @param $length
     * @param $ellipses
     * @param $strip_html
     *
     * @return string Formatted And Trimmed Content
     */
    public function getBrief($length = 100, $ellipses = true, $strip_html = false)
    {
        $input = $this->content;
        //strip tags, if desired
        if ($strip_html) {
            $input = strip_tags($input);
        }

        //no need to trim, already shorter than trim length
        if (strlen($input) <= $length) {
            return $input;
        }

        //find last space within length
        $last_space = strrpos(substr($input, 0, $length), ' ');
        $trimmed_text = substr($input, 0, $last_space);

        //add ellipses (...)
        if ($ellipses) {
            $trimmed_text .= '...';
        }

        return $trimmed_text;
    }

    /**
     * Get A Post From A ID.
     *
     * @return string
     */
    public function getPostNumber()
    {
        return $this->topic->postNumberFromId($this->id);
    }

    /**
     * Get A Posts Page Number.
     *
     * @return string
     */
    public function getPageNumber()
    {
        $result = ($this->getPostNumber() - 1) / 25 + 1;
        $result = floor($result);

        return $result;
    }
}
