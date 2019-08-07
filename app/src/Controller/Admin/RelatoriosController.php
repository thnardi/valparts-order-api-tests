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
  protected $relatoriosModel;
  protected $eventLogAdminAccessModel;
  protected $eventLogAdminAccessTypeModel;
  protected $eventLogAdminActionModel;
  protected $eventLogAdminActionTypeModel;
  protected $eventLogUserAccessModel;
  protected $eventLogUserAccessTypeModel;
  protected $eventLogUserActionModel;
  protected $eventLogUserActionTypeModel;

  public function __construct(
      View $view,
      FlashMessages $flash,
      Model $relatoriosModel,
      Model $eventLogAdminAccessModel,
      Model $eventLogAdminAccessTypeModel,
      Model $eventLogAdminActionModel,
      Model $eventLogAdminActionTypeModel,
      Model $eventLogUserAccessModel,
      Model $eventLogUserAccessTypeModel,
      Model $eventLogUserActionModel,
      Model $eventLogUserActionTypeModel,
      EntityFactory $entityFactory
  ) {
      parent::__construct($view, $flash);
      $this->relatoriosModel = $relatoriosModel;
      $this->eventLogAdminAccessModel = $eventLogAdminAccessModel;
      $this->eventLogAdminAccessTypeModel = $eventLogAdminAccessTypeModel;
      $this->eventLogAdminActionModel = $eventLogAdminActionModel;
      $this->eventLogAdminActionTypeModel = $eventLogAdminActionTypeModel;
      $this->eventLogUserAccessModel = $eventLogUserAccessModel;
      $this->eventLogUserAccessTypeModel = $eventLogUserAccessTypeModel;
      $this->eventLogUserActionModel = $eventLogUserActionModel;
      $this->eventLogUserActionTypeModel = $eventLogUserActionTypeModel;
      $this->entityFactory = $entityFactory;
  }

  public function index(Request $request, Response $response): Response
  {
    $params = $request->getQueryParams();
    // escolha do relatório que será mostrado
    $id_relatorio = isset($params['relatorio']) ? (int)$params['relatorio'] : 1;
    // escolha da página
    $page = isset($params['page']) ? (int)$params['page'] : 1;
    // escolha da orgenação
    $order = isset($params['order']) ? (int)$params['order'] : 1;
    // escolha do filtro
    $filtro = isset($params['filtro']) ? (int)$params['filtro'] : 1;
    $limit = 10;
    $offset = ($page - 1) * $limit;
    $relatorio = $this->relatoriosModel->get($id_relatorio);
    if ($relatorio->data == false) {
      $lista['data'] = false;
      $lista['tipo'] = false;
      $lista['quantidade'] = 0;
    }
    if ($relatorio->data->slug == 'accesso_admin') {
      $lista['quantidade'] = (int)$this->eventLogAdminAccessModel->getAmount($filtro)->amount;
      $lista['data'] = $this->eventLogAdminAccessModel->getAll($order, $filtro, $offset, $limit)->data;
      $lista['tipo'] = 'accesso_admin';
    }
    $relatorios = $this->relatoriosModel->getAll();
    $amountPages = ceil($lista['quantidade'] / $limit);

    // var_dump($relatorio);
    // var_dump($lista);
    // die;
    return $this->view->render($response, 'admin/relatorios/index/index.twig', [
      'relatorios' => $relatorios->data,
      'lista' => $lista,
      'params' => $params,
      'page' => $page,
      'amountPages' => $amountPages,
    ]);
  }
}
