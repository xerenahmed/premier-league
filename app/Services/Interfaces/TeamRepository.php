<?php

/*
 * Insider Champions League Trial Project - Made with ❤
 * @author xerenahmed (Eren A. Akyol)
 */

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;

interface TeamRepository {
	public function getTeams() : Collection;
	public function getTeamsByRank(string $order = 'DESC') : Collection;
	public function getTeam($id) : Team;
	public function createTeam(array $data) : Team;
	public function updateTeam($id, array $data) : Team;
	public function deleteTeam(int $id) : bool;
	public function getStats(Team $team) : array;
}
