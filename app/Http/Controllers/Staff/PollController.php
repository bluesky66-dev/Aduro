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

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\User;
use App\Poll;
use App\Option;
use App\Shoutbox;
use App\Http\Requests\StorePoll;

use Cache;
use \Toastr;

class PollController extends Controller
{
    public function polls()
    {
        $polls = Poll::orderBy('created_at', 'desc')->paginate(20);
        return view('Staff.poll.polls', compact('polls'));
    }

    public function poll($id)
    {
        $poll = Poll::where('id',$id)->firstOrFail();
        return view('Staff.poll.poll', compact('poll'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('Staff.poll.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePoll $request)
    {
        if (\Auth::check()) {
            $poll = \Auth::user()->polls()->create($request->all());
        } else {
            $poll = Poll::create($request->all());
        }

        $options = collect($request->input('options'))->map(function($value){
                    return new Option(['name' => $value]);
                });
        $poll->options()->saveMany($options);

        // Activity Log
        \LogActivity::addToLog("Staff Member " . Auth::user()->username . " has created a new poll ". $poll->title ." .");

        // Auto Shout
        Shoutbox::create(['user' => "0", 'mentions' => "0", 'message' => "A new poll has been created [url=https://blutopia.xyz/poll/" .$poll->slug."]" .$poll->title. "[/url] vote on it now! :slight_smile:"]);
        Cache::forget('shoutbox_messages');

        Toastr::success('Your poll has been created.', 'Yay!', ['options']);

        return redirect('poll/' . $poll->slug);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
