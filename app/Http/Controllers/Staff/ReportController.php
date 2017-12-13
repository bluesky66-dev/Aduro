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

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\PrivateMessage;
use App\Report;
use \Toastr;

class ReportController extends Controller
{
    /**
     * Reports System
     *
     *
     */
    public function getReports()
    {
        $reports = Report::orderBy('created_at', 'DESC')->get();

        return view('Staff.reports.index', ['reports' => $reports]);
    }

    public function getReport($report_id)
    {
        $report = Report::findOrFail($report_id);

        return view('Staff.reports.report', ['report' => $report]);
    }

    public function solveReport($report_id)
    {
        $user = Auth::user();

        $v = Validator::make(Request::all(), [
            'verdict' => 'required|min:3',
            'staff_id' => 'required'
        ]);

        $report = Report::findOrFail($report_id);

        if ($report->solved == 1) {
            return redirect()->route('getReports')->with(Toastr::error('This Report Has Already Been Solved', 'Whoops!', ['options']));
        }

        $report->verdict = Request::get('verdict');
        $report->staff_id = $user->id;
        $report->solved = 1;
        $report->save();

        // Insert the Recipient notification below
        PrivateMessage::create(['sender_id' => $user->id, 'reciever_id' => $report->reporter_id, 'subject' => "Your Report Has A New Verdict", 'message' => $report->verdict]);
        // End insert recipient notification here

        return redirect()->route('getReports')->with(Toastr::success('Report has been successfully resolved', 'Success!', ['options']));
    }
}
