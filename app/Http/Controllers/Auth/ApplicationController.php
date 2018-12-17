<?php
/**
 * NOTICE OF LICENSE.
 *
 * UNIT3D is open-sourced software licensed under the GNU General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 * @author     HDVinnie
 */

namespace App\Http\Controllers\Auth;

use App\Application;
use Illuminate\Http\Request;
use App\ApplicationUrlProof;
use Brian2694\Toastr\Toastr;
use App\ApplicationImageProof;
use App\Http\Controllers\Controller;

class ApplicationController extends Controller
{
    /**
     * @var Toastr
     */
    private $toastr;

    /**
     * ApplicationController Constructor.
     *
     * @param Toastr $toastr
     */
    public function __construct(Toastr $toastr)
    {
        $this->toastr = $toastr;
    }

    /**
     * Application Add Form.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('auth.application.create');
    }

    /**
     * Add A Application.
     *
     * @param \Illuminate\Http\Request $request
     * @return Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $application = new Application();
        $application->type = $request->input('type');
        $application->email = $request->input('email');
        $application->referer = $request->input('referer');

        if (config('email-white-blacklist.enabled') === 'allow') {
            $v = validator($application->toArray(), [
                'type' => 'required',
                'email' => 'required|email|unique:invites|unique:users|unique:applications|email_list:allow',
                'referer' => 'required'
            ]);
        } elseif (config('email-white-blacklist.enabled') === 'block') {
            $v = validator($application->toArray(), [
                'type' => 'required',
                'email' => 'required|email|unique:invites|unique:users|unique:applications|email_list:block',
                'referer' => 'required'
            ]);
        } else {
            $v = validator($application->toArray(), [
                'type' => 'required',
                'email' => 'required|email|unique:invites|unique:users|unique:applications',
                'referer' => 'required'
            ]);
        }

        if ($v->fails()) {
            return redirect()->route('create_application')
                ->with($this->toastr->error($v->errors()->toJson(), 'Whoops!', ['options']));
        } else {

            // Map And Save IMG Proofs
            $imgs = collect($request->input('img_proofs'))->map(function ($value) {
                return new ApplicationImageProof(['img' => $value]);
            });
            $application->imageProofs()->saveMany($imgs);

            // Map And Save URL Proofs
            $urls = collect($request->input('url_proofs'))->map(function ($value) {
                return new ApplicationUrlProof(['url' => $value]);
            });
            $application->urlProofs()->saveMany($urls);

            $application->save();

            return redirect()->route('login')
                ->with($this->toastr->success('Your Application Has Been Submitted. You will receive a email soon!', 'Yay!', ['options']));
        }
    }

    /**
     * Get A Application (User Can Check Status).
     *
     * @param  $email
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($email)
    {
        // Coming Soon!
        return view('auth.application.application');
    }
}
