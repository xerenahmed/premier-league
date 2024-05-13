<?php

/*
 * Insider Champions League Trial Project - Made with ❤
 * @author xerenahmed (Eren A. Akyol)
 */

declare(strict_types=1);

namespace App\Http\Resources;

use App\Services\Interfaces\FixtureRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use function app;

class LeagueFixturesResource extends JsonResource
{
	public function toArray(Request $request) : array
	{
		return [
			'league' => new LeagueResource($this),
			'fixtures' => FixtureResource::collection(app()->make(FixtureRepository::class)->getFixturesByRound($this->round))
		];
	}
}
