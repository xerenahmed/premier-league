<?php

/*
 * Insider Champions League Trial Project - Made with ❤
 * @author xerenahmed (Eren A. Akyol)
 */

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class NoTeamsFoundException extends Exception
{
	public function __construct(string $message = "No teams found", int $code = 0, ?Throwable $previous = null){
		parent::__construct($message, $code, $previous);
	}
}
