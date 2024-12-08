<?php

namespace Tests;

use DI\ContainerBuilder;
use Exception;
use PHPUnit\Framework\TestCase as PHPUnit_TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Psr7\Headers;
use Slim\Psr7\Request as SlimRequest;
use Slim\Psr7\Uri;


class TestCase extends PHPUnit_TestCase
{
	use ProphecyTrait;

	/**
	 * @return App
	 * @throws Exception
	 */
	protected function getAppInstance(): App
	{
		// Instantiate PHP-DI ContainerBuilder
		$containerBuilder = new ContainerBuilder();

		// Container intentionally not compiled for tests.

		// Set up settings
		$settings = require __DIR__ . '/../app/settings.php';
		$settings($containerBuilder);

		// Set up dependencies
		$dependencies = require __DIR__ . '/../app/dependencies.php';
		$dependencies($containerBuilder);

		// Build PHP-DI Container instance
		$container = $containerBuilder->build();

		// Instantiate the app
		AppFactory::setContainer($container);
		$app = AppFactory::create();

		// Register eloquent database
		$eloquent = require __DIR__ . '/../app/eloquent.php';
		$eloquent($container);


		// Register middleware
		$middleware = require __DIR__ . '/../app/middleware.php';
		$middleware($app);

		// Register routes
		$routes = require __DIR__ . '/../app/routes.php';
		$routes($app);

		return $app;
	}

	/**
	 * @param string $method
	 * @param string $path
	 * @param array  $headers
	 * @param array  $cookies
	 * @param array  $serverParams
	 * @return Request
	 */
	protected function createRequest(
		string $method,
		string $path,
		array $headers = [],
		array $data = [],
		array $cookies = [],
		array $serverParams = [],
	): Request {
		$uri = new Uri('', '', 80, $path);

		$jsonBody = json_encode($data);

		// Create a temporary stream
		$handle = fopen('php://temp', 'w+');
		rewind($handle);

		// Create a PSR-7 stream from the temporary resource
		$streamFactory = new StreamFactory();
		$stream = $streamFactory->createStreamFromResource($handle);

		$h = new Headers();
		foreach ($headers as $name => $value) {
			$h->addHeader($name, $value);
		}


		$request =  new SlimRequest($method, $uri, $h, $cookies, $serverParams, $stream);
		return $request;
	}

	protected function createPostJsonRequest(
		string $uri,
		array $data = null
	) {
		$request = $this->createRequest("POST", $uri);

		if ($data !== null) {
			$request = $request->withParsedBody($data);
		}

		return $request->withHeader('Content-Type', 'application/json');
	}
}
