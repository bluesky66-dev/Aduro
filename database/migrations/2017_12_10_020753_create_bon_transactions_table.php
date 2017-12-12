<?php
/**
 * NOTICE OF LICENSE
 *
 * UNIT3D is open-sourced software licensed under the GNU General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D
 * @license    https://choosealicense.com/licenses/gpl-3.0/  GNU General Public License v3.0
 * @author     BluCrew
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBonTransactionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bon_transactions', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('itemID')->unsigned()->default(0);
			$table->string('name')->default('');
			$table->float('cost', 22)->default(0.00);
			$table->integer('sender')->unsigned()->default(0);
			$table->integer('receiver')->unsigned()->default(0);
			$table->integer('torrent_id')->nullable();
			$table->text('comment', 65535);
			$table->timestamp('date_actioned')->default(DB::raw('CURRENT_TIMESTAMP'));
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('bon_transactions');
	}

}
