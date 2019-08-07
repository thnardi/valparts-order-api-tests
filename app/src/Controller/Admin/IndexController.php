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

class IndexController extends Controller
{
  protected $version;
  protected $adminAncoraModel;
  protected $eventLogAdminAccessModel;
  protected $eventLogAdminAccessTypeModel;
  protected $entityFactory;

  public function __construct(
      View $view,
      FlashMessages $flash,
      Model $adminAncoraModel,
      Model $eventLogAdminAccessModel,
      Model $eventLogAdminAccessTypeModel,
      $entityFactory,
      $version
  ) {
      parent::__construct($view, $flash);
      $this->adminAncoraModel = $adminAncoraModel;
      $this->eventLogAdminAccessModel = $eventLogAdminAccessModel;
      $this->eventLogAdminAccessTypeModel = $eventLogAdminAccessTypeModel;
      $this->entityFactory = $entityFactory;
      $this->version = $version;
  }

  public function index(Request $request, Response $response): Response
  {
      return $this->view->render($response, 'admin/dashboard/index.twig');
  }

  public function about(Request $request, Response $response): Response
  {
      return $this->view->render($response, 'admin/dashboard/about.twig', ['version' => $this->version]);
  }
  public function login(Request $request, Response $response): Response
  {
    if (empty($request->getParsedBody())) {
      return $this->view->render($response, 'admin/login.twig');
    } else {
      $session_captcha_result = $_SESSION['captcha_number_result'];
      $request_captcha = $request->getParsedBody()['captcha-result'];
      if ($session_captcha_result == $request_captcha ) {
        $slug = strtolower($request->getParsedBody()['slug']);
        $password = $request->getParsedBody()['password'];
        $data['slug'] = $slug;
        $data['password'] = $password;
        $adminAncora = $this->entityFactory->createAdminAncora($data);
        if (!AdminAncora::loginSlug($slug, $password)) {
            $this->flash->addMessage('errorLogin', 'Usuário ou senha errados!');
            return $this->httpRedirect($request, $response, '/admin/login');
        } else {
          $adminAncora = $this->adminAncoraModel->get();
          $adminAncora = $this->adminAncoraModel->get((int)$adminAncora->id);
          if (isset($_SESSION['return'])) {
              //$return = $_SESSION['return'];
              unset($_SESSION['return']);
              $return = '/admin';
          } else {
              $return = '/admin';
          }
          //log register for login in here
          $eventLog['id_admin_ancora'] = (int)$adminAncora->id;
          $eventLog['description'] = 'login administrativo.';
          $eventLog = $this->entityFactory->createEventLogAdminAccess($eventLog);
          $eventLog->setType((int)$this->eventLogAdminAccessTypeModel->getBySlug('login_admin_ancora')->data->id);
          $this->eventLogAdminAccessModel->add($eventLog);
          return $this->httpRedirect($request, $response, $return);
        }
      } else {
        $this->flash->addMessage('warning', 'Digite a soma corretamente!');
        return $this->httpRedirect($request, $response, '/admin/login');
      }
    }
  }

  public function logout(Request $request, Response $response): Response
  {
    $adminAncora = $this->adminAncoraModel->get();
    $eventLog['id_admin_ancora'] = (int)$adminAncora->id;
    $eventLog['description'] = 'logout administrativo.';
    $eventLog = $this->entityFactory->createEventLogAdminAccess($eventLog);
    $eventLog->setType((int)$this->eventLogAdminAccessTypeModel->getBySlug('logout_admin_ancora')->data->id);
    $this->eventLogAdminAccessModel->add($eventLog);
    if (isset($_SESSION['admin_ancora'])) {
        AdminAncora::setSessionId(null);
    }
    AdminAncora::logout();
    //log register for login in here
    return $this->view->render($response, 'user/login/logout_adminancora.twig');
  }
}
