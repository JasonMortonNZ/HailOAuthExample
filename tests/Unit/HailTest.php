<?php

namespace Tests\Unit;

use App\Hail;

use Mockery;
use App\HailOauth;
use Tests\TestCase;
use Mockery\MockInterface;

class HailTest extends TestCase
{

    public function test_is_authorised_returns_true_when_token_is_stored()
    {
        cache()->put('hail_token', 'abcde12345', 5);
        $hail = new Hail;
        $this->assertTrue($hail->isAuthorised());
    }

    public function test_is_authorised_returns_false_when_no_token_is_stored()
    {
        $hail = new Hail;
        $this->assertFalse($hail->isAuthorised());
    }
}
