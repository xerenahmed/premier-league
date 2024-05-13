<?php

/*
 * Insider Champions League Trial Project - Made with ❤
 * @author xerenahmed (Eren A. Akyol)
 */

declare(strict_types=1);

namespace App\Http\Resources;

use App\Repositories\FixtureStatsRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use function app;

class FixtureResource extends JsonResource
{
	public function toArray(Request $request) : array
	{
		/** @var FixtureStatsRepository $repository */
		$repository = app()->make(FixtureStatsRepository::class);

		$homeTeamStats = $repository->getTeamStats($this->id, $this->home_team_id);
		$awayTeamStats = $repository->getTeamStats($this->id, $this->away_team_id);
		return [
			'id' => $this->id,
			'round' => $this->round,
			'homeTeam' => new TeamResource($this->homeTeam),
			'awayTeam' => new TeamResource($this->awayTeam),
			'homeTeamStats' => new FixtureStatsResource($homeTeamStats),
			'awayTeamStats' => new FixtureStatsResource($awayTeamStats),
		];
	}
}
