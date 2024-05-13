<?php

/*
 * Insider Champions League Trial Project - Made with ❤
 * @author xerenahmed (Eren A. Akyol)
 */

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class League extends Model
{
	use HasFactory;

	public $timestamps = false;

	protected $fillable = [
		'name',
		'round',
		'started'
	];

	protected $casts = [
		'started' => 'bool'
	];
}
