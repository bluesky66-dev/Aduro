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

use App\Achievements\UserMade100Comments;
use App\Achievements\UserMade200Comments;
use App\Achievements\UserMade300Comments;
use App\Achievements\UserMade400Comments;
use App\Achievements\UserMade500Comments;
use App\Achievements\UserMade50Comments;
use App\Achievements\UserMade600Comments;
use App\Achievements\UserMade700Comments;
use App\Achievements\UserMade800Comments;
use App\Achievements\UserMade900Comments;
use App\Achievements\UserMadeComment;
use App\Achievements\UserMadeTenComments;
use App\Models\Article;
use App\Models\Collection;
use App\Models\Comment;
use App\Models\Playlist;
use App\Models\Torrent;
use App\Models\TorrentRequest;
use App\Models\User;
use App\Notifications\NewComment;
use App\Repositories\ChatRepository;
use App\Repositories\TaggedUserRepository;
use Illuminate\Http\Request;

/**
 * @see \Tests\Feature\Http\Controllers\CommentControllerTest
 */
class CommentController extends Controller
{
    public $tag;

    /**
     * CommentController Constructor.
     *
     * @param \App\Repositories\TaggedUserRepository $taggedUserRepository
     * @param \App\Repositories\ChatRepository       $chatRepository
     */
    public function __construct(private TaggedUserRepository $taggedUserRepository, private ChatRepository $chatRepository)
    {
    }

    /**
     * Add A Comment To A Collection.
     *
     * @param \Illuminate\Http\Request $request
     * @param                          $id
     *
     * @return Illuminate\Http\RedirectResponse
     */
    public function collection(Request $request, $id)
    {
        $collection = Collection::findOrFail($id);
        $user = \auth()->user();

        if ($user->can_comment == 0) {
            return \redirect()->route('collection.show', ['id' => $collection->id])
                ->withErros('Your Comment Rights Have Been Revoked!');
        }

        $comment = new Comment();
        $comment->content = $request->input('content');
        $comment->anon = $request->input('anonymous');
        $comment->user_id = $user->id;
        $comment->collection_id = $collection->id;

        $v = \validator($comment->toArray(), [
            'content'       => 'required',
            'user_id'       => 'required',
            'collection_id' => 'required',
            'anon'          => 'required',
        ]);

        if ($v->fails()) {
            return \redirect()->route('collection.show', ['id' => $collection->id])
                ->withErrors($v->errors());
        }
        $comment->save();

        $collectionUrl = \href_collection($collection);
        $profileUrl = \href_profile($user);

        // Auto Shout
        if ($comment->anon == 0) {
            $this->chatRepository->systemMessage(
                    "[url={$profileUrl}]{$user->username}[/url] has left a comment on collection [url={$collectionUrl}]{$collection->name}[/url]"
                );
        } else {
            $this->chatRepository->systemMessage(
                    "An anonymous user has left a comment on collection [url={$collectionUrl}]{$collection->name}[/url]"
                );
        }

        if ($this->taggedUserRepository->hasTags($request->input('content'))) {
            if ($this->taggedUserRepository->contains($request->input('content'), '@here') && $user->group->is_modo) {
                $users = \collect([]);

                $collection->comments()->get()->each(function ($c, $v) use ($users) {
                    $users->push($c->user);
                });
                $this->tag->messageCommentUsers(
                        'collection',
                        $users,
                        $user,
                        'Staff',
                        $comment
                    );
            } else {
                $sender = $comment->anon ? 'Anonymous' : $user->username;
                $this->taggedUserRepository->messageTaggedCommentUsers(
                        'collection',
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

        return \redirect()->route('mediahub.collections.show', ['id' => $collection->id, 'hash' => '#comments'])
                ->withSuccess('Your Comment Has Been Added!');
    }

    /**
     * Store A New Comment To A Article.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Article      $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function article(Request $request, $id)
    {
        $article = Article::findOrFail($id);
        $user = $request->user();

        if ($user->can_comment == 0) {
            return \redirect()->route('articles.show', ['id' => $article->id])
                ->withErrors('Your Comment Rights Have Been Revoked!');
        }

        $comment = new Comment();
        $comment->content = $request->input('content');
        $comment->anon = $request->input('anonymous');
        $comment->user_id = $user->id;
        $comment->article_id = $article->id;

        $v = \validator($comment->toArray(), [
            'content'    => 'required',
            'user_id'    => 'required',
            'article_id' => 'required',
            'anon'       => 'required',
        ]);

        if ($v->fails()) {
            return \redirect()->route('articles.show', ['id' => $article->id])
                ->withErrors($v->errors());
        }
        $comment->save();
        $articleUrl = \href_article($article);
        $profileUrl = \href_profile($user);
        // Auto Shout
        if ($comment->anon == 0) {
            $this->chatRepository->systemMessage(
                \sprintf('[url=%s]%s[/url] has left a comment on article [url=%s]%s[/url]', $profileUrl, $user->username, $articleUrl, $article->title)
            );
        } else {
            $this->chatRepository->systemMessage(
                \sprintf('An anonymous user has left a comment on article [url=%s]%s[/url]', $articleUrl, $article->title)
            );
        }
        if ($this->taggedUserRepository->hasTags($request->input('content'))) {
            if ($this->taggedUserRepository->contains($request->input('content'), '@here') && $user->group->is_modo) {
                $users = \collect([]);

                $article->comments()->get()->each(function ($c) use ($users) {
                    $users->push($c->user);
                });
                $this->taggedUserRepository->messageCommentUsers(
                    'article',
                    $users,
                    $user,
                    'Staff',
                    $comment
                );
            } else {
                $sender = $comment->anon ? 'Anonymous' : $user->username;
                $this->taggedUserRepository->messageTaggedCommentUsers(
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

        return \redirect()->route('articles.show', ['id' => $article->id])
            ->withSuccess('Your Comment Has Been Added!');
    }

    /**
     * Store A New Comment To A Playlist.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Playlist     $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function playlist(Request $request, $id)
    {
        $playlist = Playlist::findOrFail($id);
        $user = \auth()->user();

        if ($user->can_comment == 0) {
            return \redirect()->route('playlists.show', ['id' => $playlist->id])
                ->withErrors('Your Comment Rights Have Been Revoked!');
        }

        $comment = new Comment();
        $comment->content = $request->input('content');
        $comment->anon = $request->input('anonymous');
        $comment->user_id = $user->id;
        $comment->playlist_id = $playlist->id;

        $v = \validator($comment->toArray(), [
            'content'     => 'required',
            'user_id'     => 'required',
            'playlist_id' => 'required',
            'anon'        => 'required',
        ]);

        if ($v->fails()) {
            return \redirect()->route('playlists.show', ['id' => $playlist->id])
                ->withErrors($v->errors());
        }
        $comment->save();
        $playlistUrl = \href_playlist($playlist);
        $profileUrl = \href_profile($user);
        // Auto Shout
        if ($comment->anon == 0) {
            $this->chatRepository->systemMessage(
                \sprintf('[url=%s]%s[/url] has left a comment on playlist [url=%s]%s[/url]', $profileUrl, $user->username, $playlistUrl, $playlist->name)
            );
        } else {
            $this->chatRepository->systemMessage(
                \sprintf('An anonymous user has left a comment on playlist [url=%s]%s[/url]', $playlistUrl, $playlist->name)
            );
        }
        if ($this->taggedUserRepository->hasTags($request->input('content'))) {
            if ($this->taggedUserRepository->contains($request->input('content'), '@here') && $user->group->is_modo) {
                $users = \collect([]);

                $playlist->comments()->get()->each(function ($c) use ($users) {
                    $users->push($c->user);
                });
                $this->taggedUserRepository->messageCommentUsers(
                    'playlist',
                    $users,
                    $user,
                    'Staff',
                    $comment
                );
            } else {
                $sender = $comment->anon ? 'Anonymous' : $user->username;
                $this->taggedUserRepository->messageTaggedCommentUsers(
                    'playlist',
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

        return \redirect()->route('playlists.show', ['id' => $playlist->id, 'hash' => '#comments'])
            ->withSuccess('Your Comment Has Been Added!');
    }

    /**
     * Store A New Comment To A Torrent.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Torrent      $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function torrent(Request $request, $id)
    {
        $torrent = Torrent::findOrFail($id);
        $user = $request->user();

        if ($user->can_comment == 0) {
            return \redirect()->route('torrent', ['id' => $torrent->id])
                ->withErrors('Your Comment Rights Have Been Revoked!');
        }

        $comment = new Comment();
        $comment->content = $request->input('content');
        $comment->anon = $request->input('anonymous');
        $comment->user_id = $user->id;
        $comment->torrent_id = $torrent->id;

        $v = \validator($comment->toArray(), [
            'content'    => 'required',
            'user_id'    => 'required',
            'torrent_id' => 'required',
            'anon'       => 'required',
        ]);

        if ($v->fails()) {
            return \redirect()->route('torrent', ['id' => $torrent->id])
                ->withErrors($v->errors());
        }
        $comment->save();
        //Notification
        if ($user->id != $torrent->user_id) {
            $torrent->notifyUploader('comment', $comment);
        }
        $torrentUrl = \href_torrent($torrent);
        $profileUrl = \href_profile($user);
        // Auto Shout
        if ($comment->anon == 0) {
            $this->chatRepository->systemMessage(
                \sprintf('[url=%s]%s[/url] has left a comment on Torrent [url=%s]%s[/url]', $profileUrl, $user->username, $torrentUrl, $torrent->name)
            );
        } else {
            $this->chatRepository->systemMessage(
                \sprintf('An anonymous user has left a comment on torrent [url=%s]%s[/url]', $torrentUrl, $torrent->name)
            );
        }
        if ($this->taggedUserRepository->hasTags($request->input('content'))) {
            if ($this->taggedUserRepository->contains($request->input('content'), '@here') && $user->group->is_modo) {
                $users = \collect([]);

                $torrent->comments()->get()->each(function ($c) use ($users) {
                    $users->push($c->user);
                });
                $this->taggedUserRepository->messageCommentUsers(
                    'torrent',
                    $users,
                    $user,
                    'Staff',
                    $comment
                );
            } else {
                $sender = $comment->anon ? 'Anonymous' : $user->username;
                $this->taggedUserRepository->messageTaggedCommentUsers(
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

        return \redirect()->route('torrent', ['id' => $torrent->id, 'hash' => '#comments'])
            ->withSuccess('Your Comment Has Been Added!');
    }

    /**
     * Store A New Comment To A Request.
     *
     * @param \Illuminate\Http\Request   $request
     * @param \App\Models\TorrentRequest $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function request(Request $request, $id)
    {
        $tr = TorrentRequest::findOrFail($id);
        $user = $request->user();

        if ($user->can_comment == 0) {
            return \redirect()->route('request', ['id' => $tr->id])
                ->withErrors('Your Comment Rights Have Been Revoked!');
        }

        $comment = new Comment();
        $comment->content = $request->input('content');
        $comment->anon = $request->input('anonymous');
        $comment->user_id = $user->id;
        $comment->requests_id = $tr->id;

        $v = \validator($comment->toArray(), [
            'content'     => 'required',
            'user_id'     => 'required',
            'requests_id' => 'required',
            'anon'        => 'required',
        ]);

        if ($v->fails()) {
            return \redirect()->route('request', ['id' => $tr->id])
                ->withErrors($v->errors());
        }
        $comment->save();
        $trUrl = \href_request($tr);
        $profileUrl = \href_profile($user);
        // Auto Shout
        if ($comment->anon == 0) {
            $this->chatRepository->systemMessage(
                \sprintf('[url=%s]%s[/url] has left a comment on Request [url=%s]%s[/url]', $profileUrl, $user->username, $trUrl, $tr->name)
            );
        } else {
            $this->chatRepository->systemMessage(
                \sprintf('An anonymous user has left a comment on Request [url=%s]%s[/url]', $trUrl, $tr->name)
            );
        }
        //Notification
        if ($user->id != $tr->user_id) {
            $tr->notifyRequester('comment', $comment);
        }
        if ($this->taggedUserRepository->hasTags($request->input('content'))) {
            if ($this->taggedUserRepository->contains($request->input('content'), '@here') && $user->group->is_modo) {
                $users = \collect([]);

                $tr->comments()->get()->each(function ($c) use ($users) {
                    $users->push($c->user);
                });
                $this->taggedUserRepository->messageCommentUsers(
                    'request',
                    $users,
                    $user,
                    'Staff',
                    $comment
                );
            } else {
                $sender = $comment->anon ? 'Anonymous' : $user->username;
                $this->taggedUserRepository->messageTaggedCommentUsers(
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

        return \redirect()->route('request', ['id' => $tr->id, 'hash' => '#comments'])
            ->withSuccess('Your Comment Has Been Added!');
    }

    /**
     * Store A New Comment To A Torrent Via Quick Thanks.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Torrent      $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function quickthanks(Request $request, $id)
    {
        $torrent = Torrent::findOrFail($id);
        $user = $request->user();

        if ($user->can_comment == 0) {
            return \redirect()->route('torrent', ['id' => $torrent->id])
                ->withErrors('Your Comment Rights Have Been Revoked!');
        }

        $comment = new Comment();

        if ($torrent->anon === 1) {
            $thankArray = [
                'Thanks for the upload! :thumbsup_tone2:',
                'Time and effort is much appreciated :thumbsup_tone2:',
                'Great upload! :fire:', 'Thank you :smiley:',
            ];
        } else {
            $uploader = User::where('id', '=', $torrent->user_id)->first();
            $uploaderUrl = \href_profile($uploader);

            $thankArray = [
                \sprintf('Thanks for the upload [url=%s][color=%s][b]%s[/b][/color][/url] :vulcan_tone2:', $uploaderUrl, $uploader->group->color, $uploader->username),
                \sprintf('Beautiful upload [url=%s][color=%s][b]%s[/b][/color][/url] :fire:', $uploaderUrl, $uploader->group->color, $uploader->username),
                \sprintf('Cheers [url=%s][color=%s][b]%s[/b][/color][/url] for the upload :beers:', $uploaderUrl, $uploader->group->color, $uploader->username),
            ];
        }

        $selected = \mt_rand(0, (\is_countable($thankArray) ? \count($thankArray) : 0) - 1);
        $comment->content = $thankArray[$selected];
        $comment->user_id = $user->id;
        $comment->torrent_id = $torrent->id;

        $v = \validator($comment->toArray(), [
            'content'    => 'required',
            'user_id'    => 'required',
            'torrent_id' => 'required',
        ]);

        if ($v->fails()) {
            return \redirect()->route('torrent', ['id' => $torrent->id])
                ->withErrors($v->errors());
        }
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
        $torrentUrl = \href_torrent($torrent);
        $profileUrl = \href_profile($user);
        $this->chatRepository->systemMessage(
            \sprintf('[url=%s]%s[/url] has left a comment on Torrent [url=%s]%s[/url]', $profileUrl, $user->username, $torrentUrl, $torrent->name)
        );

        return \redirect()->route('torrent', ['id' => $torrent->id])
            ->withSuccess('Your Comment Has Been Added!');
    }

    /**
     * Edit A Comment.
     *
     * @param \Illuminate\Http\Request $request
     * @param                          $commentId
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function editComment(Request $request, $commentId)
    {
        $user = $request->user();
        $comment = Comment::findOrFail($commentId);

        \abort_unless($user->group->is_modo || $user->id == $comment->user_id, 403);
        $comment->content = $request->input('comment-edit');

        $v = \validator($comment->toArray(), [
            'content'    => 'required',
        ]);

        if ($v->fails()) {
            return \redirect()->back()
                ->withErrors($v->errors());
        }

        $comment->save();

        return \redirect()->back()->withSuccess('Comment Has Been Edited.');
    }

    /**
     * Delete A Comment.
     *
     * @param \Illuminate\Http\Request $request
     * @param                          $commentId
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteComment(Request $request, $commentId)
    {
        $user = $request->user();
        $comment = Comment::findOrFail($commentId);

        \abort_unless($user->group->is_modo || $user->id == $comment->user_id, 403);
        $comment->delete();

        return \redirect()->back()->withSuccess('Comment Has Been Deleted.');
    }
}
