<?php
declare(strict_types=1);

namespace Farol360\Ancora;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Flash\Messages as FlashMessages;

abstract class Controller
{
    
    protected function httpRedirect(
        Request $request,
        Response $response,
        string $path = '',
        int $status = 302
    ): Response {
        return $response->withStatus($status)->withHeader(
            'Location',
            $request->getUri()->getBaseUrl() . $path
        );
    }
}
