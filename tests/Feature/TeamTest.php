<?php

/*
 * Insider Champions League Trial Project - Made with ❤
 * @author xerenahmed (Eren A. Akyol)
 */

declare(strict_types=1);

use App\Models\Fixture;
use App\Models\FixtureStats;
use App\Models\Team;
use App\Repositories\LeagueRepository;
use App\Repositories\TeamRepository;
use Database\Seeders\TeamSeeder;
use Illuminate\Support\Collection;

it('can create team', function(){
	$team = Team::factory()->create();
	expect($team)->toBeInstanceOf(Team::class)
		->and($team->name)->toBeString()
		->and($team->strength)->toBeInt()
		->and($team->id)->toBeInt();

	$this->assertDatabaseCount('teams', 1);
});

it('seeder should create 4 teams', function(){
	$this->seed(TeamSeeder::class);

	expect(Team::count())->toBe(4);
});

it('repository should work', function(){
	/** @var TeamRepository $repository */
	$repository = app()->get(App\Services\Interfaces\TeamRepository::class);

	expect($repository)->toBeInstanceOf(TeamRepository::class);

	Team::factory()->count(10)->create();
	$teams = $repository->getTeams();
	expect($teams)
		->toBeInstanceOf(Collection::class)
		->and($teams->count())->toBe(10)
		->and($teams)->each->toBeInstanceOf(Team::class);

	/** @var Team $sample */
	$sample = $teams->first();
	expect($repository->deleteTeam($sample->id))->toBeTrue();
	$this->assertDatabaseCount('teams', 9);

	$created = $repository->createTeam($sample->toArray());
	expect($created)->toBeInstanceOf(Team::class)
		->and($created->id)->toBeInt();
	$this->assertDatabaseCount('teams', 10);

	expect($repository->getTeam($created->id))->toBeInstanceOf(Team::class);

	$team = $repository->updateTeam($created->id, ['name' => 'Eren FC']);
	expect($team)
		->toBeInstanceOf(Team::class)
		->and($team->name)->toBe('Eren FC');
});

it('relations should work', function() {
	$teams = Team::factory()->count(4)->create();
	$league = app()->make(LeagueRepository::class);
	$league->generateFixtures();
	$league->startLeague();
	$league->playAllWeeks();

	$teams->each(function($team) use ($league) {
		expect($team->homeFixtures)->toBeInstanceOf(Collection::class)->each->toBeInstanceOf(Fixture::class);
		expect($team->awayFixtures)->toBeInstanceOf(Collection::class)->each->toBeInstanceOf(Fixture::class);
		expect($team->fixtureStats)->toBeInstanceOf(Collection::class)->each->toBeInstanceOf(FixtureStats::class);
	});
});
describe('API', function(){
	it('should get all teams', function(){
		$this->seed(TeamSeeder::class);
		$this->get(route('team.index'))->assertStatus(200)
			->assertJsonStructure([
				'data' => [
					[
						'name',
						'id',
						'flag_url'
					]
				]
			]);
	});

    it('should get team stats', function(){
        $this->seed(TeamSeeder::class);
        $league = app()->make(LeagueRepository::class);
        $league->generateFixtures();
        $league->startLeague();
        $league->playAllWeeks();
        $this->get(route('team.stats', $league->getWinner()))->assertStatus(200)
            ->assertJsonStructure([
                'matches',
                'points',
                'goals'
            ]);
    });
});
