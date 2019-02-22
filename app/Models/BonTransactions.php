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

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BonTransactions extends Model
{
    /**
     * The Database Table Used By The Model.
     *
     * @var string
     */
    protected $table = 'bon_transactions';

    /**
     * Indicates If The Model Should Be Timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The Storage Format Of The Model's Date Columns.
     *
     * @var string
     */
    protected $dateFormat = 'U';

    /**
     * Belongs To A Sender.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    // Bad name to not conflict with sender (not sender_id)

    public function senderObj()
    {
        return $this->belongsTo(User::class, 'sender', 'id')->withDefault([
            'username' => 'System',
            'id'       => '1',
        ]);
    }

    /**
     * Belongs To A Receiver.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    // Bad name to not conflict with sender (not sender_id)

    public function receiverObj()
    {
        return $this->belongsTo(User::class, 'receiver', 'id')->withDefault([
            'username' => 'System',
            'id'       => '1',
        ]);
    }

    /**
     * Belongs To BonExchange.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function exchange()
    {
        return $this->belongsTo(BonExchange::class, 'itemID', 'id')->withDefault([
            'value' => 0,
            'cost' => 0,
        ]);
    }
}
