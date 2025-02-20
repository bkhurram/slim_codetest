<?php

namespace App\Application\Actions\Event;

use App\Application\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;

class EventAction extends Action
{
    protected function action(): Response
    {
        $response = $this->response
            ->withBody(new \Slim\Psr7\NonBufferedBody())
            ->withHeader('Content-Type', 'text/event-stream')
            ->withHeader('Cache-Control', 'no-cache')
            ->withHeader('Connection', 'keep-alive');

        $body = $response->getBody();

        // 1 is always true, so repeat the while loop forever (aka event-loop)
        $i = 0;
        while (true) {
            // Send named event
            $now = date('Y-m-d H:i:s');
            $event = sprintf(
                "event: %s\ndata: %s\n\n",
                'message', // event
                json_encode(['time' => $now, 'message' => "Hello from server #$i"]) // data
            );

            // Add a whitespace to the end
            $body->write($event . ' ');

            // break the loop if the client aborted the connection (closed the page)
            if (connection_aborted()) {
                break;
            }

            // sleep for 1 second before running the loop again
            sleep(5);
            $i++;
        }

        return $response;
    }
}
