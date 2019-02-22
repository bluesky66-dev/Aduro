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
 * @author     Mr.G
 */

namespace App\Helpers;

use App\Models\User;
use App\Models\Wish;
use App\Models\Follow;
use App\Models\Torrent;
use App\Bots\IRCAnnounceBot;
use App\Models\PrivateMessage;
use App\Notifications\NewUpload;
use App\Achievements\UserMadeUpload;
use App\Achievements\UserMade25Uploads;
use App\Achievements\UserMade50Uploads;
use App\Achievements\UserMade100Uploads;
use App\Achievements\UserMade200Uploads;
use App\Achievements\UserMade300Uploads;
use App\Achievements\UserMade400Uploads;
use App\Achievements\UserMade500Uploads;
use App\Achievements\UserMade600Uploads;
use App\Achievements\UserMade700Uploads;
use App\Achievements\UserMade800Uploads;
use App\Achievements\UserMade900Uploads;

class TorrentHelper
{
    public static function approveHelper($slug, $id)
    {
        $appurl = config('app.url');
        $appname = config('app.name');

        Torrent::approve($id);
        $torrent = Torrent::with('uploader')->withAnyStatus()->where('id', '=', $id)->where('slug', '=', $slug)->first();
        $uploader = $torrent->uploader;

        $wishes = Wish::where('imdb', '=', 'tt'.$torrent->imdb)->whereNull('source')->get();
        if ($wishes) {
            foreach ($wishes as $wish) {
                $wish->source = "{$appurl}/torrents/{$torrent->slug}.{$torrent->id}";
                $wish->save();

                // Send Private Message
                $pm = new PrivateMessage();
                $pm->sender_id = 1;
                $pm->receiver_id = $wish->user_id;
                $pm->subject = 'Wish List Notice!';
                $pm->message = "The following item, {$wish->title}, from your wishlist has been uploaded to {$appname}! You can view it [url={$appurl}/torrents/".$torrent->slug.'.'.$torrent->id.'] HERE [/url]
                                [color=red][b]THIS IS AN AUTOMATED SYSTEM MESSAGE, PLEASE DO NOT REPLY![/b][/color]';
                $pm->save();
            }
        }

        if ($torrent->anon == 0) {
            $followers = Follow::where('target_id', '=', $torrent->user_id)->get();
            if ($followers) {
                foreach ($followers as $follower) {
                    $pushto = User::with('notification')->find($follower->user_id);
                    if ($pushto->acceptsNotification($uploader, $pushto, 'following', 'show_following_upload')) {
                        $pushto->notify(new NewUpload('follower', $torrent));
                    }
                }
            }
        }

        $user = $torrent->user;
        $username = $user->username;
        $anon = $torrent->anon;

        if ($anon == 0) {
            // Achievements
            $user->unlock(new UserMadeUpload(), 1);
            $user->addProgress(new UserMade25Uploads(), 1);
            $user->addProgress(new UserMade50Uploads(), 1);
            $user->addProgress(new UserMade100Uploads(), 1);
            $user->addProgress(new UserMade200Uploads(), 1);
            $user->addProgress(new UserMade300Uploads(), 1);
            $user->addProgress(new UserMade400Uploads(), 1);
            $user->addProgress(new UserMade500Uploads(), 1);
            $user->addProgress(new UserMade600Uploads(), 1);
            $user->addProgress(new UserMade700Uploads(), 1);
            $user->addProgress(new UserMade800Uploads(), 1);
            $user->addProgress(new UserMade900Uploads(), 1);
        }

        // Announce To IRC
        if (config('irc-bot.enabled') == true) {
            $appname = config('app.name');
            $bot = new IRCAnnounceBot();
            if ($anon == 0) {
                $bot->message('#announce', '['.$appname.'] User '.$username.' has uploaded '.$torrent->name.' grab it now!');
                $bot->message('#announce', '[Category: '.$torrent->category->name.'] [Type: '.$torrent->type.'] [Size:'.$torrent->getSize().']');
                $bot->message('#announce', "[Link: {$appurl}/torrents/".$slug.'.'.$id.']');
            } else {
                $bot->message('#announce', '['.$appname.'] An anonymous user has uploaded '.$torrent->name.' grab it now!');
                $bot->message('#announce', '[Category: '.$torrent->category->name.'] [Type: '.$torrent->type.'] [Size: '.$torrent->getSize().']');
                $bot->message('#announce', "[Link: {$appurl}/torrents/".$slug.'.'.$id.']');
            }
        }

        // Activity Log
        \LogActivity::addToLog('Torrent '.$torrent->name.' uploaded by '.$username.' has been approved.');
    }
}
