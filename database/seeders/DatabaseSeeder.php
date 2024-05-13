<?php

/*
 * Insider Champions League Trial Project - Made with ❤
 * @author xerenahmed (Eren A. Akyol)
 */

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\League;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
	/**
	 * Seed the application's database.
	 */
	public function run() : void
	{
		League::create([
			'name' => 'Insider Champions League',
			'round' => 0,
			'started' => false
		]);
		$this->call(TeamSeeder::class);
	}
}
