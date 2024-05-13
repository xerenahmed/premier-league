<?php

/*
 * Insider Champions League Trial Project - Made with ❤
 * @author xerenahmed (Eren A. Akyol)
 */

declare(strict_types=1);

use App\Http\Controllers\FixtureController;
use App\Http\Controllers\FixtureStatsController;
use App\Http\Controllers\LeagueController;
use App\Http\Controllers\TeamController;
use Illuminate\Support\Facades\Route;

Route::get('/league', [LeagueController::class, 'index'])->name('league.index');
Route::get('/league/table', [LeagueController::class, 'table'])->name('league.table');
Route::post('/league/start', [LeagueController::class, 'start'])->name('league.start');
Route::post('/league/reset', [LeagueController::class, 'reset'])->name('league.reset');
Route::get('/league/predictions', [LeagueController::class, 'predictions'])->name('league.predictions');
Route::post('/league/play-next-week', [LeagueController::class, 'playNextWeek'])->name('league.play-next-week');
Route::post('/league/play-all-weeks', [LeagueController::class, 'playAllWeeks'])->name('league.play-all-weeks');

Route::get('/team', [TeamController::class, 'index'])->name('team.index');
Route::get('/team/{team}/stats', [TeamController::class, 'stats'])->name('team.stats');
Route::get('/fixtures', [FixtureController::class, 'index'])->name('fixtures.index');
Route::get('/fixtures/all-weeks', [FixtureController::class, 'allWeeks'])->name('fixtures.allWeeks');
Route::get('/fixtures/past-week', [FixtureController::class, 'pastWeek'])->name('fixtures.pastWeek');
Route::get('/fixtures/weekly', [FixtureController::class, 'weekly'])->name('fixtures.weekly');
Route::post('/fixtures/generate', [FixtureController::class, 'generate'])->name('fixtures.generate');
Route::put('/fixtures/stats/{fixtureStat}', [FixtureStatsController::class, 'update'])->name('fixtures.stats.update');
