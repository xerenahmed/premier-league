<?php

/*
 * Insider Champions League Trial Project - Made with ❤
 * @author xerenahmed (Eren A. Akyol)
 */

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\Models\Fixture;
use Illuminate\Database\Eloquent\Collection;

interface FixtureRepository {
	public function getFixtures() : Collection;
	public function getFixture($id) : Fixture;
	public function hasAnyFixture() : bool;
	public function getFixturesByRound($round) : Collection;
	public function getFixturesLeft() : Collection;
	public function getLastFixtureRound() : int;
	public function getFixturesWeekly() : \Illuminate\Support\Collection;
	public function createFixture(array $data) : Fixture;
	public function updateFixture($id, array $data) : Fixture;
	public function deleteFixture(Fixture $fixture) : bool;
}
