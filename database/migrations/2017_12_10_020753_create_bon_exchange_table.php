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

class CreateBonExchangeTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bon_exchange', function (Blueprint $table) {
            $table->increments('id');
            $table->string('description')->nullable();
            $table->bigInteger('value')->unsigned()->default(0);
            $table->integer('cost')->unsigned()->default(0);
            $table->boolean('upload')->default(0);
            $table->boolean('download')->default(0);
            $table->boolean('personal_freeleech')->default(0);
            $table->boolean('invite')->default(0);
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('bon_exchange');
    }

}
