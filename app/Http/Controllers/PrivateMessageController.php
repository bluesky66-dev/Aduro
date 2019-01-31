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
use App\PrivateMessage;
use Brian2694\Toastr\Toastr;
use Illuminate\Http\Request;

class PrivateMessageController extends Controller
{
    /**
     * @var Toastr
     */
    private $toastr;

    /**
     * PrivateMessageController Constructor.
     *
     * @param Toastr $toastr
     */
    public function __construct(Toastr $toastr)
    {
        $this->toastr = $toastr;
    }

    /**
     * Search PM Inbox.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function searchPMInbox(Request $request)
    {
        $user = auth()->user();
        $pms = PrivateMessage::where('receiver_id', '=', $user->id)->where([
            ['subject', 'like', '%'.$request->input('subject').'%'],
        ])->latest()->paginate(20);

        return view('pm.inbox', ['pms' => $pms, 'user' => $user]);
    }

    /**
     * Search PM Outbox.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function searchPMOutbox(Request $request)
    {
        $user = auth()->user();
        $pms = PrivateMessage::where('sender_id', '=', $user->id)->where([
            ['subject', 'like', '%'.$request->input('subject').'%'],
        ])->latest()->paginate(20);

        return view('pm.outbox', ['pms' => $pms, 'user' => $user]);
    }

    /**
     * View Inbox.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getPrivateMessages()
    {
        $user = auth()->user();
        $pms = PrivateMessage::where('receiver_id', '=', $user->id)->latest()->paginate(25);

        return view('pm.inbox', ['pms' => $pms, 'user' => $user]);
    }

    /**
     * View Outbox.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getPrivateMessagesSent()
    {
        $user = auth()->user();
        $pms = PrivateMessage::where('sender_id', '=', $user->id)->latest()->paginate(20);

        return view('pm.outbox', ['pms' => $pms, 'user' => $user]);
    }

    /**
     * View A Message.
     *
     * @param $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getPrivateMessageById($id)
    {
        $user = auth()->user();
        $pm = PrivateMessage::where('id', '=', $id)->firstOrFail();

        if ($pm->sender_id == $user->id || $pm->receiver_id == $user->id) {
            if ($user->id === $pm->receiver_id && $pm->read === 0) {
                $pm->read = 1;
                $pm->save();
            }

            return view('pm.message', ['pm' => $pm, 'user' => $user]);
        } else {
            return redirect()->route('inbox')
                ->with($this->toastr->error('What Are You Trying To Do Here!', 'Whoops!', ['options']));
        }
    }

    /**
     * Create Message Form.
     *
     * @param $receiver_id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function makePrivateMessage($receiver_id = '', $username = '')
    {
        $user = auth()->user();
        $usernames = User::oldest('username')->get();

        return view('pm.send', ['usernames' => $usernames, 'user' => $user, 'receiver_id' => $receiver_id,
            'username'                      => $username, ]);
    }

    /**
     * Create A Message.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return Illuminate\Http\RedirectResponse
     */
    public function sendPrivateMessage(Request $request)
    {
        $user = auth()->user();

        $dest = 'default';
        if ($request->has('dest') && $request->input('dest') == 'profile') {
            $dest = 'profile';
        }

        $pm = new PrivateMessage();
        $pm->sender_id = $user->id;
        $pm->receiver_id = $request->input('receiver_id');
        $pm->subject = $request->input('subject');
        $pm->message = $request->input('message');
        $pm->read = 0;

        $v = validator($pm->toArray(), [
            'sender_id'   => 'required',
            'receiver_id' => 'required',
            'subject'     => 'required',
            'message'     => 'required',
            'read'        => 'required',
        ]);

        if ($request->has('receiver_id')) {
            $recipient = User::where('id', '=', (int) $request->input('receiver_id'))->firstOrFail();
        } else {
            return redirect()->route('create', ['username' => auth()->user()->username, 'id' => auth()->user()->id])
                ->with($this->toastr->error($v->errors()->toJson(), 'Whoops!', ['options']));
        }

        if ($v->fails()) {
            if ($dest == 'profile') {
                return redirect()->route('profile', ['username' => $recipient->slug, 'id'=> $recipient->id])
                    ->with($this->toastr->error($v->errors()->toJson(), 'Whoops!', ['options']));
            }

            return redirect()->route('create', ['username' => auth()->user()->username, 'id' => auth()->user()->id])
                ->with($this->toastr->error($v->errors()->toJson(), 'Whoops!', ['options']));
        } else {
            $pm->save();
            if ($dest == 'profile') {
                return redirect()->route('profile', ['username' => $recipient->slug, 'id'=> $recipient->id])
                    ->with($this->toastr->success('Your PM Was Sent Successfully!', 'Yay!', ['options']));
            }

            return redirect()->route('inbox')
                ->with($this->toastr->success('Your PM Was Sent Successfully!', 'Yay!', ['options']));
        }
    }

    /**
     * Reply To A Message.
     *
     * @param \Illuminate\Http\Request $request
     * @param $id
     *
     * @return Illuminate\Http\RedirectResponse
     */
    public function replyPrivateMessage(Request $request, $id)
    {
        $user = auth()->user();

        $message = PrivateMessage::where('id', '=', $id)->firstOrFail();

        $pm = new PrivateMessage();
        $pm->sender_id = $user->id;
        $pm->receiver_id = $message->sender_id;
        $pm->subject = $message->subject;
        $pm->message = $request->input('message');
        $pm->related_to = $message->id;
        $pm->read = 0;

        $v = validator($pm->toArray(), [
            'sender_id'   => 'required',
            'receiver_id' => 'required',
            'subject'     => 'required',
            'message'     => 'required',
            'related_to'  => 'required',
            'read'        => 'required',
        ]);

        if ($v->fails()) {
            return redirect()->route('inbox')
                ->with($this->toastr->error($v->errors()->toJson(), 'Whoops!', ['options']));
        } else {
            $pm->save();

            return redirect()->route('inbox')
                ->with($this->toastr->success('Your PM Was Sent Successfully!', 'Yay!', ['options']));
        }
    }

    /**
     * Delete A Message.
     *
     * @param $id
     *
     * @return Illuminate\Http\RedirectResponse
     */
    public function deletePrivateMessage($id)
    {
        $user = auth()->user();
        $pm = PrivateMessage::where('id', '=', $id)->firstOrFail();

        if ($pm->sender_id == $user->id || $pm->receiver_id == $user->id) {
            $pm->delete();

            return redirect()->route('inbox')
                ->with($this->toastr->success('PM Was Deleted Successfully!', 'Yay!', ['options']));
        } else {
            return redirect()->route('inbox')
                ->with($this->toastr->error('What Are You Trying To Do Here!', 'Whoops!', ['options']));
        }
    }

    /**
     * Mark All Messages As Read.
     *
     * @return Illuminate\Http\RedirectResponse
     */
    public function markAllAsRead()
    {
        $user = auth()->user();
        $pms = PrivateMessage::where('receiver_id', '=', $user->id)->get();
        foreach ($pms as $pm) {
            $pm->read = 1;
            $pm->save();
        }

        return redirect()->route('inbox')
            ->with($this->toastr->success('Your Messages Have All Been Marked As Read!', 'Yay!', ['options']));
    }
}
