<?php

/*
 * Insider Champions League Trial Project - Made with ❤
 * @author xerenahmed (Eren A. Akyol)
 */

declare(strict_types=1);

namespace App\Http\Resources;

use App\Services\Interfaces\TeamRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use function app;
use function array_merge;

class LeagueTableRowResource extends JsonResource
{
	public function toArray(Request $request) : array
	{
		$teamRepository = app()->make(TeamRepository::class);
		return array_merge($teamRepository->getStats($this->resource), [
			'id' => $this->id,
			'team' => new TeamResource($this->resource),
		]);
	}
}
