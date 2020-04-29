<?php
declare(strict_types=1);

namespace Farol360\Ancora\Controller\Admin;

use Farol360\Ancora\Controller;
use Farol360\Ancora\Model;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Flash\Messages as FlashMessages;
use Slim\Views\Twig as View;

use Farol360\Ancora\User;
use Farol360\Ancora\AdminAncora;

class BannersCabecalhoController extends Controller
{
  protected $version;
  protected $adminAncoraModel;
  protected $eventLogAdminAccessModel;
  protected $eventLogAdminAccessTypeModel;
  protected $bannersCabecalhoModel;
  protected $entityFactory;

  public function __construct(
      View $view,
      FlashMessages $flash,
      Model $adminAncoraModel,
      Model $eventLogAdminAccessModel,
      Model $eventLogAdminAccessTypeModel,
      Model $bannersCabecalhoModel,
      $entityFactory,
      $version
  ) {
      parent::__construct($view, $flash);
      $this->adminAncoraModel = $adminAncoraModel;
      $this->eventLogAdminAccessModel = $eventLogAdminAccessModel;
      $this->eventLogAdminAccessTypeModel = $eventLogAdminAccessTypeModel;
      $this->bannersCabecalhoModel = $bannersCabecalhoModel;
      $this->entityFactory = $entityFactory;
      $this->version = $version;
  }

  public function index(Request $request, Response $response, array $args): Response
  {
     // get params
    $params = $request->getQueryParams();
    // pagination params
    if (!empty($params['page'])) {
        $page = intval($params['page']);
    } else {
        $page = 1;
    }
    $limit = 20;
    $offset = ($page - 1) * $limit;
    $banners = $this->bannersCabecalhoModel->getAll($offset, $limit);

    foreach($banners as $banner) {
      $banner->img_featured = ($banner->img_featured !== null) ? json_decode($banner->img_featured, true) : [];
      $banner->img_mobile = ($banner->img_mobile !== null) ? json_decode($banner->img_mobile, true) : [];
    }
    // var_dump($banners); die;
    // pagination controll;
    $amountbanners = $this->bannersCabecalhoModel->getAmount();
    $amountPages = ceil($amountbanners->amount / $limit);
    // var_dump($banners); die;
    return $this->view->render($response, 'admin/banners_cabecalho/index.twig', [
        'banners' => $banners,
        'page' => $page,
        'amountbanners' => $amountbanners,
        'amountPages' => $amountPages
        ]);
  }
  public function edit(Request $request, Response $response, array $args): Response
  {
    // retrive argument id in url, if has it
    $bannerId = intval($args['id']);
    // select in db the post by id
    $banner = $this->bannersCabecalhoModel->get($bannerId);
    $banner->img_featured = ($banner->img_featured !== null) ? json_decode($banner->img_featured, true) : [];
    $banner->img_mobile = ($banner->img_mobile !== null) ? json_decode($banner->img_mobile, true) : [];
    // if post dnt exist, return error
    if (!$banner) {
          $this->flash->addMessage('danger', "banner não encontrado.");
        return $this->httpRedirect($request, $response, '/admin/banners_cabecalho');
    }
    // var_dump($banner); die;
    $languages = $this->languageModel->getAll();
    return $this->view->render($response, 'admin/banners_cabecalho/edit.twig', [
            'banner' => $banner,
            'languages' => $languages
        ]
    );
  }
}
