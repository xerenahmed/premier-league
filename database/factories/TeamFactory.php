<?php

/*
 * Insider Champions League Trial Project - Made with ❤
 * @author xerenahmed (Eren A. Akyol)
 */

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Team>
 */
class TeamFactory extends Factory
{
	/**
	 * Define the model's default state.
	 *
	 * @return array<string, mixed>
	 */
	public function definition() : array
	{
		return [
			'name' => $this->faker->name(),
			'flag_url' => $this->faker->imageUrl(),
			'featured_player_url' => $this->faker->imageUrl(),
			'texture_colors' => '#000,#fff',
			'strength' => $this->faker->numberBetween(6,20) * 5,
		];
	}
}
