<?php
/**
 * NOTICE OF LICENSE.
 *
 * UNIT3D Community Edition is open-sourced software licensed under the GNU Affero General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D Community Edition
 *
 * @author     HDVinnie <hdinnovations@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

use App\Helpers\ByteUnits;
use Illuminate\Database\Seeder;

class BonExchangeTableSeeder extends Seeder
{
    /**
     * The library used for parsing byte units.
     *
     * @var Parser
     */
    protected $byteUnits;

    public function __construct(
        ByteUnits $byteUnits
    ) {
        $this->byteUnits = $byteUnits;
    }

    /**
     * Auto generated seed file.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('bon_exchange')->delete();

        \DB::table('bon_exchange')->insert([
            0 => [
                'id'                 => 1,
                'description'        => '2 GiB Upload',
                'value'              => $this->byteUnits->bytesFromUnit('2GiB'),
                'cost'               => 500,
                'upload'             => 1,
                'download'           => 0,
                'personal_freeleech' => 0,
                'invite'             => 0,
            ],
            1 => [
                'id'                 => 2,
                'description'        => '10 GiB Upload',
                'value'              => $this->byteUnits->bytesFromUnit('10GiB'),
                'cost'               => 1000,
                'upload'             => 1,
                'download'           => 0,
                'personal_freeleech' => 0,
                'invite'             => 0,
            ],
            2 => [
                'id'                 => 3,
                'description'        => '25 GiB Upload',
                'value'              => $this->byteUnits->bytesFromUnit('25GiB'),
                'cost'               => 2000,
                'upload'             => 1,
                'download'           => 0,
                'personal_freeleech' => 0,
                'invite'             => 0,
            ],
            3 => [
                'id'                 => 4,
                'description'        => '100 GiB Upload',
                'value'              => $this->byteUnits->bytesFromUnit('100GiB'),
                'cost'               => 5000,
                'upload'             => 1,
                'download'           => 0,
                'personal_freeleech' => 0,
                'invite'             => 0,
            ],
            4 => [
                'id'                 => 9,
                'description'        => '1 Invite',
                'value'              => 1,
                'cost'               => 2500,
                'upload'             => 0,
                'download'           => 0,
                'personal_freeleech' => 0,
                'invite'             => 1,
            ],
            5 => [
                'id'                 => 10,
                'description'        => 'Personal 24Hr Freeleech',
                'value'              => 1,
                'cost'               => 7500,
                'upload'             => 0,
                'download'           => 0,
                'personal_freeleech' => 1,
                'invite'             => 0,
            ],
        ]);
    }
}
