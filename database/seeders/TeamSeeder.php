<?php

/*
 * Insider Champions League Trial Project - Made with ❤
 * @author xerenahmed (Eren A. Akyol)
 */

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Seeder;
use function asset;

class TeamSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 */
	public function run() : void
	{
		Team::create([
			'name' => 'Man City',
			'texture_colors' => ['#6a9bc2','#98c5e9'],
			'flag_url' => asset('img/man-city-logo.png'),
			'featured_player_url' => asset('img/man-city-player.png'),
			'strength' => 95,
		]);
		Team::create([
			'name' => 'Liverpool',
			'texture_colors' => ['#911712','#dc0714'],
			'flag_url' => asset('img/liverpool-logo.png'),
			'featured_player_url' => asset('img/liverpool-player.png'),
			'strength' => 90,
		]);
		Team::create([
			'name' => 'Arsenal',
			'texture_colors' => ['#be000a','#ff0203'],
			'flag_url' => asset('img/arsenal-logo.png'),
			'featured_player_url' => asset('img/arsenal-player.png'),
			'strength' => 85,
		]);
		Team::create([
			'name' => 'Chelsea',
			'texture_colors' => ['#1934be','#2145f6'],
			'flag_url' => asset('img/chelsea-logo.png'),
			'featured_player_url' => asset('img/chelsea-player.png'),
			'strength' => 80,
		]);
	}
}
