<?php

/*
 * Insider Champions League Trial Project - Made with ❤
 * @author xerenahmed (Eren A. Akyol)
 */

declare(strict_types=1);

use App\Models\FixtureStats;
use App\Models\League;
use App\Models\Team;
use App\Services\Interfaces\FixtureRepository;
use App\Services\Interfaces\FixtureStatsRepository;
use App\Services\Interfaces\LeagueRepository;

beforeEach(function() {
	$this->leagueRepository = app()->make(LeagueRepository::class);
	$this->fixtureRepository = app()->make(FixtureRepository::class);
	$this->fixtureStatsRepository = app()->make(FixtureStatsRepository::class);

	// generate dataset
	Team::factory()->count(4)->create();
	League::factory()->create();
	$this->leagueRepository->generateFixtures();
	$this->leagueRepository->playAllWeeks();
});

describe('repository', function() {
	it('should get a valid fixture', function(){
		expect($this->fixtureStatsRepository->getFixtureStats(FixtureStats::first()->id))
			->toBeInstanceOf(FixtureStats::class);
	});

	it('should update a fixture', function(){
		$fixtureStats = FixtureStats::first();
		$newGoals = rand(0, 5);
		$updated = $this->fixtureStatsRepository->updateFixtureStats($fixtureStats->id, ['goals' => $newGoals]);
		expect($updated)
			->toBeInstanceOf(FixtureStats::class)
			->and($updated->goals)->toBe($newGoals);
	});

	it('should delete a fixture', function(){
		$fixtureStats = FixtureStats::first();
		$this->fixtureStatsRepository->deleteFixtureStats($fixtureStats->id);
		$this->assertDatabaseMissing('fixture_stats', ['id' => $fixtureStats->id]);
	});
});

describe('controller', function() {
	it('should update a fixture stat', function(){
		$sample = FixtureStats::first();
		$this->put('/api/fixtures/stats/' . $sample->id, [
			'goals' => 10
		])->assertSuccessful();
		$sample->refresh();
		expect($sample->goals)->toBe(10);
	});
});
