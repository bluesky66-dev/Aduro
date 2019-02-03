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
use App\Article;
use App\Comment;
use App\Torrent;
use App\TorrentRequest;
use Brian2694\Toastr\Toastr;
use Illuminate\Http\Request;
use App\Notifications\NewComment;
use App\Repositories\ChatRepository;
use App\Achievements\UserMadeComment;
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
use App\Achievements\UserMadeTenComments;
use App\Repositories\TaggedUserRepository;

class CommentController extends Controller
{
    /**
     * @var TaggedUserRepository
     */
    private $tag;

    /**
     * @var ChatRepository
     */
    private $chat;

    /**
     * @var Toastr
     */
    private $toastr;

    /**
     * CommentController Constructor.
     *
     * @param TaggedUserRepository $tag
     * @param ChatRepository       $chat
     * @param Toastr               $toastr
     */
    public function __construct(TaggedUserRepository $tag, ChatRepository $chat, Toastr $toastr)
    {
        $this->tag = $tag;
        $this->chat = $chat;
        $this->toastr = $toastr;
    }

    /**
     * Add A Comment To A Article.
     *
     * @param \Illuminate\Http\Request $request
     * @param $slug
     * @param $id
     *
     * @return Illuminate\Http\RedirectResponse
     */
    public function article(Request $request, $slug, $id)
    {
        $article = Article::findOrFail($id);
        $user = auth()->user();

        if ($user->can_comment == 0) {
            return redirect()->route('article', ['slug' => $article->slug, 'id' => $article->id])
                ->with($this->toastr->error('Your Comment Rights Have Benn Revoked!!!', 'Whoops!', ['options']));
        }

        $comment = new Comment();
        $comment->content = $request->input('content');
        $comment->anon = $request->input('anonymous');
        $comment->user_id = $user->id;
        $comment->article_id = $article->id;

        $v = validator($comment->toArray(), [
            'content'    => 'required',
            'user_id'    => 'required',
            'article_id' => 'required',
            'anon'       => 'required',
        ]);

        if ($v->fails()) {
            return redirect()->route('article', ['slug' => $article->slug, 'id' => $article->id])
                ->with($this->toastr->error($v->errors()->toJson(), 'Whoops!', ['options']));
        } else {
            $comment->save();

            $article_url = hrefArticle($article);
            $profile_url = hrefProfile($user);

            $this->chat->systemMessage(
                ":robot: [b][color=#fb9776]System[/color][/b] : [url={$profile_url}]{$user->username}[/url] has left a comment on article [url={$article_url}]{$article->title}[/url]"
            );

            if ($this->tag->hasTags($request->input('content'))) {
                if ($this->tag->contains($request->input('content'), '@here') && $user->group->is_modo) {
                    $users = collect([]);

                    $article->comments()->get()->each(function ($c, $v) use ($users) {
                        $users->push($c->user);
                    });
                    $this->tag->messageCommentUsers(
                        'article',
                        $users,
                        $user,
                        'Staff',
                        $comment
                    );
                } else {
                    if ($comment->anon) {
                        $sender = 'Anonymous';
                    } else {
                        $sender = $user->username;
                    }
                    $this->tag->messageTaggedCommentUsers(
                        'article',
                        $request->input('content'),
                        $user,
                        $sender,
                        $comment
                    );
                }
            }

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

            return redirect()->route('article', ['slug' => $article->slug, 'id' => $article->id])
                ->with($this->toastr->success('Your Comment Has Been Added!', 'Yay!', ['options']));
        }
    }

    /**
     * Add A Comment To A Torrent.
     *
     * @param \Illuminate\Http\Request $request
     * @param $slug
     * @param $id
     *
     * @return Illuminate\Http\RedirectResponse
     */
    public function torrent(Request $request, $slug, $id)
    {
        $torrent = Torrent::findOrFail($id);
        $user = auth()->user();

        if ($user->can_comment == 0) {
            return redirect()->route('torrent', ['slug' => $torrent->slug, 'id' => $torrent->id])
                ->with($this->toastr->error('Your Comment Rights Have Benn Revoked!!!', 'Whoops!', ['options']));
        }

        $comment = new Comment();
        $comment->content = $request->input('content');
        $comment->anon = $request->input('anonymous');
        $comment->user_id = $user->id;
        $comment->torrent_id = $torrent->id;

        $v = validator($comment->toArray(), [
            'content'    => 'required',
            'user_id'    => 'required',
            'torrent_id' => 'required',
            'anon'       => 'required',
        ]);

        if ($v->fails()) {
            return redirect()->route('torrent', ['slug' => $torrent->slug, 'id' => $torrent->id])
                ->with($this->toastr->error($v->errors()->toJson(), 'Whoops!', ['options']));
        } else {
            $comment->save();

            //Notification
            if ($user->id != $torrent->user_id) {
                $torrent->notifyUploader('comment', $comment);
            }

            $torrent_url = hrefTorrent($torrent);
            $profile_url = hrefProfile($user);

            // Auto Shout
            if ($comment->anon == 0) {
                $this->chat->systemMessage(
                    ":robot: [b][color=#fb9776]System[/color][/b] : [url={$profile_url}]{$user->username}[/url] has left a comment on Torrent [url={$torrent_url}]{$torrent->name}[/url]"
                );
            } else {
                $this->chat->systemMessage(
                    ":robot: [b][color=#fb9776]System[/color][/b] : An anonymous user has left a comment on torrent [url={$torrent_url}]{$torrent->name}[/url]"
                );
            }

            if ($this->tag->hasTags($request->input('content'))) {
                if ($this->tag->contains($request->input('content'), '@here') && $user->group->is_modo) {
                    $users = collect([]);

                    $torrent->comments()->get()->each(function ($c, $v) use ($users) {
                        $users->push($c->user);
                    });
                    $this->tag->messageCommentUsers(
                        'torrent',
                        $users,
                        $user,
                        'Staff',
                        $comment
                    );
                } else {
                    if ($comment->anon) {
                        $sender = 'Anonymous';
                    } else {
                        $sender = $user->username;
                    }
                    $this->tag->messageTaggedCommentUsers(
                        'torrent',
                        $request->input('content'),
                        $user,
                        $sender,
                        $comment
                    );
                }
            }

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

            return redirect()->route('torrent', ['slug' => $torrent->slug, 'id' => $torrent->id])
                ->with($this->toastr->success('Your Comment Has Been Added!', 'Yay!', ['options']));
        }
    }

    /**
     * Add A Comment To A Request.
     *
     * @param \Illuminate\Http\Request $request
     * @param $slug
     * @param $id
     *
     * @return Illuminate\Http\RedirectResponse
     */
    public function request(Request $request, $id)
    {
        $tr = TorrentRequest::findOrFail($id);
        $user = auth()->user();

        if ($user->can_comment == 0) {
            return redirect()->route('request', ['id' => $tr->id])
                ->with($this->toastr->error('Your Comment Rights Have Benn Revoked!!!', 'Whoops!', ['options']));
        }

        $comment = new Comment();
        $comment->content = $request->input('content');
        $comment->anon = $request->input('anonymous');
        $comment->user_id = $user->id;
        $comment->requests_id = $tr->id;

        $v = validator($comment->toArray(), [
            'content'     => 'required',
            'user_id'     => 'required',
            'requests_id' => 'required',
            'anon'        => 'required',
        ]);

        if ($v->fails()) {
            return redirect()->route('request', ['id' => $tr->id])
                ->with($this->toastr->error($v->errors()->toJson(), 'Whoops!', ['options']));
        } else {
            $comment->save();

            $tr_url = hrefRequest($tr);
            $profile_url = hrefProfile($user);

            // Auto Shout
            if ($comment->anon == 0) {
                $this->chat->systemMessage(
                    ":robot: [b][color=#fb9776]System[/color][/b] : [url={$profile_url}]{$user->username}[/url] has left a comment on Request [url={$tr_url}]{$tr->name}[/url]"
                );
            } else {
                $this->chat->systemMessage(
                    ":robot: [b][color=#fb9776]System[/color][/b] : An anonymous user has left a comment on Request [url={$tr_url}]{$tr->name}[/url]"
                );
            }

            //Notification
            if ($user->id != $tr->user_id) {
                $tr->notifyRequester('comment', $comment);
            }

            if ($this->tag->hasTags($request->input('content'))) {
                if ($this->tag->contains($request->input('content'), '@here') && $user->group->is_modo) {
                    $users = collect([]);

                    $tr->comments()->get()->each(function ($c, $v) use ($users) {
                        $users->push($c->user);
                    });
                    $this->tag->messageCommentUsers(
                        'request',
                        $users,
                        $user,
                        'Staff',
                        $comment
                    );
                } else {
                    if ($comment->anon) {
                        $sender = 'Anonymous';
                    } else {
                        $sender = $user->username;
                    }
                    $this->tag->messageTaggedCommentUsers(
                        'request',
                        $request->input('content'),
                        $user,
                        $sender,
                        $comment
                    );
                }
            }
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

            return redirect()->route('request', ['id' => $tr->id])
                ->with($this->toastr->success('Your Comment Has Been Added!', 'Yay!', ['options']));
        }
    }

    /**
     * Add A Comment To A Torrent Via Quick Thanks.
     *
     * @param $id
     *
     * @return Illuminate\Http\RedirectResponse
     */
    public function quickthanks($id)
    {
        $torrent = Torrent::findOrFail($id);
        $user = auth()->user();

        if ($user->can_comment == 0) {
            return redirect()->route('torrent', ['slug' => $torrent->slug, 'id' => $torrent->id])
                ->with($this->toastr->error('Your Comment Rights Have Benn Revoked!!!', 'Whoops!', ['options']));
        }

        $comment = new Comment();
        $thankArray = [
            'Thanks for the upload! :thumbsup_tone2:',
            'Time and effort is much appreciated :thumbsup_tone2:',
            'Great upload! :fire:', 'Thankyou :smiley:',
        ];
        $selected = mt_rand(0, count($thankArray) - 1);
        $comment->content = $thankArray[$selected];
        $comment->user_id = $user->id;
        $comment->torrent_id = $torrent->id;

        $v = validator($comment->toArray(), [
            'content'    => 'required',
            'user_id'    => 'required',
            'torrent_id' => 'required',
        ]);

        if ($v->fails()) {
            return redirect()->route('torrent', ['slug' => $torrent->slug, 'id' => $torrent->id])
                ->with($this->toastr->error($v->errors()->toJson(), 'Whoops!', ['options']));
        } else {
            $comment->save();

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
            if ($user->id != $torrent->user_id) {
                User::find($torrent->user_id)->notify(new NewComment('torrent', $comment));
            }

            // Auto Shout
            $torrent_url = hrefTorrent($torrent);
            $profile_url = hrefProfile($user);

            $this->chat->systemMessage(
                ":robot: [b][color=#fb9776]System[/color][/b] : [url={$profile_url}]{$user->username}[/url] has left a comment on Torrent [url={$torrent_url}]{$torrent->name}[/url]"
            );

            return redirect()->route('torrent', ['slug' => $torrent->slug, 'id' => $torrent->id])
                ->with($this->toastr->success('Your Comment Has Been Added!', 'Yay!', ['options']));
        }
    }

    /**
     * Edit A Comment.
     *
     * @param \Illuminate\Http\Request $request
     * @param $comment_id
     *
     * @return Illuminate\Http\RedirectResponse
     */
    public function editComment(Request $request, $comment_id)
    {
        $user = auth()->user();
        $comment = Comment::findOrFail($comment_id);

        abort_unless($user->group->is_modo || $user->id == $comment->user_id, 403);
        $content = $request->input('comment-edit');
        $comment->content = $content;
        $comment->save();

        return back()->with($this->toastr->success('Comment Has Been Edited.', 'Yay!', ['options']));
    }

    /**
     * Delete A Comment.
     *
     * @param $comment_id
     *
     * @return Illuminate\Http\RedirectResponse
     */
    public function deleteComment($comment_id)
    {
        $user = auth()->user();
        $comment = Comment::findOrFail($comment_id);

        abort_unless($user->group->is_modo || $user->id == $comment->user_id, 403);
        $comment->delete();

        return back()->with($this->toastr->success('Comment Has Been Deleted.', 'Yay!', ['options']));
    }
}
