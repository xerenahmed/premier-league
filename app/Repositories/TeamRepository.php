<?php

/*
 * Insider Champions League Trial Project - Made with ❤
 * @author xerenahmed (Eren A. Akyol)
 */

declare(strict_types=1);

namespace App\Repositories;

use App\Models\FixtureStats;
use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;

class TeamRepository implements \App\Services\Interfaces\TeamRepository {

	public function __construct(
		public FixtureStatsRepository $fixtureStatsRepository
	){}

	/**
	 * Returns all the teams.
	 */
	public function getTeams() : Collection{
		return Team::all();
	}

	/**
	 * Returns single team by id.
	 */
	public function getTeam($id) : Team{
		return Team::findOrFail($id);
	}

	public function createTeam(array $data) : Team{
		return Team::create($data);
	}

	public function updateTeam($id, array $data) : Team{
		Team::whereKey($id)->update($data);
		return Team::findOrFail($id);
	}

	public function deleteTeam(int $id) : bool{
		return Team::findOrFail($id)->delete();
	}

	/**
	 * Returns teams by rank from sum of points and goals.
	 */
	public function getTeamsByRank(string $order = 'DESC') : Collection{
		return $this->getTeams()->sortBy(function(Team $team) {
			// Multiply points with 999 because goals shouldn't affect the order.
			$stats = $this->fixtureStatsRepository->getStatsFromTeam($team->id);
			return ($stats->sum('points') * 999) + $stats->sum('goals');
		}, descending: $order === 'DESC');
	}

	/**
	 * Get overall stats of the Team.
	 */
	public function getStats(Team $team) : array{
		$fixtureStats = $this->fixtureStatsRepository->getStatsFromTeam($team->id);

		$gd = 0;
		$fixtureStats->each(function (FixtureStats $fixtureStat) use (&$gd) {
			$opponent = $this->fixtureStatsRepository->getOpponentStats($fixtureStat->fixture_id, $fixtureStat->team_id);
			$gd += $fixtureStat->goals - $opponent->goals;
		});
		return [
			'matches' => $fixtureStats->count(),
			'wins' => $fixtureStats->where('points', 3)->count(),
			'draws' => $fixtureStats->where('points', 1)->count(),
			'loses' => $fixtureStats->where('points', 0)->count(),
			'goals' => $fixtureStats->sum('goals'),
			'goal_difference' => $gd,
			'points' => $fixtureStats->sum('points'),
			'fouls' => $fixtureStats->sum('points'),
		];
	}
}
