<?php

/*
 * Insider Champions League Trial Project - Made with ❤
 * @author xerenahmed (Eren A. Akyol)
 */

declare(strict_types=1);

use App\Http\Resources\WeeklyFixtureResource;
use App\Models\Fixture;
use App\Models\FixtureStats;
use App\Models\Team;
use App\Services\Interfaces\FixtureRepository;
use App\Services\Interfaces\FixtureStatsRepository;
use App\Services\Interfaces\LeagueRepository;
use App\Services\Interfaces\TeamRepository;
use Illuminate\Database\Eloquent\Collection;

beforeEach(function(){
	$this->leagueRepository = app()->make(LeagueRepository::class);
	$this->teamRepository = app()->make(TeamRepository::class);
	$this->fixtureRepository = app()->make(FixtureRepository::class);
	$this->fixtureStatsRepository = app()->make(FixtureStatsRepository::class);

	// generate dataset
	Team::factory()->count(4)->create();
	$this->leagueRepository->generateFixtures();
});

describe('UNIT', function(){
	it('every team should play twice with each other', function(){
		$teams = $this->teamRepository->getTeams();
		foreach($teams->crossJoin($teams) as [$team1, $team2]){
			if($team2->id === $team1->id){
				continue;
			}

			expect(Fixture::query()->where('home_team_id', $team1->id)->where('away_team_id', $team2->id)->count())->toBe(1);
			expect(Fixture::query()->where('home_team_id', $team2->id)->where('away_team_id', $team1->id)->count())->toBe(1);
		}
	});

	it('should get last round correctly', function() {
	   expect($this->fixtureRepository->getLastFixtureRound())->toBe(Fixture::query()->latest('id')->first()->round);
	});

	it('can generate correct fixtures with 4 teams', function(){
		$this->assertDatabaseCount('teams', 4);
		$this->assertDatabaseCount('fixtures', 4 * 3);
		expect($this->fixtureRepository->getFixturesWeekly()->count())->toBe(6);

		for($i = 1; $i <= 4; $i++){
			$sample = Fixture::query()->skip($i)->first();
			$opposite = Fixture::query()->skip($i + (4 * 3 / 2))->first();
			expect($sample->home_team_id)->toBe($opposite->away_team_id)
				->and($sample->away_team_id)->toBe($opposite->home_team_id);
		}
	});

	it('can generate correct fixtures with 6 teams', function(){
		Team::factory()->count(2)->create(); // 2 more
		$this->leagueRepository->resetLeague();
		$this->leagueRepository->generateFixtures();
		$this->assertDatabaseCount('teams', 6);
		expect($this->fixtureRepository->getFixturesWeekly()->count())->toBe(6 * 5 / 3);
		$this->assertDatabaseCount('fixtures', 6 * 5);

		$teams = $this->teamRepository->getTeams();
		foreach($teams->pluck('id')->crossJoin($teams->pluck('id')) as [$team1, $team2]){
			if($team2 === $team1){
				continue;
			}

			expect(Fixture::query()->where('home_team_id', $team1)->where('away_team_id', $team2)->count())->toBe(1);
			expect(Fixture::query()->where('home_team_id', $team2)->where('away_team_id', $team1)->count())->toBe(1);
		}

		for($i = 1; $i <= 4; $i++){
			$sample = Fixture::query()->skip($i)->first();
			$opposite = Fixture::query()->skip($i + (6 * 5 / 2))->first();
			expect($sample->home_team_id)->toBe($opposite->away_team_id)
				->and($sample->away_team_id)->toBe($opposite->home_team_id);
		}
	});

	it('should delete stats when fixture deleted', function(){
		$fixtures = $this->fixtureRepository->getFixtures();
		expect($fixtures)->not->toBeEmpty();

		$fixtures->each(function($fixture){
			$this->fixtureRepository->deleteFixture($fixture);
		});

		$fixtures = $this->fixtureRepository->getFixtures();
		expect($fixtures)->toBeEmpty();
		$fixtureStats = $this->fixtureStatsRepository->getAllFixtureStats();
		expect($fixtureStats)->toBeEmpty();
		$this->assertDatabaseEmpty('fixtures');
		$this->assertDatabaseEmpty('fixture_stats');
	});

	it('should work with relations', function(){
		$league = $this->leagueRepository->getLeague();
		$this->fixtureRepository->getFixtures()->each(function(Fixture $fixture) use ($league){
			expect($fixture->league->name)->toBe($league->name);
			expect($fixture->homeTeam)->toBeInstanceOf(Team::class);
			expect($fixture->awayTeam)->toBeInstanceOf(Team::class);
			expect($fixture->stats)->toBeInstanceOf(Collection::class)
				->each->toBeInstanceOf(FixtureStats::class);
		});
	});

	it('should work with stats relations', function(){
		$this->leagueRepository->playAllWeeks();

		$this->fixtureStatsRepository->getAllFixtureStats()->each(function(FixtureStats $stats) {
			expect($stats->fixture)->toBeInstanceOf(Fixture::class);
			expect($stats->team)->toBeInstanceOf(Team::class);

			$stats->points = 3;
			expect($stats->isWin)->toBeTrue()
				->and($stats->isDraw)->toBeFalse()
				->and($stats->isLose)->toBeFalse();
			$stats->points = 0;
			expect($stats->isLose)->toBeTrue()
				->and($stats->isDraw)->toBeFalse()
				->and($stats->isWin)->toBeFalse();
			$stats->points = 1;
			expect($stats->isDraw)->toBeTrue()
				->and($stats->isWin)->toBeFalse()
				->and($stats->isLose)->toBeFalse();
		});
	});
});

describe('REPOSITORY', function(){
	it('should get a valid fixture', function(){
		expect($this->fixtureRepository->getFixture(Fixture::first()->id))
			->toBeInstanceOf(Fixture::class);
	});

	it('should get a weekly fixtures', function(){
		$fixtures = $this->fixtureRepository->getFixturesWeekly();
		expect($fixtures->count())
			->toBe($this->fixtureRepository->getFixtures()->count() / 2);
		expect($fixtures)->each->toBeInstanceOf(Collection::class);
	});

	it('should update a fixture', function(){
		$fixture = Fixture::first();
		$newRound = rand(0, 5);
		$updated = $this->fixtureRepository->updateFixture($fixture->id, ['round' => $newRound]);
		expect($updated)
			->toBeInstanceOf(Fixture::class)
			->and($updated->round)->toBe($newRound);
	});
});

describe('API', function(){
	it('can get fixtures', function(){
		$this->get(route('fixtures.index'))
			->assertSuccessful()
			->assertJsonCount(2, 'data.fixtures');
	});

    it('can get all fixtures', function(){
        $this->get(route('fixtures.allWeeks'))
            ->assertSuccessful()
            ->assertJsonCount(6, 'data');
    });

	it('can get past fixtures', function(){
		$resp = $this->get(route('fixtures.index'))->json('data');
		$this->leagueRepository->playNextWeek();
		$pastWeekResp = $this->get(route('fixtures.pastWeek'))->json('data');
		expect($resp['fixtures'][0]['id'])->toBe($pastWeekResp['fixtures'][0]['id']);
	});

	it('can get fixtures by weekly', function(){
		$resp = $this->get(route('fixtures.weekly'));
		$resp->assertSuccessful()
			->assertJsonCount(6, 'data');
		expect($resp->json('data'))->each(function(Pest\Expectation $item){
			expect($item->value['round'])->toBeInt();
			expect($item->value['fixtures'])->toHaveCount(2);
		});
	});

	it('can generate fixtures', function(){
		$this->leagueRepository->resetLeague();
		$this->post(route('fixtures.generate'))->assertSuccessful();
		$this->assertDatabaseCount('fixtures', 12);
	});

	it('should throw error when there is no team', function(){
		$this->leagueRepository->resetLeague();
		Team::query()->delete();
		$this->post(route('fixtures.generate'))->assertBadRequest();
		$this->assertDatabaseCount('fixtures', 0);
	});
});
