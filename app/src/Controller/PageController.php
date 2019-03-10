<?php
declare(strict_types=1);

namespace Farol360\Ancora\Controller;

use Farol360\Ancora\Controller;
use Farol360\Ancora\Mailer;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Flash\Messages as FlashMessages;
use Slim\Views\Twig as View;

use Farol360\Ancora\Model;
use Farol360\Ancora\Model\EntityFactory;

class PageController extends Controller
{
    protected $mailer;


    public function __construct(View $view, FlashMessages $flash, Mailer $mailer, Model $postModel, Model $postTypeModel, EntityFactory $entityFactory)
    {
        parent::__construct($view, $flash);
        $this->mailer           = $mailer;
        $this->postModel        = $postModel;
        $this->postTypeModel    = $postTypeModel;
        $this->entityFactory    = $entityFactory;
    }


    public function index(Request $request, Response $response): Response
    {

        return $this->view->render($response, 'page/index.twig');
    }

    public function post(Request $request, Response $response, array $args): Response
    {

        $post = $this->postModel->get(intval($args['id']));



        return $this->view->render($response, 'page/post.twig', ['post' => $post]);
    }

    public function posts(Request $request, Response $response): Response
    {

        $posts = $this->postModel->getAll();
        return $this->view->render($response, 'page/post_list.twig', ['posts' => $posts]);
    }

}
