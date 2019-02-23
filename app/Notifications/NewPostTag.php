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
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewPostTag extends Notification implements ShouldQueue
{
    use Queueable;

    public $type;
    public $tagger;
    public $post;

    /**
     * Create a new notification instance.
     *
     * @param Post $post
     *
     * @return void
     */
    public function __construct(string $type, string $tagger, Post $post)
    {
        $this->type = $type;
        $this->post = $post;
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

        return [
            'title' => $this->tagger.' Has Tagged You In A Post',
            'body'  => $this->tagger.' has tagged you in a Post in Topic '.$this->post->topic->name,
            'url'   => "/forums/topic/{$this->post->topic->slug}.{$this->post->topic->id}?page={$this->post->getPageNumber()}#post-{$this->post->id}",
        ];
    }
}
