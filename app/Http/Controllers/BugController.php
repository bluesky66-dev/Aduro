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

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\User;
use App\Mail\Bug;
use \Toastr;

class BugController extends Controller
{
    /**
     * Bug Report Form
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function bugForm()
    {
        return view('bug.bug');
    }

    /**
     * Send Bug Report
     *
     * @param \Illuminate\Http\Request $request
     * @return Illuminate\Http\RedirectResponse
     */
    public function bug(Request $request)
    {
        // Fetch owner account
        $user = User::where('id', 3)->first();
        $input = $request->all();

        Mail::to($user->email, $user->username)->send(new Bug($input));

        return redirect()->route('home')
        ->with(Toastr::success('Your Bug Was Successfully Sent!', 'Yay!', ['options']));
    }
}
