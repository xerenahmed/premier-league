<?php

/*
 * Insider Champions League Trial Project - Made with ❤
 * @author xerenahmed (Eren A. Akyol)
 */

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\LeagueResource;
use App\Http\Resources\LeagueTableResource;
use App\Http\Resources\PredictionResource;
use App\Repositories\FixtureRepository;
use App\Repositories\LeagueRepository;
use App\Repositories\TeamRepository;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use function response;

class LeagueController extends Controller {
	public function __construct(
		public LeagueRepository $leagueRepository,
		public TeamRepository $teamRepository,
		public FixtureRepository $fixtureRepository,
	){}

	public function index() : LeagueResource{
		return new LeagueResource($this->leagueRepository->getLeague());
	}

	public function table() : LeagueTableResource{
		$teams = $this->teamRepository->getTeamsByRank();
		return new LeagueTableResource($this->leagueRepository->getLeague(), $teams);
	}

	public function start() : JsonResponse{
		if ($this->leagueRepository->isLeagueStarted()) {
			return response()->json('already started', 304);
		}

		if (!$this->fixtureRepository->hasAnyFixture()) {
			return response()->json('no fixtures', 404);
		}

		$this->leagueRepository->startLeague();
		// Play first week to see results on start
		$this->leagueRepository->playNextWeek();
		return response()->json(true);
	}

	public function reset() : JsonResponse{
		$this->leagueRepository->resetLeague();
		return response()->json(true);
	}

	public function playNextWeek() : LeagueResource{
		$this->leagueRepository->playNextWeek();
		return new LeagueResource($this->leagueRepository->getLeague());
	}

	public function playAllWeeks() : LeagueResource{
		$this->leagueRepository->playAllWeeks();
		return new LeagueResource($this->leagueRepository->getLeague());
	}

	public function predictions() : JsonResponse|AnonymousResourceCollection{
        try {
            $predictions = $this->leagueRepository->getPredictions();
        } catch(QueryException $e) {
            return response()->json([
                'data' => 'too many request'
            ], 429);
        }
		if ($predictions === null) {
			return response()->json([
                'data' => 'predictions not ready yet'
            ], 400);
		}
		return PredictionResource::collection($predictions->map(fn($data) => (object) $data));
	}
}
