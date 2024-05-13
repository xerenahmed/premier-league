<?php

/*
 * Insider Champions League Trial Project - Made with ❤
 * @author xerenahmed (Eren A. Akyol)
 */

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
	/**
	 * Run the migrations.
	 */
	public function up() : void
	{
		Schema::create('fixture_stats', function (Blueprint $table) {
			$table->id();
			$table->foreignId('fixture_id')->constrained('fixtures');
			$table->foreignId('team_id')->constrained('teams');
			$table->integer('points');
			$table->integer('goals');
			$table->integer('fouls');
			$table->integer('aggression');
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down() : void
	{
		Schema::dropIfExists('fixture_stats');
	}
};
