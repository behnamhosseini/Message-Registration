<?php

namespace Tests\Unit;

use App\Http\Middleware\ValidateUserToken;
use App\Services\TokenService;
use Illuminate\Http\Request;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

class CheckTokenValidityTest extends TestCase
{

    public function testItPassesValidTokenThrough()
    {
        $mockService = $this->createMock(TokenService::class);
        $mockService->expects($this->once())
            ->method('validateToken')
            ->with($this->equalTo('valid-token'))
            ->willReturn(['user_id' => 1, 'status' => null]);

        $middleware = new ValidateUserToken($mockService);

        $request = Request::create('/test', 'get');
        $request->headers->set('Authorization', 'Bearer valid-token');

        $response = $middleware->handle($request, function ($req) {
            return new Response();
        });
        $this->assertEquals(200, $response->getStatusCode());
    }

}
