<?php

namespace App\Application\Middleware;

use App\Application\Models\User;
use App\Application\Services\JwtService;
use Illuminate\Support\Carbon;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Exception\HttpUnauthorizedException;

class AuthMiddleware implements Middleware
{
    /**
     * @inheritdoc
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        $authHeader = $request->getHeaderLine('Authorization');

        if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode(['error' => 'Token not provided or invalid']));

            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        $token = $matches[1];

        try {
            $jwtService = new JwtService($_ENV['JWT_SECRET']);
            if (!$jwtService->validateToken($token)) {
                throw new \Exception('Invalid Token');
            }
            $decoded = $jwtService->decodeToken($token);

            // $tokenExpire = Carbon::parse($decoded['exp'])->toDateTime();

            // check token expire
            if (time() > $decoded['exp']) {
                throw new HttpUnauthorizedException($request, 'Token expired');
            }

//			 $request = $request->withAttribute('userId', $decoded['sub']);

            $user = User::findOrFail($decoded['sub']);
            $request = $request->withAttribute('user', $user);
        } catch (\Exception $e) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode(['error' => 'Token invalid or expired']));

            return $response->withHeader('Content-Type', 'application/json')
                            ->withStatus(401);
        }

        return $handler->handle($request);
    }
}
