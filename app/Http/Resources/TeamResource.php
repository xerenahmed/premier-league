<?php

/*
 * Insider Champions League Trial Project - Made with ❤
 * @author xerenahmed (Eren A. Akyol)
 */

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamResource extends JsonResource
{
	public function toArray(Request $request) : array
	{
		return [
			'id' => $this->id,
			'name' => $this->name,
			'flag_url' => $this->flag_url,
			'featured_player_url' => $this->featured_player_url,
			'texture_colors' => $this->texture_colors
		];
	}
}
