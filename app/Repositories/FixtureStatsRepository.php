<?php

/*
 * Insider Champions League Trial Project - Made with ❤
 * @author xerenahmed (Eren A. Akyol)
 */

declare(strict_types=1);

namespace App\Repositories;

use App\Models\FixtureStats;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class FixtureStatsRepository implements \App\Services\Interfaces\FixtureStatsRepository {

	public function getAllFixtureStats() : Collection{
		return FixtureStats::all();
	}

	public function getFixtureStats($id) : FixtureStats{
		return FixtureStats::findOrFail($id);
	}

	public function createFixtureStats(array $data) : FixtureStats{
		return FixtureStats::create($data);
	}

	public function updateFixtureStats($id, array $data) : FixtureStats{
		FixtureStats::whereKey($id)->update($data);
		Cache::flush();
		return FixtureStats::findOrFail($id);
	}

	public function recalculateFixturePoints() : void{
		$allStats = $this->getAllFixtureStats();
		foreach($allStats->groupBy('fixture_id')->toArray() as [$stats, $opponentStats]){
			if ($stats['goals'] > $opponentStats['goals']) {
				$stats['points'] = FixtureStats::POINTS_WIN;
				$opponentStats['points'] = FixtureStats::POINTS_LOSE;
			} elseif ($stats['goals'] === $opponentStats['goals']){
				$stats['points'] = $opponentStats['points'] = FixtureStats::POINTS_DRAW;
			} else{
				$stats['points'] = FixtureStats::POINTS_LOSE;
				$opponentStats['points'] = FixtureStats::POINTS_WIN;
			}
			$this->updateFixtureStats($stats['id'], $stats);
			$this->updateFixtureStats($opponentStats['id'], $opponentStats);
		}
	}

	public function deleteFixtureStats($id) : bool{
		return FixtureStats::destroy($id) > 0;
	}

	public function getStatsFromTeam(int $teamId) : Collection{
		return FixtureStats::where('team_id', $teamId)->orderBy('id', 'desc')->get();
	}

	public function getTeamStats(int $fixtureId, int $teamId) : ?FixtureStats{
		return FixtureStats::where('fixture_id', $fixtureId)->where('team_id', $teamId)->first();
	}

	public function getOpponentStats(int $fixtureId, int $teamId) : ?FixtureStats{
		return FixtureStats::where('fixture_id', $fixtureId)->where('team_id', '<>', $teamId)->first();
	}

	public function getLastStatsFromTeam(int $teamId, int $limit) : Collection{
		return FixtureStats::where('team_id', $teamId)->orderBy('id', 'desc')->take($limit)->get();
	}
}
