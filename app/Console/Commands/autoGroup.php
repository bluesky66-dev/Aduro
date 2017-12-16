<?php
/**
 * NOTICE OF LICENSE
 *
 * UNIT3D is open-sourced software licensed under the GNU General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D
 * @license    https://choosealicense.com/licenses/gpl-3.0/  GNU General Public License v3.0
 * @author     Mr.G
 */

namespace App\Console\Commands;

use App\PrivateMessage;
use App\User;
use App\Group;
use App\History;
use Carbon\Carbon;

use Illuminate\Support\Facades\Config;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class autoGroup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'autoGroup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically Change A Users Group Class If Requirements Met';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Temp Hard Coding of Immune Groups (Config Files To Come)
        $current = Carbon::now();
        $groups = Group::select('id')->where('autogroup', '=', 1)->get()->toArray();
        $users = User::whereIn('group_id', $groups)->get();

        foreach ($users as $user) {

            $hiscount = History::where('user_id', '=', $user->id)->count();

            // Temp Hard Coding of Group Requirements (Config Files To Come) (Upload in Bytes!) (Seedtime in Seconds!)

            //Leech ratio dropped below sites minimum
            if ($user->getRatio() < Config::get('other.ratio') && $user->group_id != 15) {
                $user->group_id = 15;
                $user->can_request = 0;
                $user->can_invite = 0;
                $user->can_download = 0;
                $user->save();
                //PrivateMessage::create(['sender_id' => "1", 'reciever_id' => $user->id, 'subject' => "Sad News My Friend!", 'message' => "You have been demoted to Leech group. Your ratio dropped below 0.2. So now you have been placed in Leech Group and your download rights, invite rights and request rights have been revoked. Please seed and use bon to bring ratio back up! [color=red][b]THIS IS AN AUTOMATED SYSTEM MESSAGE, PLEASE DO NOT REPLY![/b][/color]"]);
            }
            //Member >= 0 but < 1TB and ratio above sites minimum
            if ($user->uploaded >= 0 && $user->getRatio() > Config::get('other.ratio') && $user->group_id != 3) {
                $user->group_id = 3;
                $user->can_download = 1;
                $user->can_request = 1;
                $user->can_invite = 1;
                $user->save();
                //PrivateMessage::create(['sender_id' => "1", 'reciever_id' => $user->id, 'subject' => "Group Change", 'message' => "You group/rank has been changed! [color=red][b]THIS IS AN AUTOMATED SYSTEM MESSAGE, PLEASE DO NOT REPLY![/b][/color]"]);
            }

            //BluMember >= 1TB but < 5TB and account 1 month old
            if ($user->uploaded >= 1073741824000 && $user->uploaded < 1073741824000 * 5 && $user->created_at < $current->copy()->subDays(30)->toDateTimeString() && $user->group_id != 11) {
                $user->group_id = 11;
                $user->save();
                //PrivateMessage::create(['sender_id' => "1", 'reciever_id' => $user->id, 'subject' => "Group Change", 'message' => "You group/rank has been changed! [color=red][b]THIS IS AN AUTOMATED SYSTEM MESSAGE, PLEASE DO NOT REPLY![/b][/color]"]);
            }
            //BluMaster >= 5TB but < 20TB and account 1 month old
            if ($user->uploaded >= 1073741824000 * 5 && $user->uploaded < 1073741824000 * 20 && $user->created_at < $current->copy()->subDays(30)->toDateTimeString() && $user->group_id != 12) {
                $user->group_id = 12;
                $user->save();
                //PrivateMessage::create(['sender_id' => "1", 'reciever_id' => $user->id, 'subject' => "Group Change", 'message' => "You group/rank has been changed! [color=red][b]THIS IS AN AUTOMATED SYSTEM MESSAGE, PLEASE DO NOT REPLY![/b][/color]"]);
            }
            //BluExtremist >= 20TB but < 50TB and account 3 month old
            if ($user->uploaded >= 1073741824000 * 20 && $user->uploaded < 1073741824000 * 50 && $user->created_at < $current->copy()->subDays(90)->toDateTimeString() && $user->group_id != 13) {
                $user->group_id = 13;
                $user->save();
                //PrivateMessage::create(['sender_id' => "1", 'reciever_id' => $user->id, 'subject' => "Group Change", 'message' => "You group/rank has been changed! [color=red][b]THIS IS AN AUTOMATED SYSTEM MESSAGE, PLEASE DO NOT REPLY![/b][/color]"]);
            }
            //BluLegend >= 50TB but < 100TB and account 6 month old
            if ($user->uploaded >= 1073741824000 * 50 && $user->uploaded < 1073741824000 * 100 && $user->created_at < $current->copy()->subDays(180)->toDateTimeString() && $user->group_id != 14) {
                $user->group_id = 14;
                $user->save();
                //PrivateMessage::create(['sender_id' => "1", 'reciever_id' => $user->id, 'subject' => "Group Change", 'message' => "You group/rank has been changed! [color=red][b]THIS IS AN AUTOMATED SYSTEM MESSAGE, PLEASE DO NOT REPLY![/b][/color]"]);
            }
            //Blutopian >= 100TB and account 1 year old
            if ($user->uploaded >= 1073741824000 * 100 && $user->created_at < $current->copy()->subDays(365)->toDateTimeString() && $user->group_id != 16) {
                $user->group_id = 16;
                $user->save();
                //PrivateMessage::create(['sender_id' => "1", 'reciever_id' => $user->id, 'subject' => "Group Change", 'message' => "You group/rank has been changed! [color=red][b]THIS IS AN AUTOMATED SYSTEM MESSAGE, PLEASE DO NOT REPLY![/b][/color]"]);
            }

            //BluSeeder seeding >= 150 and account 1 month old and seedtime average 30 days or better
            if ($user->getSeeding() >= 150 && round($user->getTotalSeedTime() / max(1, $hiscount)) > 2592000 && $user->created_at < $current->copy()->subDays(30)->toDateTimeString() && $user->group_id != 17) {
                $user->group_id = 17;
                $user->save();
                //PrivateMessage::create(['sender_id' => "1", 'reciever_id' => $user->id, 'subject' => "Group Change", 'message' => "You group/rank has been changed! [color=red][b]THIS IS AN AUTOMATED SYSTEM MESSAGE, PLEASE DO NOT REPLY![/b][/color]"]);
            }
            //BluArchivist seeding >= 300 and account 3 month old and seedtime average 60 days or better
            if ($user->getSeeding() >= 300 && round($user->getTotalSeedTime() / max(1, $hiscount)) > 2592000 * 2 && $user->created_at < $current->copy()->subDays(90)->toDateTimeString() && $user->group_id != 18) {
                $user->group_id = 18;
                $user->save();
                //PrivateMessage::create(['sender_id' => "1", 'reciever_id' => $user->id, 'subject' => "Group Change", 'message' => "You group/rank has been changed! [color=red][b]THIS IS AN AUTOMATED SYSTEM MESSAGE, PLEASE DO NOT REPLY![/b][/color]"]);
            }
        }
    }
}
