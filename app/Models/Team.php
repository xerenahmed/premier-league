<?php

/*
 * Insider Champions League Trial Project - Made with ❤
 * @author xerenahmed (Eren A. Akyol)
 */

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model {
	use HasFactory;

	public $timestamps = false;

	protected $fillable = [
		'name',
		'strength',
		'texture_colors',
		'flag_url',
		'featured_player_url',
	];

	protected $casts = [
		'texture_colors' => 'array'
	];

	public function homeFixtures() : HasMany {
		return $this->hasMany(Fixture::class, 'home_team_id');
	}

	public function awayFixtures() : HasMany {
		return $this->hasMany(Fixture::class, 'away_team_id');
	}

	public function fixtureStats() : HasMany {
		return $this->hasMany(FixtureStats::class);
	}
}
