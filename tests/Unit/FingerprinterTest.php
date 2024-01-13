<?php

namespace MasudZaman\Fingerprints\Tests\Unit;

use Illuminate\Routing\Route;
use MasudZaman\Fingerprints\Tests\TestCase;

class FingerprinterTest extends TestCase
{
    public function test_is_a_pure_function_when_cookie()
    {
        $request = $this->makeRequest('GET', '/test', [], [config('fingerprints.cookie_name') => 'testing']);

        $fingerprint1 = $request->fingerprint();
        $fingerprint2 = $request->fingerprint();

        $this->assertNotEmpty($fingerprint1);
        $this->assertNotEmpty($fingerprint2);
        $this->assertEquals($fingerprint1, $fingerprint2);
    }

    public function test_is_a_pure_function_when_no_cookie()
    {
        $request = $this->makeRequest('GET', '/test');
        $request->setRouteResolver(function () {
            return new Route(['GET'], '/test', ['test1', 'test2']);
        });

        $fingerprint1 = $request->fingerprint();
        $fingerprint2 = $request->fingerprint();

        $this->assertNotEmpty($fingerprint1);
        $this->assertNotEmpty($fingerprint2);
        $this->assertEquals($fingerprint1, $fingerprint2);
    }
}
