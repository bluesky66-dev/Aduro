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
 * @author     HDVinnie, singularity43
 */

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewCommentTag extends Notification implements ShouldQueue
{
    use Queueable;

    public $type;

    public $tagger;

    public $comment;

    /**
     * Create a new notification instance.
     *
     * @param  string  $type
     * @param  string  $tagger
     * @param  Comment  $comment
     */
    public function __construct(string $type, string $tagger, Comment $comment)
    {
        $this->type = $type;
        $this->comment = $comment;
        $this->tagger = $tagger;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function toArray($notifiable)
    {
        $appurl = config('app.url');

        if ($this->type == 'torrent') {
            return [
                'title' => $this->tagger.' Has Tagged You In A Torrent Comment',
                'body' => $this->tagger.' has tagged you in a Comment for Torrent '.$this->comment->torrent->name,
                'url' => "/torrents/{$this->comment->torrent->slug}.{$this->comment->torrent->id}",
            ];
        } elseif ($this->type == 'request') {
            return [
                'title' => $this->tagger.' Has Tagged You In A Request Comment',
                'body' => $this->tagger.' has tagged you in a Comment for Request '.$this->comment->request->name,
                'url' => "/request/{$this->comment->request->id}",
            ];
        }

        return [
            'title' => $this->tagger.' Has Tagged You In An Article Comment',
            'body' => $this->tagger.' has tagged you in a Comment for Article '.$this->comment->article->title,
            'url' => "/articles/{$this->comment->article->slug}.{$this->comment->article->id}",
        ];
    }
}
