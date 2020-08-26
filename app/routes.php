<?php
declare(strict_types=1);

// includes
use Farol360\Ancora\Controller\OrderController as OrderController;
// use Farol360\Ancora\Controller\UserController as User;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->map(['get','post'], '/erp_service', OrderController::class . ':erp_service');
$app->map(['get','post'],'/financeiro', OrderController::class . ':financeiro');

