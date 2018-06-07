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

use App\Repositories\ChatRepository;
use Illuminate\Http\Request;
use App\Http\Requests\VoteOnPoll;
use App\Poll;
use App\Option;
use App\Voter;
use \Toastr;

class PollController extends Controller
{
    /**
     * @var ChatRepository
     */
    private $chat;

    public function __construct(ChatRepository $chat)
    {
        $this->chat = $chat;
    }

    /**
     * Show All Polls
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $polls = Poll::latest()->paginate(15);

        return view('poll.latest', ['polls' => $polls]);
    }

    /**
     * Show A Poll
     *
     * @param $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($slug)
    {
        $poll = Poll::whereSlug($slug)->firstOrFail();
        $user = auth()->user();
        $user_has_voted = $poll->voters->where('user_id', $user->id)->isNotEmpty();

        if ($user_has_voted) {
            return redirect('poll/' . $poll->slug . '/result')
                ->with(Toastr::info('You have already vote on this poll. Here are the results.', 'Hey There!', ['options']));
        }

        return view('poll.show', compact('poll'));
    }

    /**
     * Vote On A Poll
     *
     * @param VoteOnPoll $request
     * @return Illuminate\Http\RedirectResponse
     */
    public function vote(VoteOnPoll $request)
    {
        $user = auth()->user();
        $poll = Option::findOrFail($request->input('option.0'))->poll;

        foreach ($request->input('option') as $option) {
            Option::findOrFail($option)->increment('votes');
        }

        if (Voter::where('user_id', $user->id)->where('poll_id', $poll->id)->exists()) {
            return redirect('poll/' . $poll->slug . '/result')
                ->with(Toastr::error('Bro have already vote on this poll. Your vote has not been counted.', 'Whoops!', ['options']));
        }

        if ($poll->ip_checking == 1) {
            $vote = new Voter();
            $vote->poll_id = $poll->id;
            $vote->user_id = $user->id;
            $vote->ip_address =$request->ip();
            $vote->save();
        }

        $poll_url = hrefPoll($poll);
        $profile_url = hrefProfile($user);

        $this->chat->systemMessage(
            "[url={$profile_url}]{$user->username}[/url] has voted on poll [url={$poll_url}]{$poll->title}[/url]"
        );

        return redirect('poll/' . $poll->slug . '/result')
            ->with(Toastr::success('Your vote has been counted.', 'Yay!', ['options']));
    }

    /**
     * Show A Polls Results
     *
     * @param $slug
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function result($slug)
    {
        $poll = Poll::whereSlug($slug)->firstOrFail();
        $map = [
            'poll' => $poll,
            'total_votes' => $poll->totalVotes()
        ];

        return view('poll.result', $map);
    }
}
