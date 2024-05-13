<?php

/*
 * Insider Champions League Trial Project - Made with ❤
 * @author xerenahmed (Eren A. Akyol)
 */

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
	return view('app');
});
