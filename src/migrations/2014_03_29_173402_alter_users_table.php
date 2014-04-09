<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::table('roles')->insert(array('name' => 'Стандартная','description'=>'Стандартная роль, назначаемая пользователю по умолчанию. Запрещает совершение всех запрещенных действий.'));
		DB::table('roles')->insert(array('name' => 'Администратор','description'=>'Роль, для которой нет запретов.'));
		Schema::table('users', function($table)
		{
		    $table->integer('role_id')->unsigned()->index();
		    $table->foreign('role_id')->references('id')->on('roles')->onDelete('restrict')->onUpdate('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('users', function($table)
		{
			$table->dropForeign('role_id');
		    $table->dropColumn('role_id');
		});
	}

}
