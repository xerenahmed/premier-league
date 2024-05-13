<?php

/*
 * Insider Champions League Trial Project - Made with ❤
 * @author xerenahmed (Eren A. Akyol)
 */

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WeeklyFixtureResource extends JsonResource
{
	public function toArray(Request $request) : array
	{
		return [
			'round' => $this->first()->round,
			'fixtures' => FixtureResource::collection($this->resource),
		];
	}
}
