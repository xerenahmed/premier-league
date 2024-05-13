<?php

/*
 * Insider Champions League Trial Project - Made with ❤
 * @author xerenahmed (Eren A. Akyol)
 */

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeagueTableResource extends JsonResource
{
	/** @var Collection<Team> */
	public Collection $teams;

	public function __construct($resource, Collection $teams){
		parent::__construct($resource);
		$this->teams = $teams;
	}

	public function toArray(Request $request) : array
	{
		return [
			'league' => new LeagueResource($this),
			'rows' => LeagueTableRowResource::collection($this->teams),
		];
	}
}
