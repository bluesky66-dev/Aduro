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

namespace App\Http\Controllers\MediaHub;

use App\Models\Tv;
use App\Models\Season;
use App\Http\Controllers\Controller;

class TvSeasonController extends Controller
{
    /**
     * Display All TV Seasons.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        //
    }

    /**
     * Show A TV Season.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        $season = Season::with(['episodes'])->findOrFail($id);
        $show = Tv::whereId($season->tv_id)->first();

        return view('mediahub.tv.season.show', [
            'season' => $season,
            'show'   => $show,
        ]);
    }
}