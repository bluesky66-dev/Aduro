<?php
/**
 * NOTICE OF LICENSE.
 *
 * UNIT3D is open-sourced software licensed under the GNU Affero General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D
 *
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 * @author     HDVinnie
 */

namespace App\Http\Controllers\Staff;

use App\Models\Like;
use App\Models\Note;
use App\Models\Peer;
use App\Models\Post;
use App\Models\User;
use App\Models\Group;
use App\Models\Thank;
use App\Models\Topic;
use App\Models\Follow;
use App\Models\Invite;
use App\Models\Comment;
use App\Models\Message;
use App\Models\Torrent;
use Illuminate\Http\Request;
use App\Models\PrivateMessage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Users List.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $users = User::with('group')->latest()->paginate(25);
        $uploaders = User::with('group')->where('group_id', '=', 7)->latest()->paginate(25);
        $mods = User::with('group')->where('group_id', '=', 6)->latest()->paginate(25);
        $admins = User::with('group')->where('group_id', '=', 4)->latest()->paginate(25);
        $coders = User::with('group')->where('group_id', '=', 10)->latest()->paginate(25);

        return view('Staff.user.user_search', [
            'users'     => $users,
            'uploaders' => $uploaders,
            'mods'      => $mods,
            'admins'    => $admins,
            'coders'    => $coders,
        ]);
    }

    /**
     * Search For A User.
     *
     * @param  Request  $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function search(Request $request)
    {
        $users = User::where([
            ['username', 'like', '%'.$request->input('username').'%'],
        ])->paginate(25);
        $users->setPath('?username='.$request->input('username'));

        return view('Staff.user.user_results', ['users' => $users]);
    }

    /**
     * User Edit Form.
     *
     * @param $username
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function settings($username)
    {
        $user = User::where('username', '=', $username)->firstOrFail();
        $groups = Group::all();
        $notes = Note::where('user_id', '=', $user->id)->latest()->paginate(25);

        return view('Staff.user.user_edit', [
            'user'   => $user,
            'groups' => $groups,
            'notes'  => $notes,
        ]);
    }

    /**
     * Edit A User.
     *
     * @param \Illuminate\Http\Request $request
     * @param $username
     *
     * @@return Illuminate\Http\RedirectResponse
     */
    public function edit(Request $request, $username)
    {
        $user = User::with('group')->where('username', '=', $username)->firstOrFail();
        $staff = $request->user();

        $sendto = (int) $request->input('group_id');

        $weights = [
            'is_modo',
            'is_admin',
        ];

        $sender = -1;
        $target = -1;
        foreach ($weights as $pos => $weight) {
            if ($user->group->$weight && $user->group->$weight == 1) {
                $target = $pos;
            }
            if ($staff->group->$weight && $staff->group->$weight == 1) {
                $sender = $pos;
            }
        }

        if ($target == 1 && $user->group->id == 10) {
            $target = 2;
        }
        if ($sender == 1 && $staff->group->id == 10) {
            $sender = 2;
        }

        // Hard coded until group change.

        if ($target >= $sender || ($sender == 0 && ($sendto == 6 || $sendto == 4 || $sendto == 10)) || ($sender == 1 && ($sendto == 4 || $sendto == 10))) {
            return redirect()->route('users.show', ['username' => $user->username])
                ->withErrors('You Are Not Authorized To Perform This Action!');
        }

        $user->username = $request->input('username');
        $user->email = $request->input('email');
        $user->uploaded = $request->input('uploaded');
        $user->downloaded = $request->input('downloaded');
        $user->title = $request->input('title');
        $user->about = $request->input('about');
        $user->group_id = (int) $request->input('group_id');
        $user->save();

        // Activity Log
        \LogActivity::addToLog("Staff Member {$staff->username} has edited {$user->username} account.");

        return redirect()->route('users.show', ['username' => $user->username])
            ->withSuccess('Account Was Updated Successfully!');
    }

    /**
     * Edit A Users Permissions.
     *
     * @param \Illuminate\Http\Request $request
     * @param $username
     *
     * @return Illuminate\Http\RedirectResponse
     */
    public function permissions(Request $request, $username)
    {
        $user = User::where('username', '=', $username)->firstOrFail();
        $staff = $request->user();

        $user->can_upload = $request->input('can_upload');
        $user->can_download = $request->input('can_download');
        $user->can_comment = $request->input('can_comment');
        $user->can_invite = $request->input('can_invite');
        $user->can_request = $request->input('can_request');
        $user->can_chat = $request->input('can_chat');
        $user->save();

        // Activity Log
        \LogActivity::addToLog("Staff Member {$staff->username} has edited {$user->username} account permissions.");

        return redirect()->route('users.show', ['username' => $user->username])
            ->withSuccess('Account Permissions Successfully Edited');
    }

    /**
     * Edit A Users Password.
     *
     * @param \Illuminate\Http\Request $request
     * @param $username
     *
     * @return Illuminate\Http\RedirectResponse
     */
    protected function password(Request $request, $username)
    {
        $user = User::where('username', '=', $username)->firstOrFail();
        $staff = auth()->user();

        $new_password = $request->input('new_password');
        $user->password = Hash::make($new_password);
        $user->save();

        // Activity Log
        \LogActivity::addToLog("Staff Member {$staff->username} has changed {$user->username} password.");

        return redirect()->route('users.show', ['username' => $user->username])
            ->withSuccess('Account Password Was Updated Successfully!');
    }

    /**
     * Delete A User.
     *
     * @param $username
     *
     * @return Illuminate\Http\RedirectResponse
     */
    protected function destroy($username)
    {
        $user = User::where('username', '=', $username)->firstOrFail();
        $staff = auth()->user();

        abort_if($user->group->is_modo || auth()->user()->id == $user->id, 403);

        // Removes UserID from Torrents if any and replaces with System UserID (1)
        foreach (Torrent::withAnyStatus()->where('user_id', '=', $user->id)->get() as $tor) {
            $tor->user_id = 1;
            $tor->save();
        }
        // Removes UserID from Comments if any and replaces with System UserID (1)
        foreach (Comment::where('user_id', '=', $user->id)->get() as $com) {
            $com->user_id = 1;
            $com->save();
        }
        // Removes UserID from Posts if any and replaces with System UserID (1)
        foreach (Post::where('user_id', '=', $user->id)->get() as $post) {
            $post->user_id = 1;
            $post->save();
        }
        // Removes UserID from Topic Creators if any and replaces with System UserID (1)
        foreach (Topic::where('first_post_user_id', '=', $user->id)->get() as $topic) {
            $topic->first_post_user_id = 1;
            $topic->save();
        }
        // Removes UserID from Topic if any and replaces with System UserID (1)
        foreach (Topic::where('last_post_user_id', '=', $user->id)->get() as $topic) {
            $topic->last_post_user_id = 1;
            $topic->save();
        }
        // Removes UserID from PM if any and replaces with System UserID (1)
        foreach (PrivateMessage::where('sender_id', '=', $user->id)->get() as $sent) {
            $sent->sender_id = 1;
            $sent->save();
        }
        // Removes UserID from PM if any and replaces with System UserID (1)
        foreach (PrivateMessage::where('receiver_id', '=', $user->id)->get() as $received) {
            $received->receiver_id = 1;
            $received->save();
        }
        // Removes all Posts made by User from the shoutbox
        foreach (Message::where('user_id', '=', $user->id)->get() as $shout) {
            $shout->delete();
        }
        // Removes all notes for user
        foreach (Note::where('user_id', '=', $user->id)->get() as $note) {
            $note->delete();
        }
        // Removes all likes for user
        foreach (Like::where('user_id', '=', $user->id)->get() as $like) {
            $like->delete();
        }
        // Removes all thanks for user
        foreach (Thank::where('user_id', '=', $user->id)->get() as $thank) {
            $thank->delete();
        }
        // Removes all follows for user
        foreach (Follow::where('user_id', '=', $user->id)->get() as $follow) {
            $follow->delete();
        }
        // Removes UserID from Sent Invites if any and replaces with System UserID (1)
        foreach (Invite::where('user_id', '=', $user->id)->get() as $sent_invite) {
            $sent_invite->user_id = 1;
            $sent_invite->save();
        }
        // Removes UserID from Received Invite if any and replaces with System UserID (1)
        foreach (Invite::where('accepted_by', '=', $user->id)->get() as $received_invite) {
            $received_invite->accepted_by = 1;
            $received_invite->save();
        }
        // Removes all Peers for user
        foreach (Peer::where('user_id', '=', $user->id)->get() as $peer) {
            $peer->delete();
        }
        // Activity Log
        \LogActivity::addToLog("Staff Member {$staff->username} has deleted {$user->username} account.");

        if ($user->delete()) {
            return redirect()->route('staff.dashboard.index')
                ->withSuccess('Account Has Been Removed');
        } else {
            return redirect()->route('staff.dashboard.index')
                ->withErrors('Something Went Wrong!');
        }
    }
}
