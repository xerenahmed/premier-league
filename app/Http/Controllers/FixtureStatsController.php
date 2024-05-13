<?php

/*
 * Insider Champions League Trial Project - Made with ❤
 * @author xerenahmed (Eren A. Akyol)
 */

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\Interfaces\FixtureStatsRepository;
use Illuminate\Http\Request;

class FixtureStatsController extends Controller {
	public function __construct(
		public FixtureStatsRepository $fixtureStatsRepository,
	){}

	public function update($fixtureStats, Request $request){
		$update = $this->fixtureStatsRepository->updateFixtureStats($fixtureStats, [
			'goals' => $request->input('goals')
		]);
		$this->fixtureStatsRepository->recalculateFixturePoints();
		return $update;
	}
}
