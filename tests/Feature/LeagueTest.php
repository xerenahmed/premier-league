<?php

/*
 * Insider Champions League Trial Project - Made with ❤
 * @author xerenahmed (Eren A. Akyol)
 */

declare(strict_types=1);

use App\Helpers\CacheKey;
use App\Models\Team;
use App\Repositories\LeagueRepository;
use App\Services\Interfaces\TeamRepository;
use Illuminate\Support\Facades\Cache;

beforeEach(function() {
	$this->leagueRepository = app()->make(LeagueRepository::class);
	$this->teamRepository = app()->make(TeamRepository::class);
});

it('can gets same league entry', function () {
	/** @var LeagueRepository $repository */
	$repository = $this->leagueRepository;

	$this->assertDatabaseCount('leagues', 0);
	expect($repository->getLeague()->id)->toBe($repository->getLeague()->id);
	$this->assertDatabaseCount('leagues', 1);
});

it('can generate fixtures', function() {
	/** @var LeagueRepository $repository */
	$repository = $this->leagueRepository;
	Team::factory()->count(4)->create();

	$repository->generateFixtures();
	$this->assertDatabaseCount('fixtures', 4 * 3);
});

describe('REPOSITORY', function(){
	it('should throw error when the team count is an odd number', function() {
		Team::factory()->count(1)->create();
		$this->leagueRepository->generateFixtures();
	})->throws('There is one team extra which is not expected');

	describe('prediction', function() {
		it('should return cached value', function() {
			/** @var LeagueRepository $repository */
			$repository = $this->leagueRepository;
			Team::factory()->count(4)->create();

			$repository->generateFixtures();
			$repository->startLeague();
			foreach(range(0, 4) as $_) {
				$repository->playNextWeek();
			}
			$league = $repository->getLeague();
			expect(Cache::has(CacheKey::LEAGUE_PREDICTION . ':' . $league->round))->toBeFalse();
			$initial = $repository->getPredictions();
			expect(Cache::has(CacheKey::LEAGUE_PREDICTION . ':' . $league->round))->toBeTrue();
			$cached = $repository->getPredictions();
			expect($cached)->toBe($initial);
		});

		it('can predict championship', function() {
			/** @var LeagueRepository $repository */
			$repository = $this->leagueRepository;
			Team::factory()->count(4)->create();

			$repository->generateFixtures();
			$repository->startLeague();
			foreach(range(0, 4) as $_) {
				$repository->playNextWeek();
			}
			$predictions = $repository->getPredictions();
			expect($predictions)->toHaveCount(4);
		});
	});
});

describe('API', function() {
	it('can index', function() {
		$this->get(route('league.index'))->assertStatus(201);
		$this->get(route('league.index'))->assertStatus(200)->assertJsonPath('data.name', 'Insider Champions League');
	});

	it('should not start simulation without any fixtures', function() {
		$this->post(route('league.start'))
			->assertNotFound();
	});

	it('should not modify when simulation already started', function() {
		$this->leagueRepository->startLeague();

		$this->post(route('league.start'))
			->assertNotModified();
	});

	it('should start simulation', function() {
		Team::factory()->count(4)->create();
		$this->leagueRepository->generateFixtures();

		$this->post(route('league.start'))
			->assertSuccessful();
	});

	it('should reset league', function() {
		Team::factory()->count(4)->create();
		$this->leagueRepository->generateFixtures();

		$this->post(route('league.start'))
			->assertSuccessful();

		$this->post(route('league.reset'))
			->assertSuccessful();

		$this->assertDatabaseCount('fixtures', 0);
		$this->assertDatabaseCount('fixture_stats', 0);
		$league = $this->leagueRepository->getLeague();
		expect($league->round)->toBe(0)
			->and($league->started)->toBeFalse();
	});

	it('should play next week', function(){
		Team::factory()->count(4)->create();
		$this->post(route('fixtures.generate'))
			->assertSuccessful();
		$this->post(route('league.start'))
			->assertSuccessful();

		$this->post(route('league.play-next-week'))->assertSuccessful();
	});

	it('should play all weeks', function(){
		Team::factory()->count(4)->create();
		$this->post(route('fixtures.generate'))
			->assertSuccessful();
		$this->post(route('league.start'))
			->assertSuccessful();

		$this->post(route('league.play-all-weeks'))->assertSuccessful();
		$this->get(route('league.index'))
			->assertJsonPath('data.hasAllPlayed', true)
			->assertJsonPath('data.winner', fn($val) => $val !== null);
	});

	it('should get table', function() {
		Team::factory()->count(4)->create();
		$this->post(route('fixtures.generate'))
			->assertSuccessful();
		$this->post(route('league.start'))
			->assertSuccessful();

		$this->post(route('league.play-next-week'))->assertSuccessful();
		$this->post(route('league.play-next-week'))->assertSuccessful();

		$this->get(route('league.table'))
			->assertJsonIsObject('data.league')
			->assertJsonIsArray('data.rows');
	});

	it('should get predictions', function() {
		Team::factory()->count(4)->create();
		$this->post(route('fixtures.generate'))
			->assertSuccessful();
		$this->post(route('league.start'))
			->assertSuccessful();

		$this->get(route('league.predictions'))
			->assertBadRequest();

		for($i = 0; $i < 4; $i++){
			$this->post(route('league.play-next-week'))->assertSuccessful();
		}

		$this->get(route('league.predictions'))
			->assertJsonIsArray('data')
			->assertJsonStructure([
				'data' => [[
					'team' => [
						'name'
					],
					'chance'
				]]
			]);
	});
});
