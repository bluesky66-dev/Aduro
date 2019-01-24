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

namespace App\Http\Controllers;

use App\User;
use App\Report;
use App\Torrent;
use App\TorrentRequest;
use Brian2694\Toastr\Toastr;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * @var Report
     */
    private $report;

    /**
     * @var Toastr
     */
    private $toastr;

    /**
     * ReportController Constructor.
     *
     * @param Report  $report
     * @param Toastr  $toastr
     */
    public function __construct(Report $report, Toastr $toastr)
    {
        $this->report = $report;
        $this->toastr = $toastr;
    }

    /**
     * Create A Request Report.
     *
     * @param \Illuminate\Http\Request $request
     * @param $id
     *
     * @return Illuminate\Http\RedirectResponse
     */
    public function request(Request $request, int $id)
    {
        $torrentRequest = TorrentRequest::findOrFail($id);
        $reported_by = auth()->user();
        $reported_user = $torrentRequest->user;

        $v = validator($request->all(), [
            'message' => 'required',
        ]);

        if ($v->fails()) {
            return redirect()->route('request', ['id' => $id])
                ->with($this->toastr->error($v->errors()->toJson(), 'Whoops!', ['options']));
        } else {
            $this->report->create([
                'type' => 'Request',
                'request_id' => $torrentRequest->id,
                'torrent_id' => 0,
                'reporter_id' => $reported_by->id,
                'reported_user' => $reported_user->id,
                'title' => $torrentRequest->name,
                'message' => $request->get('message'),
                'solved' => 0,
            ]);

            // Activity Log
            \LogActivity::addToLog("Member {$reported_by->username} has made a new Torrent Request report.");

            return redirect()->route('request', ['id' => $id])
                ->with($this->toastr->success('Your report has been successfully sent', 'Yay!', ['options']));
        }
    }

    /**
     * Create A Torrent Report.
     *
     * @param \Illuminate\Http\Request $request
     * @param $slug
     * @param $id
     *
     * @return Illuminate\Http\RedirectResponse
     */
    public function torrent(Request $request, $slug, int $id)
    {
        $torrent = Torrent::findOrFail($id);
        $reported_by = auth()->user();
        $reported_user = $torrent->user;

        $v = validator($request->all(), [
            'message' => 'required',
        ]);

        if ($v->fails()) {
            return redirect()->route('torrent', ['slug' => $slug, 'id' => $id])
                ->with($this->toastr->error($v->errors()->toJson(), 'Whoops!', ['options']));
        } else {
            $this->report->create([
                'type' => 'Torrent',
                'torrent_id' => $torrent->id,
                'request_id' => 0,
                'reporter_id' => $reported_by->id,
                'reported_user' => $reported_user->id,
                'title' => $torrent->name,
                'message' => $request->get('message'),
                'solved' => 0,
            ]);

            // Activity Log
            \LogActivity::addToLog("Member {$reported_by->username} has made a new Torrent report.");

            return redirect()->route('torrent', ['slug' => $slug, 'id' => $id])
                ->with($this->toastr->success('Your report has been successfully sent', 'Yay!', ['options']));
        }
    }

    /**
     * Create A User Report.
     *
     * @param \Illuminate\Http\Request $request
     * @param $username
     * @param $id
     *
     * @return Illuminate\Http\RedirectResponse
     */
    public function user(Request $request, $username, int $id)
    {
        $reported_user = User::findOrFail($id);
        $reported_by = auth()->user();

        $v = validator($request->all(), [
            'message' => 'required',
        ]);

        if ($v->fails()) {
            return redirect()->route('profile', ['username' => $username, 'id' => $id])
                ->with($this->toastr->error($v->errors()->toJson(), 'Whoops!', ['options']));
        } else {
            $this->report->create([
                'type' => 'User',
                'torrent_id' => 0,
                'request_id' => 0,
                'reporter_id' => $reported_by->id,
                'reported_user' => $reported_user->id,
                'title' => $reported_user->username,
                'message' => $request->get('message'),
                'solved' => 0,
            ]);

            // Activity Log
            \LogActivity::addToLog("Member {$reported_by->username} has made a new User report.");

            return redirect()->route('profile', ['username' => $username, 'id' => $id])
                ->with($this->toastr->success('Your report has been successfully sent', 'Yay!', ['options']));
        }
    }
}
