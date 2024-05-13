<?php

/*
 * Insider Champions League Trial Project - Made with ❤
 * @author xerenahmed (Eren A. Akyol)
 */

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\TeamResource;
use App\Models\Team;
use App\Repositories\TeamRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TeamController extends Controller {
	public function __construct(
		public TeamRepository $teamRepository,
	){}

	public function index() : AnonymousResourceCollection{
		return TeamResource::collection($this->teamRepository->getTeams());
	}

    public function stats(Team $team): JsonResponse{
        return response()->json($this->teamRepository->getStats($team));
    }
}
