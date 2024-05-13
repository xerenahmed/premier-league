<?php

/*
 * Insider Champions League Trial Project - Made with ❤
 * @author xerenahmed (Eren A. Akyol)
 */

declare(strict_types=1);

namespace App\Repositories;

use App\Exceptions\NoTeamsFoundException;
use App\Helpers\CacheKey;
use App\Models\Fixture;
use App\Models\League;
use App\Models\Team;
use App\Services\Interfaces\FixtureRepository;
use App\Services\Interfaces\FixtureStatsRepository;
use App\Services\Interfaces\TeamRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use function array_chunk;
use function array_filter;
use function array_merge;
use function collect;
use function count;
use function now;
use function range;

class LeagueRepository implements \App\Services\Interfaces\LeagueRepository{
	public function __construct(
		protected TeamRepository         $teamRepository,
		protected FixtureRepository      $fixtureRepository,
		protected FixtureStatsRepository $fixtureStatsRepository,
	){
	}

	/**
	 * There will be just one league for this application
	 * so get first or create new one
	 */
	public function getLeague() : League{
		return League::firstOrCreate([], [
			'name' => 'Insider Champions League',
			'round' => 0,
			'started' => false,
		]);
	}

	/**
	 * Is league started?
	 */
	public function isLeagueStarted() : bool{
		return $this->getLeague()->started;
	}

	/**
	 * Should be called after fixtures created.
	 */
	public function startLeague() : bool{
		return $this->getLeague()->update([
			'started' => true,
		]);
	}

	/**
	 * Generates fixtures.
	 *
	 * @throws NoTeamsFoundException
	 * @throws \Exception
	 */
	public function generateFixtures() : void{
		$teams = $this->teamRepository->getTeams()->pluck('id');
		$teamCount = $teams->count();

		if($teamCount === 0){
			throw new NoTeamsFoundException();
		}

		if ($teamCount % 2 !== 0) {
			throw new \Exception('There is one team extra which is not expected');
		}

		$firstHalf = $teams->slice(0, $teamCount / 2)->values();
		$firstHalfManipulated = clone $firstHalf;
		$secondHalf = $teams->slice($teamCount / 2, $teamCount)->values();
		$secondHalfManipulated = clone $secondHalf;
		$fixtures = [];

		// cross matching
		for ($i = 0; $i < $teamCount / 2; $i++) {
			for ($j = 0; $j < ($teamCount / 2); $j++) {
				$fixtures[] = [
					'home_team_id' => $firstHalfManipulated[$j],
					'away_team_id' => $secondHalf[$j],
				];
			}
			$firstHalfManipulated->push($firstHalfManipulated->shift());
		}

		// inner matching
		// [a, b, c] [d, e, f]
		// [a,b] [d, e]
		// [b,c] [e, f]
		for ($i = 0; $i < count($firstHalfManipulated); $i++){
			$fixtures[] = [
				'home_team_id' => $firstHalfManipulated[0],
				'away_team_id' => $firstHalfManipulated[1],
			];
			$fixtures[] = [
				'home_team_id' => $secondHalfManipulated[0],
				'away_team_id' => $secondHalfManipulated[1],
			];
			$firstHalfManipulated->push($firstHalfManipulated->shift());
			$secondHalfManipulated->push($secondHalfManipulated->shift());
			if (count($firstHalfManipulated) === 2) {
				break;
			}
		}

		$finalFixtures = $fixtures;
		foreach($fixtures as $fixture){
			$finalFixtures[] = [
				'home_team_id' => $fixture['away_team_id'],
				'away_team_id' => $fixture['home_team_id'],
			];
		}

		$league = $this->getLeague();
		$round = 0;
		foreach(array_chunk($finalFixtures, $teamCount / 2) as $fixtures){
			foreach($fixtures as $fixture){
				$this->fixtureRepository->createFixture(array_merge($fixture, [
					'home_team_id' => $fixture['home_team_id'],
					'away_team_id' => $fixture['away_team_id'],
					'league_id' => $league->id,
					'round' => $round
				]));
			}
			$round++;
		}
	}

	/**
	 * Delete fixtures and reset the league.
	 */
	public function resetLeague() : void{
		$this->fixtureRepository->getFixtures()->each(function($fixture){
			$this->fixtureRepository->deleteFixture($fixture);
		});

		$this->getLeague()->update([
			'round' => 0,
			'started' => false,
		]);
		Cache::flush();
	}

	/**
	 * Play fixtures in current round and to next round.
	 */
	public function playNextWeek() : bool{
		$league = $this->getLeague();
		if($this->hasAllPlayed()){
			return false;
		}
		$this->fixtureRepository->getFixturesByRound($league->round)
			->each(function(Fixture $fixture){
				$this->fixtureRepository->play($fixture);
			});
		$league->update([
			'round' => $league->round + 1
		]);
		return true;
	}

	/**
	 * Plays all the fixtures in order.
	 */
	public function playAllWeeks() : bool{
		$i = 0;
		while($this->playNextWeek()){
			if($i++ >= 50){
				// @codeCoverageIgnoreStart
				throw new \UnexpectedValueException('Failed to play all weeks');
				// @codeCoverageIgnoreEnd
			}
		}
		return $i > 0;
	}

	/**
	 * Returns that is there any waiting fixture left.
	 * Used in frontend to render the final form.
	 */
	public function hasAllPlayed() : bool{
		return $this->fixtureRepository->getFixturesByRound($this->getLeague()->round)->isEmpty();
	}

	/**
	 * Should be called after checked by @method hasAllPlayed()
	 * Returns as the first team in the ranking.
	 */
	public function getWinner() : ?Team{
		$teams = $this->teamRepository->getTeamsByRank();
		return $teams->first();
	}

	public function getPredictions() : ?Collection{
		$round = $this->getLeague()->round;
		// Check if the league round is less than 3. If so, return null, as predictions are not available until a certain round.
		if($round < 3){
			return null;
		}

		// We are caching this expensive operation.
		$cacheKey = CacheKey::LEAGUE_PREDICTION . ':' . $round;
		if ($cached = Cache::get($cacheKey)) {
			return $cached;
		}

		/**
		 *  Iterate 25 times for performance instead the best accuracy.
		 *    i. Begin a database transaction for simulation purpose.
		 *    ii. Iterate over each fixture left in the league
		 *      - Simulate playing the fixture using the fixtureRepository->play() method.
		 *    iii. Retrieve the latest team rankings.
		 *    iv. Roll back the database transaction to undo the simulated matches.
		 *    v. Determine the ranking position of the current team in the latest rankings.
		 *    vi. Store the ranking position in the $predictions array for the current team.
		 */
		$teams = $this->teamRepository->getTeams();
		$predictions = [];
		foreach($teams as $team){
			foreach(range(1, 25) as $_){
				DB::beginTransaction();
				$this->fixtureRepository->getFixturesLeft()->each(function(Fixture $fixture) use ($team, $predictions){
					$this->fixtureRepository->play($fixture);
				});
				$latestRankings = $this->teamRepository->getTeamsByRank();
				DB::rollBack();

				$index = 0;
				foreach($latestRankings as $ranking){
					$index++;
					if($ranking->id === $team->id){
						break;
					}
				}
				$predictions[$team->id][] = $index;
			}
		}
		$data = [];
		foreach($predictions as $teamId => $rankings){
			$data[] = [
				'team' => $teams->where('id', $teamId)->first(),
				// Multiply winnings by 4 because we simulated it 25 times.
				'chance' => count(array_filter($rankings, fn($rank) => $rank === 1)) * 4
			];
		}
		$data = collect($data)->sortByDesc('chance');
		Cache::put($cacheKey, $data, now()->addMinutes(5));
		return $data;
	}
}
