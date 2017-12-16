<?php
/**
 * NOTICE OF LICENSE
 *
 * UNIT3D is open-sourced software licensed under the GNU General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D
 * @license    https://choosealicense.com/licenses/gpl-3.0/  GNU General Public License v3.0
 * @author     HDVinnie
 */

namespace App\Console\Commands;

use App\Shoutbox;
use App\FeaturedTorrent;
use App\Torrent;

use Carbon\Carbon;
use Cache;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class removeFeaturedTorrent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'removeFeaturedTorrent';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically Removes Featured Torrents If Expired';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $current = Carbon::now();
        $featured_torrents = FeaturedTorrent::where('created_at', '<', $current->copy()->subDays(7)->toDateTimeString())->get();

        foreach ($featured_torrents as $featured_torrent) {
            // Find The Torrent
            $torrent = Torrent::where('featured', '=', 1)->where('id', '=', $featured_torrent->torrent_id)->first();
            $torrent->free = 0;
            $torrent->doubleup = 0;
            $torrent->featured = 0;
            $torrent->save();

            // Auto Announce Featured Expired
            Shoutbox::create(['user' => "1", 'mentions' => "1", 'message' => "Ladies and Gents, [url={{ route('torrents') }}/" . $torrent->slug . "." . $torrent->id . "]" . $torrent->name . "[/url] is no longer featured. :poop:"]);
            Cache::forget('shoutbox_messages');

            // Delete The Record From DB
            $featured_torrent->delete();
        }
    }
}
