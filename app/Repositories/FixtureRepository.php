<?php

/*
 * Insider Champions League Trial Project - Made with ❤
 * @author xerenahmed (Eren A. Akyol)
 */

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Fixture;
use App\Models\FixtureStats;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Random\Randomizer;
use function intval;
use function max;
use function rand;
use function round;

class FixtureRepository implements \App\Services\Interfaces\FixtureRepository{

	public function __construct(
		public FixtureStatsRepository $fixtureStatsRepository
	){}

	public function getFixtures() : Collection{
		return Fixture::all();
	}

	public function getFixture($id) : Fixture{
		return Fixture::findOrFail($id);
	}

	public function createFixture(array $data) : Fixture{
		return Fixture::create($data);
	}

	public function updateFixture($id, array $data) : Fixture{
		Fixture::whereKey($id)->update($data);
		return Fixture::findOrFail($id);
	}

	public function deleteFixture(Fixture $fixture) : bool{
		return $fixture->delete();
	}

	/**
	 * @return Collection<Fixture>
	 */
	public function getFixturesByRound($round) : Collection{
		return Fixture::query()->where('round', $round)->get();
	}

	/**
	 * @return Collection<Fixture>
	 */
	public function getFixturesLeft() : Collection{
		return Fixture::whereDoesntHave('stats')->get();
	}

	/**
	 * Get fixtures grouped by round.
	 *
	 * @return \Illuminate\Support\Collection<Fixture>
	 */
	public function getFixturesWeekly() : \Illuminate\Support\Collection{
		return Fixture::query()->orderBy('round', 'asc')->get()->groupBy('round');
	}

	public function hasAnyFixture() : bool{
		return Fixture::query()->exists();
	}

	/**
	 * Plays the fixture. Fixture stats will be created as a result for each team.
	 */
	public function play(Fixture $fixture) : void{
		$random = new Randomizer();
		$opponentStats = null;
		foreach ([$fixture->homeTeam, $fixture->awayTeam] as $i => $team){
			$isHomeTeam = $team->id === $fixture->home_team_id;
			$stats = [
				'fixture_id' => $fixture->id,
				'team_id' => $team->id,
				'points' => 0,
			];
			$lastMatches = $this->fixtureStatsRepository->getLastStatsFromTeam($team->id, 2);
			$stats['aggression'] = $lastMatches->where('points', 0)->count() * 20;

			$aggressionFactor = $stats['aggression'] > 0 ? (int) round(rand(0, $stats['aggression']) / 15) : 0;
			$goalkeeperMistake = $random->getInt(0, 10) === 0 ? 1 : 0;
			// Aggression downs luck
			$luck = $random->getInt(0, 5 + $aggressionFactor) === 0 ? 2 : 0;

			$strengthFactor = rand(0, 100 - $team->strength) < 4;
			$stats['goals'] = rand(0, 2 + $luck + $goalkeeperMistake + $aggressionFactor + rand(0, $isHomeTeam ? 1 : 0) + $strengthFactor);
			$stats['fouls'] = rand(0, 2 + intval(round((max($stats['aggression'], 1) / 10))));

			// Calculate points
			if ($i === 1) {
				if ($stats['goals'] > $opponentStats['goals']) {
					$stats['points'] = FixtureStats::POINTS_WIN;
					$opponentStats['points'] = FixtureStats::POINTS_LOSE;
				} elseif ($stats['goals'] === $opponentStats['goals']){
					$stats['points'] = $opponentStats['points'] = FixtureStats::POINTS_DRAW;
				} else {
					$stats['points'] = FixtureStats::POINTS_LOSE;
					$opponentStats['points'] = FixtureStats::POINTS_WIN;
				}

				DB::beginTransaction();
				$this->fixtureStatsRepository->createFixtureStats($stats);
				$this->fixtureStatsRepository->createFixtureStats($opponentStats);
				DB::commit();
			} else {
				$opponentStats = $stats;
			}
		}
	}

	public function getLastFixtureRound() : int{
		return Fixture::query()->max('round');
	}
}
