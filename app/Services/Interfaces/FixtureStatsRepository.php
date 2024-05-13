<?php

/*
 * Insider Champions League Trial Project - Made with ❤
 * @author xerenahmed (Eren A. Akyol)
 */

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\Models\FixtureStats;
use Illuminate\Support\Collection;

interface FixtureStatsRepository {
	public function getAllFixtureStats() : Collection;
	public function getFixtureStats($id) : FixtureStats;
	public function createFixtureStats(array $data) : FixtureStats;
	public function updateFixtureStats($id, array $data) : FixtureStats;
	public function deleteFixtureStats($id) : bool;
	public function recalculateFixturePoints() : void;
	public function getStatsFromTeam(int $teamId) : Collection;
	public function getTeamStats(int $fixtureId, int $teamId) : ?FixtureStats;
	public function getOpponentStats(int $fixtureId, int $teamId) : ?FixtureStats;
	public function getLastStatsFromTeam(int $teamId, int $limit) : Collection;
}
