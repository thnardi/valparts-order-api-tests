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
    // pagination controll;
    $amountbanners = $this->bannersCabecalhoModel->getAmount();
    $amountPages = ceil($amountbanners->amount / $limit);
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

    // if post dnt exist, return error
    if (!$banner) {
      $this->flash->addMessage('danger', "não encontrado.");
      return $this->httpRedirect($request, $response, '/admin/banners_cabecalho');
    }

    return $this->view->render($response, 'admin/banners_cabecalho/edit.twig', [
        'banner' => $banner
      ]
    );
  }
  public function update(Request $request, Response $response): Response
  {
    // get data from body request
    $data = $request->getParsedBody();

    try {
    $this->bannersCabecalhoModel->beginTransaction();
    $banner = $this->entityFactory->createBannersCabecalho($data);
    $oldBanner = $this->bannersCabecalhoModel->get((int) $banner->id);
    $files = $request->getUploadedFiles();
    // if files are empty means size == 0
    //var_dump($banner);die;
      if ($files['img_featured']->getSize() != 0) {
          $image = $files['img_featured'];
          if ($image->getError() === UPLOAD_ERR_OK) {
              //verify allowed extensions
              $filename = $image->getClientFilename();
              $allowedExtensions = [
                  'jpg',
                  'jpeg',
                  'gif',
                  'png'
              ];
              // if not allowed extension
              if (!in_array(pathinfo($filename,PATHINFO_EXTENSION), $allowedExtensions)) {
                //inform error msg
                $this->bannersCabecalhoModel->rollback();
                $this->flash->addMessage('danger', "Imagem em formato inválido.");
                //redirect to this url
                return $this->httpRedirect($request, $response, '/admin/banners_cabecalho');
              }
              //verify size
              if ($image->getSize() > 900000) {
                //inform error msg
                $this->bannersCabecalhoModel->rollback();
                $this->flash->addMessage('danger', "Imagem muito grande (max 500kb).");
                //redirect to this url
                return $this->httpRedirect($request, $response, '/admin/banners_cabecalho');
              }
              $filename = sprintf(
                  '%s.%s',
                  uniqid(),
                  pathinfo($image->getClientFilename(), PATHINFO_EXTENSION)
              );
              $path = 'upload/img/';
              $image->moveTo($path . $filename);
              $banner->img_featured = $path . $filename;
          }
          // remove old img from disk
          if (file_exists($request->getParsedBody()['img_featured_old'])) {
              unlink($request->getParsedBody()['img_featured_old']);
          }
      // if has no image, set old as atual
      } else {
        $banner->img_featured = $oldBanner->img_featured;
      }
      // mobile image
      if ($files['img_mobile']->getSize() != 0) {
          $image = $files['img_mobile'];
          if ($image->getError() === UPLOAD_ERR_OK) {
              //verify allowed extensions
              $filename = $image->getClientFilename();
              $allowedExtensions = [
                  'jpg',
                  'jpeg',
                  'gif',
                  'png'
              ];
              // if not allowed extension
              if (!in_array(pathinfo($filename,PATHINFO_EXTENSION), $allowedExtensions)) {
                //inform error msg
                $this->bannersCabecalhoModel->rollback();
                $this->flash->addMessage('danger', "Imagem em formato inválido.");
                //redirect to this url
                return $this->httpRedirect($request, $response, '/admin/banners_cabecalho');
              }
              //verify size
              if ($image->getSize() > 900000) {
                //inform error msg
                $this->bannersCabecalhoModel->rollback();
                $this->flash->addMessage('danger', "Imagem muito grande (max 500kb).");
                //redirect to this url
                return $this->httpRedirect($request, $response, '/admin/banners_cabecalho');
              }
              $filename = sprintf(
                  '%s.%s',
                  uniqid(),
                  pathinfo($image->getClientFilename(), PATHINFO_EXTENSION)
              );
              $path = 'upload/img/';
              $image->moveTo($path . $filename);
              $banner->img_mobile = $path . $filename;
          }
          // remove old img from disk
          if (file_exists($request->getParsedBody()['img_mobile_old'])) {
              unlink($request->getParsedBody()['img_mobile_old']);
          }
      // if has no image, set old as atual
      } else {
        $banner->img_mobile = $oldBanner->img_mobile;
      }
      //var_dump($banner);die;
      $this->bannersCabecalhoModel->update($banner);
      $this->bannersCabecalhoModel->commit();
      $this->flash->addMessage('success', " atualizado com sucesso.");
      return $this->httpRedirect($request, $response, '/admin/banners_cabecalho');
    } catch (ModelException $e) {
    // if get this point, something unforeseen happened
    $this->bannersCabecalhoModel->rollback();
    $this->flash->addMessage('danger', "Erro indefinido ao adicionar. Por favor entre em contato com a Farol 360.");
    return $this->httpRedirect($request, $response, '/admin/banners_cabecalho');
    }
  }
}
