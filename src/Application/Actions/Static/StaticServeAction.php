<?php

namespace App\Application\Actions\Static;

use App\Application\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;

class StaticServeAction extends Action
{
    protected function action(): Response
    {
        $filePath = realpath(APP_ROOT . '/public/' . $this->args['file']);
        if (!file_exists($filePath)) {
            return $this->response->withStatus(404, 'File Not Found');
        }

        $mimeType = match (pathinfo($filePath, PATHINFO_EXTENSION)) {
            'css'   => 'text/css',
            'js'    => 'application/javascript',
            default => 'text/html',
        };

        $this->response->getBody()->write(file_get_contents($filePath));

        return $this->response->withHeader('Content-Type', $mimeType . '; charset=UTF-8');
    }
}
