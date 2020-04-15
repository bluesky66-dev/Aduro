<?php
/**
 * NOTICE OF LICENSE.
 *
 * UNIT3D Community Edition is open-sourced software licensed under the GNU Affero General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D Community Edition
 *
 * @author     HDVinnie <hdinnovations@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Http\Controllers;

use App\Mail\InviteUser;
use App\Models\Invite;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Ramsey\Uuid\Uuid;

class InviteController extends Controller
{
    /**
     * Invite Tree.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User         $username
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request, $username)
    {
        $user = $request->user();
        $owner = User::where('username', '=', $username)->firstOrFail();
        abort_unless($user->group->is_modo || $user->id === $owner->id, 403);

        $invites = Invite::with(['sender', 'receiver'])->where('user_id', '=', $owner->id)->latest()->paginate(25);

        return view('user.invites', ['owner' => $owner, 'invites' => $invites, 'route' => 'invite']);
    }

    /**
     * Invite Form.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request)
    {
        $user = $request->user();

        if (config('other.invite-only') == false) {
            return redirect()->route('home.index')
            ->withErrors('Invitations Are Disabled Due To Open Registration!');
        }
        if ($user->can_invite == 0) {
            return redirect()->route('home.index')
            ->withErrors('Your Invite Rights Have Been Revoked!');
        }
        if (config('other.invites_restriced') == true && ! in_array($user->group->name, config('other.invite_groups'))) {
            return redirect()->route('home.index')
                ->withErrors('Invites are currently disabled for your group.');
        }

        return view('user.invite', ['user' => $user, 'route' => 'invite']);
    }

    /**
     * Send Invite.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Exception
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $current = new Carbon();
        $user = $request->user();

        if (config('other.invites_restriced') == true && ! in_array($user->group->name, config('other.invite_groups'))) {
            return redirect()->route('home.index')
                ->withErrors('Invites are currently disabled for your group.');
        }

        if ($user->invites <= 0) {
            return redirect()->route('invites.create')
                ->withErrors('You do not have enough invites!');
        }

        $exist = Invite::where('email', '=', $request->input('email'))->first();

        if ($exist) {
            return redirect()->route('invites.create')
                ->withErrors('The email address your trying to send a invite to has already been sent one.');
        }

        $code = Uuid::uuid4()->toString();
        $invite = new Invite();
        $invite->user_id = $user->id;
        $invite->email = $request->input('email');
        $invite->code = $code;
        $invite->expires_on = $current->copy()->addDays(config('other.invite_expire'));
        $invite->custom = $request->input('message');

        if (config('email-white-blacklist.enabled') === 'allow') {
            $v = validator($invite->toArray(), [
                'email'  => 'required|email|unique:users|email_list:allow', // Whitelist
                'custom' => 'required',
            ]);
        } elseif (config('email-white-blacklist.enabled') === 'block') {
            $v = validator($invite->toArray(), [
                'email'  => 'required|email|unique:users|email_list:block', // Blacklist
                'custom' => 'required',
            ]);
        } else {
            $v = validator($invite->toArray(), [
                'email'  => 'required|email|unique:users', // Default
                'custom' => 'required',
            ]);
        }

        if ($v->fails()) {
            return redirect()->route('invites.create')
                ->withErrors($v->errors());
        }
        Mail::to($request->input('email'))->send(new InviteUser($invite));
        $invite->save();
        $user->invites -= 1;
        $user->save();

        return redirect()->route('invites.create')
            ->withSuccess('Invite was sent successfully!');
    }

    /**
     * Resend Invite.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Invite       $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function send(Request $request, $id)
    {
        $user = $request->user();
        $invite = Invite::findOrFail($id);

        abort_unless($invite->user_id === $user->id, 403);

        if ($invite->accepted_by !== null) {
            return redirect()->route('invites.index', ['username' => $user->username])
                ->withErrors('The invite you are trying to resend has already been used.');
        }

        Mail::to($invite->email)->send(new InviteUser($invite));

        return redirect()->route('invites.index', ['username' => $user->username])
            ->withSuccess('Invite was resent successfully!');
    }
}
