<?php

/*
 * Insider Champions League Trial Project - Made with ❤
 * @author xerenahmed (Eren A. Akyol)
 */

declare(strict_types=1);

namespace App\Http\Resources;

use App\Repositories\LeagueRepository;
use App\Services\Interfaces\FixtureRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use function app;

class LeagueResource extends JsonResource
{
	public function toArray(Request $request) : array
	{
		$fixtureRepository = app()->make(FixtureRepository::class);
		$leagueRepository = app()->make(LeagueRepository::class);

		return [
			'name' => $this->name,
			'round' => $this->round,
			'hasAllPlayed' => $leagueRepository->hasAllPlayed(),
			'winner' => new TeamResource($leagueRepository->getWinner()),
			'hasFixtures' => $fixtureRepository->hasAnyFixture(),
			'started' => $this->started
		];
	}
}
