<?php

/*
 * Insider Champions League Trial Project - Made with ❤
 * @author xerenahmed (Eren A. Akyol)
 */

declare(strict_types=1);

describe('UI Test', function() {

	it('should open page', function() {
		$this->get('/')->assertStatus(200);
	});
});
