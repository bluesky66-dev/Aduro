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

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWarningsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warnings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->index('warnings_user_id_foreign');
            $table->integer('warned_by')->index('warnings_warned_by_foreign');
            $table->bigInteger('torrent')->unsigned()->index('warnings_torrent_foreign');
            $table->text('reason', 65535);
            $table->dateTime('expires_on')->nullable();
            $table->boolean('active')->default(0);
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('warnings');
    }

}
