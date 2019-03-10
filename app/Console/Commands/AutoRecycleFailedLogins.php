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

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\FailedLoginAttempt;

class AutoRecycleFailedLogins extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:recycle_failed_logins';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recycle Failed Logins Once 30 Days Old.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $current = Carbon::now();
        $failedLogins = FailedLoginAttempt::where('created_at', '<', $current->copy()->subDays(30)->toDateTimeString())->get();

        foreach ($failedLogins as $failedLogin) {
            $failedLogin->delete();
        }
    }
}
