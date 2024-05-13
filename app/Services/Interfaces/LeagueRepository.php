<?php

/*
 * Insider Champions League Trial Project - Made with ❤
 * @author xerenahmed (Eren A. Akyol)
 */

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\Models\League;
use App\Models\Team;
use Illuminate\Support\Collection;

interface LeagueRepository {
	// There will be just one league for this application
	// so get first or create new one
	public function getLeague() : League;

	public function startLeague() : bool;

	public function isLeagueStarted() : bool;

	public function generateFixtures() : void;

	public function resetLeague() : void;

	public function playNextWeek() : bool;

	public function playAllWeeks() : bool;

	public function hasAllPlayed() : bool;

	public function getWinner() : ?Team;

	public function getPredictions() : ?Collection;
}
