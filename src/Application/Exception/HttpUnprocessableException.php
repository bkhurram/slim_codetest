<?php
namespace App\Application\Exception;

use Slim\Exception\HttpSpecializedException;

class HttpUnprocessableException extends HttpSpecializedException
{
	/**
	* @var int
	*/
	protected $code = 422;

	/**
	* @var string
	*/
	protected $message = 'Unprocessable Content';

	protected string $title = '422 Unprocessable Content';
	protected string $description = 'The request requires valid payload data';
}
