<?php

namespace App\Application\Controller;

use App\Application\Actions\ActionPayload;
use App\Application\Services\ValidatorService;
use Illuminate\Database\Capsule\Manager as Capsule;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;

abstract class BaseController
{
	protected LoggerInterface $logger;
	protected ValidatorService $validator;
	protected Capsule $capsule;

	public function __construct(LoggerInterface $logger, ValidatorService $validator, Capsule $capsule)
	{
		$this->logger = $logger;
		$this->validator = $validator;
		$this->capsule = $capsule;
	}

	protected function getFormData(Request $request)
	{
		return $request->getParsedBody();
	}

	protected function respondWithData(ResponseInterface $response, $data = null, int $statusCode = 200): Response
	{
		$payload = new ActionPayload($statusCode, $data);
		return $this->respond($response, $payload);
	}

	protected function respond(ResponseInterface $response, ActionPayload $payload): Response
	{
		$json = json_encode($payload->getError() ?? $payload->getData(), JSON_PRETTY_PRINT);
		$response->getBody()->write($json);

		return $response
					->withHeader('Content-Type', 'application/json')
					->withStatus($payload->getStatusCode());
	}
}
