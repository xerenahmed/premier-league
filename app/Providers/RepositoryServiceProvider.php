<?php

/*
 * Insider Champions League Trial Project - Made with ❤
 * @author xerenahmed (Eren A. Akyol)
 */

declare(strict_types=1);

namespace App\Providers;

use App\Repositories;
use App\Services\Interfaces;
use Illuminate\Support\ServiceProvider;
use function app;

class RepositoryServiceProvider extends ServiceProvider
{
	/**
	 * Register services.
	 */
	public function register() : void
	{
		app()->bind(Interfaces\TeamRepository::class, Repositories\TeamRepository::class);
		app()->bind(Interfaces\FixtureRepository::class, Repositories\FixtureRepository::class);
		app()->bind(Interfaces\FixtureStatsRepository::class, Repositories\FixtureStatsRepository::class);
		app()->bind(Interfaces\LeagueRepository::class, Repositories\LeagueRepository::class);
	}

	/**
	 * Bootstrap services.
	 */
	public function boot() : void
	{
		//
	}
}
