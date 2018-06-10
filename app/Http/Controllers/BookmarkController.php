<?php
/**
 * NOTICE OF LICENSE
 *
 * UNIT3D is open-sourced software licensed under the GNU General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 * @author     HDVinnie
 */

namespace App\Http\Controllers;

class BookmarkController extends Controller
{
    /**
     * Get Torrent Bookmarks
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function bookmarks()
    {
        $myBookmarks = auth()->user()->bookmarks;

        return view('bookmark.bookmarks', ['myBookmarks' => $myBookmarks]);
    }
}
