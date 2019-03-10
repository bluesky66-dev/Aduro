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

use Illuminate\Bus\Queueable;
use App\Models\TorrentRequest;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewRequestFillReject extends Notification implements ShouldQueue
{
    use Queueable;

    public $type;
    public $sender;
    public $tr;

    /**
     * Create a new notification instance.
     *
     * @param Torrent $torrent
     *
     * @return void
     */
    public function __construct(string $type, string $sender, TorrentRequest $tr)
    {
        $this->type = $type;
        $this->sender = $sender;
        $this->tr = $tr;
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
            'title' => $this->sender.' Has Rejected Your Fill Of A Requested Torrent',
            'body'  => $this->sender.' has rejected your fill of Requested Torrent '.$this->tr->name,
            'url'   => "/request/{$this->tr->id}",
        ];
    }
}
