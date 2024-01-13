<?php

namespace MasudZaman\Trails\Tests\Unit;

use Illuminate\Routing\Route;
use MasudZaman\Trails\Tests\TestCase;

class TrailerTest extends TestCase
{
    public function test_is_a_pure_function_when_cookie()
    {
        $request = $this->makeRequest('GET', '/test', [], [config('trails.cookie_name') => 'testing']);

        $trail1 = $request->trail();
        $trail2 = $request->trail();

        $this->assertNotEmpty($trail1);
        $this->assertNotEmpty($trail2);
        $this->assertEquals($trail1, $trail2);
    }

    public function test_is_a_pure_function_when_no_cookie()
    {
        $request = $this->makeRequest('GET', '/test');
        $request->setRouteResolver(function () {
            return new Route(['GET'], '/test', ['test1', 'test2']);
        });

        $trail1 = $request->trail();
        $trail2 = $request->trail();

        $this->assertNotEmpty($trail1);
        $this->assertNotEmpty($trail2);
        $this->assertEquals($trail1, $trail2);
    }
}
