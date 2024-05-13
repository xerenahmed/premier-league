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

/**
 * There is two fixture stats for each fixture.
 * One is for home team and second is for away team.
 *
 * @property int $points - Gained points. Win 3, Draw 1, Lose 0
 * @property int $goals - Goal count.
 * @property int $fouls - Fouls committed.
 * @property int $aggression - A team loses in a row will play more aggressively and win more likely.
 */
class FixtureStats extends Model
{
	use HasFactory;

	public $timestamps = false;

	public const POINTS_WIN = 3;
	public const POINTS_DRAW = 1;
	public const POINTS_LOSE = 0;

	protected $fillable = [
		'fixture_id',
		'team_id',
		'points',
		'goals',
		'fouls',
		'aggression'
	];

	public function team() : BelongsTo {
		return $this->belongsTo(Team::class);
	}

	public function fixture() : BelongsTo {
		return $this->belongsTo(Fixture::class);
	}

	public function getIsWinAttribute() : bool {
		return $this->points === self::POINTS_WIN;
	}

	public function getIsDrawAttribute() : bool {
		return $this->points === self::POINTS_DRAW;
	}

	public function getIsLoseAttribute() : bool {
		return $this->points === self::POINTS_LOSE;
	}
}
