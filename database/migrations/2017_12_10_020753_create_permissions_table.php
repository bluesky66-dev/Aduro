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

class CreatePermissionsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('forum_id')->index('fk_permissions_forums1_idx');
            $table->integer('group_id')->index('fk_permissions_groups1_idx');
            $table->boolean('show_forum');
            $table->boolean('read_topic');
            $table->boolean('reply_topic');
            $table->boolean('start_topic');
            $table->boolean('upload');
            $table->boolean('download');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('permissions');
    }

}
