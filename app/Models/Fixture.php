<?php

/*
 * Insider Champions League Trial Project - Made with ❤
 * @author xerenahmed (Eren A. Akyol)
 */

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Fixture extends Model
{
	use HasFactory;

	public $timestamps = false;

	protected $fillable = [
		'league_id',
		'home_team_id',
		'away_team_id',
		'round'
	];

	public function league() : BelongsTo{
		return $this->belongsTo(League::class);
	}

	public function homeTeam() : BelongsTo{
		return $this->belongsTo(Team::class, 'home_team_id');
	}

	public function awayTeam() : BelongsTo{
		return $this->belongsTo(Team::class, 'away_team_id');
	}

	public function stats() : HasMany{
		return $this->hasMany(FixtureStats::class);
	}

	protected static function boot(){
		parent::boot();

		static::deleting(function($fixture){
			$fixture->stats()->delete();
		});
	}
}
