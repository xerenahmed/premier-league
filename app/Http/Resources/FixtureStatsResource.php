<?php

/*
 * Insider Champions League Trial Project - Made with ❤
 * @author xerenahmed (Eren A. Akyol)
 */

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FixtureStatsResource extends JsonResource
{
	public function toArray(Request $request) : array
	{
		return [
			'id' => $this->id,
			'fixture_id' => $this->fixture_id,
			'team_id' => $this->team_id,
			'points' => $this->points,
			'goals' => $this->goals,
			'fouls' => $this->fouls,
		];
	}
}
