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
		Schema::create('fixtures', function (Blueprint $table) {
			$table->id();
			$table->foreignId('league_id')->constrained();
			$table->foreignId('home_team_id')->constrained()->on('teams');
			$table->foreignId('away_team_id')->constrained()->on('teams');
			$table->integer('round');
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down() : void
	{
		Schema::dropIfExists('fixtures');
	}
};
