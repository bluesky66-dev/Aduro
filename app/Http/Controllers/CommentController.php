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
 
namespace App\Http\Controllers;

use App\User;
use App\Article;
use App\Comment;
use App\Torrent;
use App\Requests;
use App\Shoutbox;
use App\PrivateMessage;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;

use \Toastr;
use Cache;

use App\Achievements\UserMadeComment;
use App\Achievements\UserMadeTenComments;
use App\Achievements\UserMade50Comments;
use App\Achievements\UserMade100Comments;
use App\Achievements\UserMade200Comments;
use App\Achievements\UserMade300Comments;
use App\Achievements\UserMade400Comments;
use App\Achievements\UserMade500Comments;
use App\Achievements\UserMade600Comments;
use App\Achievements\UserMade700Comments;
use App\Achievements\UserMade800Comments;
use App\Achievements\UserMade900Comments;

use App\Notifications\NewTorrentComment;

class CommentController extends Controller
{

    /**
    * Add a comment on a artical
    *
    * @param $slug
    * @param $id
    *
    */
    public function article($slug, $id)
    {
        $article = Article::findOrFail($id);
        $user = Auth::user();

        // User's comment rights disbabled?
        if ($user->can_comment == 0) {
            return Redirect::route('article', ['slug' => $article->slug, 'id' => $article->id])->with(Toastr::warning('Your Comment Rights Have Benn Revoked!!!', 'Error!', ['options']));
        }

        $comment = new Comment();
        $comment->content = Request::get('content');
        $comment->user_id = $user->id;
        $comment->article_id = $article->id;
        $v = Validator::make($comment->toArray(), ['content' => 'required', 'user_id' => 'required', 'article_id' => 'required']);
        $appurl = env('APP_URL', 'http://unit3d.site');
        if ($v->passes()) {
            Shoutbox::create(['user' => "1", 'mentions' => "1", 'message' => "User [url={$appurl}/" . $user->username . "." . $user->id . "]" . $user->username . "[/url] has left a comment on article [url={$appurl}/articles/" . $article->slug . "." . $article->id . "]" . $article->title . "[/url]"]);
            Cache::forget('shoutbox_messages');
            $comment->save();
            Toastr::success('Your Comment Has Been Added!', 'Yay!', ['options']);
        } else {
            Toastr::warning('A Error Has Occured And Your Comment Was Not Posted!', 'Sorry', ['options']);
        }
        return Redirect::route('article', ['slug' => $article->slug, 'id' => $article->id]);
    }

    /**
    * Add a comment on a torrent
    *
    * @param $slug
    * @param $id
    */
    public function torrent($slug, $id)
    {
        $torrent = Torrent::findOrFail($id);
        $user = Auth::user();

        // User's comment rights disbabled?
        if ($user->can_comment == 0) {
            return Redirect::route('torrent', ['slug' => $torrent->slug, 'id' => $torrent->id])->with(Toastr::warning('Your Comment Rights Have Benn Revoked!!!', 'Error!', ['options']));
        }

        $comment = new Comment();
        $comment->content = Request::get('content');
        $comment->anon = Request::get('anonymous');
        $comment->user_id = $user->id;
        $comment->torrent_id = $torrent->id;
        $v = Validator::make($comment->toArray(), ['content' => 'required', 'user_id' => 'required', 'torrent_id' => 'required', 'anon' => 'required']);
        if ($v->passes()) {
            $comment->save();
            Toastr::success('Your Comment Has Been Added!', 'Yay!', ['options']);
            // Achievements
            $user->unlock(new UserMadeComment(), 1);
            $user->addProgress(new UserMadeTenComments(), 1);
            $user->addProgress(new UserMade50Comments(), 1);
            $user->addProgress(new UserMade100Comments(), 1);
            $user->addProgress(new UserMade200Comments(), 1);
            $user->addProgress(new UserMade300Comments(), 1);
            $user->addProgress(new UserMade400Comments(), 1);
            $user->addProgress(new UserMade500Comments(), 1);
            $user->addProgress(new UserMade600Comments(), 1);
            $user->addProgress(new UserMade700Comments(), 1);
            $user->addProgress(new UserMade800Comments(), 1);
            $user->addProgress(new UserMade900Comments(), 1);

            //Notification
            User::find($torrent->user_id)->notify(new NewTorrentComment($comment));

            // Auto Shout
            $appurl = env('APP_URL', 'http://unit3d.site');
            if ($comment->anon == 0){
                Shoutbox::create(['user' => "1", 'mentions' => "1", 'message' => "User [url={$appurl}/" . $user->username . "." . $user->id . "]" . $user->username . "[/url] has left a comment on Torrent [url={$appurl}/torrents/" . $torrent->slug . "." . $torrent->id . "]" . $torrent->name . "[/url]"]);
            Cache::forget('shoutbox_messages');
            } else {
                Shoutbox::create(['user' => "1", 'mentions' => "1", 'message' => "User Anonymous has left a comment on Torrent [url={$appurl}/torrents/" . $torrent->slug . "." . $torrent->id . "]" . $torrent->name . "[/url]"]);
                Cache::forget('shoutbox_messages');
            }
        } else {
            Toastr::warning('A Error Has Occured And Your Comment Was Not Posted!', 'Sorry', ['options']);
        }
        return Redirect::route('torrent', ['slug' => $torrent->slug, 'id' => $torrent->id]);
    }

    /**
    * Add a comment on a request
    *
    * @param $slug
    * @param $id
    */
    public function request($id)
    {
        $request = Requests::findOrFail($id);
        $user = Auth::user();

        // User's comment rights disbabled?
        if ($user->can_comment == 0) {
            return Redirect::route('request', ['id' => $request->id])->with(Toastr::warning('Your Comment Rights Have Benn Revoked!!!', 'Error!', ['options']));
        }

        $comment = new Comment();
        $comment->content = Request::get('content');
        $comment->user_id = $user->id;
        $comment->requests_id = $request->id;
        $v = Validator::make($comment->toArray(), ['content' => 'required', 'user_id' => 'required', 'requests_id' => 'required']);
        if ($v->passes()) {
            $comment->save();
            Toastr::success('Your Comment Has Been Added!', 'Yay!', ['options']);
            // Achievements
            $user->unlock(new UserMadeComment(), 1);
            $user->addProgress(new UserMadeTenComments(), 1);
            $user->addProgress(new UserMade50Comments(), 1);
            $user->addProgress(new UserMade100Comments(), 1);
            $user->addProgress(new UserMade200Comments(), 1);
            $user->addProgress(new UserMade300Comments(), 1);
            $user->addProgress(new UserMade400Comments(), 1);
            $user->addProgress(new UserMade500Comments(), 1);
            $user->addProgress(new UserMade600Comments(), 1);
            $user->addProgress(new UserMade700Comments(), 1);
            $user->addProgress(new UserMade800Comments(), 1);
            $user->addProgress(new UserMade900Comments(), 1);
            // Auto Shout
            $appurl = env('APP_URL', 'http://unit3d.site');
            Shoutbox::create(['user' => "1", 'mentions' => "1", 'message' => "User [url={$appurl}/" . $user->username . "." . $user->id . "]" . $user->username . "[/url] has left a comment on Request [url={$appurl}/request/" . $request->id . "]" . $request->name . "[/url]"]);
              Cache::forget('shoutbox_messages');
            // Auto PM
            PrivateMessage::create(['sender_id' => "0", 'reciever_id' => $request->user_id, 'subject' => "Your Request " . $request->name . " Has A New Comment!", 'message' => $comment->user->username . " Has Left A Comment On [url={$appurl}/request/" . $request->id . "]" . $request->name . "[/url]"]);
        } else {
            Toastr::warning('A Error Has Occured And Your Comment Was Not Posted!', 'Sorry', ['options']);
        }
        return Redirect::route('request', ['id' => $request->id]);
    }

    /**
    * Add a comment on a torrent via quickthanks
    *
    * @param $slug
    * @param $id
    */
    public function quickthanks($id)
    {
        $torrent = Torrent::findOrFail($id);
        $user = Auth::user();
        $uploader = $torrent->user;

        // User's comment rights disbabled?
        if ($user->can_comment == 0) {
            return Redirect::route('torrent', ['slug' => $torrent->slug, 'id' => $torrent->id])->with(Toastr::warning('Your Comment Rights Have Benn Revoked!!!', 'Error!', ['options']));
        }

        $comment = new Comment();
        $thankArray = ["Thanks for the upload! :thumbsup_tone2:","Time and effort is much appreciated :thumbsup_tone2:","Great upload! :fire:","Thankyou :smiley:"];
        $selected = mt_rand(0, count($thankArray) -1);
        $comment->content = $thankArray[$selected];
        $comment->user_id = $user->id;
        $comment->torrent_id = $torrent->id;
        $v = Validator::make($comment->toArray(), ['content' => 'required', 'user_id' => 'required', 'torrent_id' => 'required']);
        if ($v->passes()) {
            $comment->save();
            Toastr::success('Your Comment Has Been Added!', 'Yay!', ['options']);
            // Achievements
            $user->unlock(new UserMadeComment(), 1);
            $user->addProgress(new UserMadeTenComments(), 1);
            $user->addProgress(new UserMade50Comments(), 1);
            $user->addProgress(new UserMade100Comments(), 1);
            $user->addProgress(new UserMade200Comments(), 1);
            $user->addProgress(new UserMade300Comments(), 1);
            $user->addProgress(new UserMade400Comments(), 1);
            $user->addProgress(new UserMade500Comments(), 1);
            $user->addProgress(new UserMade600Comments(), 1);
            $user->addProgress(new UserMade700Comments(), 1);
            $user->addProgress(new UserMade800Comments(), 1);
            $user->addProgress(new UserMade900Comments(), 1);

            //Notification
            User::find($torrent->user_id)->notify(new NewTorrentComment($comment));

            // Auto Shout
            $appurl = env('APP_URL', 'http://unit3d.site');
            Shoutbox::create(['user' => "1", 'mentions' => "1", 'message' => "User [url={$appurl}/" . $user->username . "." . $user->id . "]" . $user->username . "[/url] has left a comment on Torrent [url={$appurl}/torrents/" . $torrent->slug . "." . $torrent->id . "]" . $torrent->name . "[/url]"]);
            Cache::forget('shoutbox_messages');

            } else {
            Toastr::warning('A Error Has Occured And Your Comment Was Not Posted!', 'Sorry', ['options']);
            }

        return Redirect::route('torrent', ['slug' => $torrent->slug, 'id' => $torrent->id]);
    }


    /**
    * Delete a comment on a torrent
    *
    *
    * @param $comment_id
    */
    public function deleteComment($comment_id)
    {
    $comment = Comment::findOrFail($comment_id);

    if(!$comment){
        return back()->with(Toastr::error('Comment Is Already Deleted', 'Attention', ['options']));
    }

    $comment->delete();

    return back()->with(Toastr::success('Comment Has Been Deleted.', 'Yay!', ['options']));
  }

}
