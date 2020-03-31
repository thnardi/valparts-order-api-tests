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
    protected $ambiente_windows;


    public function __construct(View $view, FlashMessages $flash, Mailer $mailer, Model $postModel, Model $postTypeModel, EntityFactory $entityFactory, $ambiente_windows)
    {
        parent::__construct($view, $flash);
        $this->mailer           = $mailer;
        $this->postModel        = $postModel;
        $this->postTypeModel    = $postTypeModel;
        $this->entityFactory    = $entityFactory;
        $this->ambiente_windows    = $ambiente_windows;
    }


    public function index(Request $request, Response $response): Response
    {

        return $this->view->render($response, 'page/index.twig');
    }

    public function manutencao(Request $request, Response $response): Response
    {
      return $this->view->render($response, 'page/manutencao.twig',[
      ]);
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

    public function create_captcha(Request $request, Response $response): Response
    {
        $my_img = imagecreate( 300, 30 );
        $background = imagecolorallocate( $my_img, 255, 255, 255 );
        $text_colour = imagecolorallocate( $my_img, 0, 0, 0 );
        $line_colour = imagecolorallocate( $my_img, 128, 255, 0 );
        $line_colour2 = imagecolorallocate( $my_img, 255, 128, 0 );
        $_SESSION['captcha_number_1'] = rand(0,9);
        $_SESSION['captcha_number_2'] = rand(0,9);
        $_SESSION['field_id'] = rand(0,9);
        $_SESSION['captcha_number_result'] = $_SESSION['captcha_number_1'] + $_SESSION['captcha_number_2'];
        $text = $_SESSION['captcha_number_1'] . " + " .  $_SESSION['captcha_number_2'];
        //imagettftext($my_img, 16, 0, 5, 28, $text_colour, realpath('default-img/Arial.ttf'),
        //$text);
       if ($this->ambiente_windows == "0") {
          imagettftext($my_img, 16, 0, 5, 28, $text_colour, "/default-img/Arial.ttf",
          $text);
        }
        if ($this->ambiente_windows == "1") {
          imagettftext($my_img, 16, 0, 5, 28, $text_colour, realpath("default-img/Arial.ttf"),
          $text);
        }
        // var_dump($my_img);die;
        //imagestring( $my_img, 5, 5, 5, $text, $text_colour );
        //imageline( $my_img, 2, 2, 30, 25, $line_colour );
        //imageline( $my_img, 2, 25, 30, 2, $line_colour2 );
        header( "Content-type: image/png" );
        imagepng( $my_img );
        imagecolordeallocate($my_img, $line_colour );
        imagecolordeallocate($my_img, $text_colour );
        imagecolordeallocate($my_img, $background );
        imagedestroy( $my_img );
        die;
        return $response;
    }

}
