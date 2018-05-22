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

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use \Toastr;

class GiftController extends Controller
{

    /**
     * Send Gift Form
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $users = User::oldest('username')->get();
        return view('Staff.gift.index', compact('users'));
    }

    /**
     * Send The Gift
     *
     * @param Request $request
     * @return Illuminate\Http\RedirectResponse
     */
    public function gift(Request $request)
    {
        $staff = auth()->user();

        $username = $request->input('username');
        $seedbonus = $request->input('seedbonus');
        $invites = $request->input('invites');
        $fl_tokens = $request->input('fl_tokens');

            $v = validator($request->all(), [
                'username' => "required|exists:users,username|max:180",
                'seedbonus' => "required|numeric|min:0",
                'invites' => "required|numeric|min:0",
                'fl_tokens' => "required|numeric|min:0"
            ]);

        if ($v->fails()) {
            return redirect()->route('systemGift')
                ->with(Toastr::error($v->errors()->toJson(), 'Whoops!', ['options']));
        } else {
            $recipient = User::where('username', 'LIKE', $username)->first();

            if (!$recipient) {
                return redirect()->route('systemGift')
                    ->with(Toastr::error('Unable to find specified user', 'Whoops!', ['options']));
            }

            $recipient->seedbonus += $seedbonus;
            $recipient->invites += $invites;
            $recipient->fl_tokens += $fl_tokens;
            $recipient->save();

            // Activity Log
            \LogActivity::addToLog("Staff Member {$staff->username} has sent a system gift to {$recipient->username} account.");

            return redirect()->route('systemGift')
                ->with(Toastr::success('Gift Sent', 'Yay!', ['options']));
        }
    }
}
