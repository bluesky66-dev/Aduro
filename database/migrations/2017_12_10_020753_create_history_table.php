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

class CreateHistoryTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('history', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->index('history_user_id_foreign');
            $table->string('agent')->nullable();
            $table->string('info_hash')->index('info_hash');
            $table->bigInteger('uploaded')->unsigned()->nullable();
            $table->bigInteger('actual_uploaded')->unsigned()->nullable();
            $table->bigInteger('client_uploaded')->unsigned()->nullable();
            $table->bigInteger('downloaded')->unsigned()->nullable();
            $table->bigInteger('actual_downloaded')->unsigned()->nullable();
            $table->bigInteger('client_downloaded')->unsigned()->nullable();
            $table->boolean('seeder')->default(0);
            $table->boolean('active')->default(0);
            $table->bigInteger('seedtime')->unsigned()->default(0);
            $table->timestamps();
            $table->dateTime('completed_at')->nullable();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('history');
    }
}
