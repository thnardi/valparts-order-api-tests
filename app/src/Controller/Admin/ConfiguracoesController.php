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

class ConfiguracoesController extends Controller
{
  protected $version;
  protected $adminAncoraModel;
  protected $eventLogAdminAccessModel;
  protected $eventLogAdminAccessTypeModel;
  protected $configuracoesModel;
  protected $entityFactory;

  public function __construct(
      View $view,
      FlashMessages $flash,
      Model $adminAncoraModel,
      Model $eventLogAdminAccessModel,
      Model $eventLogAdminAccessTypeModel,
      Model $configuracoesModel,
      $entityFactory,
      $version
  ) {
      parent::__construct($view, $flash);
      $this->adminAncoraModel = $adminAncoraModel;
      $this->eventLogAdminAccessModel = $eventLogAdminAccessModel;
      $this->eventLogAdminAccessTypeModel = $eventLogAdminAccessTypeModel;
      $this->configuracoesModel = $configuracoesModel;
      $this->entityFactory = $entityFactory;
      $this->version = $version;
  }

  public function index(Request $request, Response $response, array $args): Response
  {

    $configId = 1;

    $configuracao = $this->configuracoesModel->get($configId);
    //var_dump($configuracao->value);die;
    return $this->view->render($response, 'admin/configuracoes/index.twig', [
        'configuracao' => $configuracao
    ]);
  }
  public function update(Request $request, Response $response): Response
  {
    $data = $request->getParsedBody();
    $data['id'] = 1;
    $data['value'] = (int)$data['value'];
    //var_dump($data);die;
      $config = $this->entityFactory->createConfiguracoes($data);

      $this->configuracoesModel->update($config);

      $this->flash->addMessage('success', "atualizado com sucesso.");
      return $this->httpRedirect($request, $response, '/admin/configuracoes');
  }
}
