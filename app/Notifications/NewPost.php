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

use App\Models\Post;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewPost extends Notification implements ShouldQueue
{
    use Queueable;

    public $post;
    public $type;
    public $poster;

    /**
     * Create a new notification instance.
     *
     * @param Post $post
     * @param string $type
     *
     * @return void
     */
    public function __construct(string $type, User $poster, Post $post)
    {
        $this->poster = $poster;
        $this->post = $post;
        $this->type = $type;
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

        if ($this->type == 'subscription') {
            return [
                'title' => $this->poster->username.' Has Posted In A Subscribed Topic',
                'body' => $this->poster->username.' has left a new post in Subscribed Topic '.$this->post->topic->name,
                'url' => "/forums/topic/{$this->post->topic->slug}.{$this->post->topic->id}?page={$this->post->getPageNumber()}#post-{$this->post->id}",
            ];
        } else {
            return [
                'title' => $this->poster->username.' Has Posted In A Topic You Started',
                'body' => $this->poster->username.' has left a new post in Your Topic '.$this->post->topic->name,
                'url' => "/forums/topic/{$this->post->topic->slug}.{$this->post->topic->id}?page={$this->post->getPageNumber()}#post-{$this->post->id}",
            ];
        }
    }
}
