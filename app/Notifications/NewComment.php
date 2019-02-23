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
 * @author     HDVinnie, singularity43
 */

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewComment extends Notification
{
    use Queueable;

    public $type;
    public $comment;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(string $type, Comment $comment)
    {
        $this->type = $type;
        $this->comment = $comment;
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
            if ($this->comment->anon == 0) {
                return [
                    'title' => 'New Torrent Comment Received',
                    'body' => $this->comment->user->username.' has left you a comment on Torrent '.$this->comment->torrent->name,
                    'url' => '/torrents/'.$this->comment->torrent->slug.'.'.$this->comment->torrent->id,
                ];
            } else {
                return [
                    'title' => 'New Torrent Comment Received',
                    'body' => 'Anonymous has left you a comment on Torrent '.$this->comment->torrent->name,
                    'url' => '/torrents/'.$this->comment->torrent->slug.'.'.$this->comment->torrent->id,
                ];
            }
        }
        if ($this->comment->anon == 0) {
            return [
                'title' => 'New Request Comment Received',
                'body'  => $this->comment->user->username.' has left you a comment on Torrent Request '.$this->comment->request->name,
                'url'   => '/request/'.$this->comment->request->id,
            ];
        } else {
            return [
                'title' => 'New Request Comment Received',
                'body'  => 'Anonymous has left you a comment on Torrent Request '.$this->comment->request->name,
                'url'   => '/request/'.$this->comment->request->id,
            ];
        }
    }
}
