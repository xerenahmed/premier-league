<?php

/*
 * Insider Champions League Trial Project - Made with ❤
 * @author xerenahmed (Eren A. Akyol)
 */

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exceptions\NoTeamsFoundException;
use App\Http\Resources\FixtureResource;
use App\Http\Resources\LeagueFixturesResource;
use App\Http\Resources\WeeklyFixtureResource;
use App\Repositories\FixtureRepository;
use App\Repositories\LeagueRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use function response;

class FixtureController extends Controller {
	public function __construct(
		public LeagueRepository $leagueRepository,
		public FixtureRepository $fixtureRepository,
	){}

	/**
	 * Returns fixtures in current week/round.
	 */
	public function index() : LeagueFixturesResource{
		return new LeagueFixturesResource($this->leagueRepository->getLeague());
	}

    /**
     * Returns all fixtures grouped weekly.
     */
    public function allWeeks(Request $request): JsonResponse{
        $fixturesList = $this->fixtureRepository->getFixturesWeekly();
        $data = [];
        foreach($fixturesList as $fixtures) {
            $data[] = (new WeeklyFixtureResource($fixtures))->toArray($request);
        }
        return response()->json(['data' => $data]);
    }

	/**
	 * Returns fixtures from past week.
	 */
	public function pastWeek() : LeagueFixturesResource{
		$league = $this->leagueRepository->getLeague();
		// manipulate data
		$league->round = $league->round - 1;
		return new LeagueFixturesResource($league);
	}

	/**
	 * Returns fixtures grouped by week/round.
	 */
	public function weekly() : AnonymousResourceCollection{
		return WeeklyFixtureResource::collection($this->fixtureRepository->getFixturesWeekly());
	}

	/**
	 * Generates fixtures and returns.
	 */
	public function generate() : JsonResponse|LeagueFixturesResource{
		try {
			$this->leagueRepository->generateFixtures();
		} catch(NoTeamsFoundException $e) {
			return response()->json([
				'data' => 'There is no teams'
			], 400);
		}
		return new LeagueFixturesResource($this->leagueRepository->getLeague());
	}
}
