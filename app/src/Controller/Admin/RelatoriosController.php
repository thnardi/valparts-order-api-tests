<?php
declare(strict_types=1);

namespace Farol360\Ancora\Controller\Admin;

use Farol360\Ancora\Controller;
use Farol360\Ancora\Model;
use Farol360\Ancora\Model\EntityFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Flash\Messages as FlashMessages;
use Slim\Views\Twig as View;

class RelatoriosController extends Controller
{
    protected $entityFactory;

    public function __construct(
        View $view,
        FlashMessages $flash,
        EntityFactory $entityFactory
    ) {
        parent::__construct($view, $flash);
        $this->entityFactory = $entityFactory;
    }

    public function index(Request $request, Response $response): Response
    {
        return $this->view->render($response, 'admin/relatorios/index.twig', [

        ]);
    }
}
