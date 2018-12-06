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

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessMassPM;
use App\User;
use Brian2694\Toastr\Toastr;
use Illuminate\Http\Request;

class MassPMController extends Controller
{
    /**
     * @var Toastr
     */
    private $toastr;

    /**
     * MassPMController Constructor.
     *
     * @param Toastr $toastr
     */
    public function __construct(Toastr $toastr)
    {
        $this->toastr = $toastr;
    }

    /**
     * Mass PM Form.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function massPM()
    {
        return view('Staff.masspm.index');
    }

    /**
     * Send The Mass PM.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return Illuminate\Http\RedirectResponse
     */
    public function sendMassPM(Request $request)
    {
        $staff = auth()->user();
        $users = User::all();

        $sender_id = 1;
        $subject = $request->input('subject');
        $message = $request->input('message');

        $v = validator($request->all(), [
            'subject' => 'required|min:5',
            'message' => 'required|min:5',
        ]);

        if ($v->fails()) {
            return redirect()->route('massPM')
                ->with($this->toastr->error($v->errors()->toJson(), 'Whoops!', ['options']));
        } else {
            foreach ($users as $user) {
                $this->dispatch(new ProcessMassPM($sender_id, $user->id, $subject, $message));
            }

            // Activity Log
            \LogActivity::addToLog("Staff Member {$staff->username} has sent a MassPM.");

            return redirect()->route('massPM')
                ->with($this->toastr->success('MassPM Sent', 'Yay!', ['options']));
        }
    }
}
