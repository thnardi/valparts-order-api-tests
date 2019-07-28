<?php
declare(strict_types=1);
namespace Farol360\Ancora\Controller\Admin;
use Farol360\Ancora\Model\ModelException;
use Farol360\Ancora\CustomLogger;
use Farol360\Ancora\Controller;
use Farol360\Ancora\Model;
use Farol360\Ancora\Model\EntityFactory;
use Farol360\Ancora\User;
use Fusonic\SpreadsheetExport\Spreadsheet;
use Fusonic\SpreadsheetExport\ColumnTypes\DateColumn;
use Fusonic\SpreadsheetExport\ColumnTypes\NumericColumn;
use Fusonic\SpreadsheetExport\ColumnTypes\TextColumn;
use Fusonic\SpreadsheetExport\Writers\OdsWriter;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Flash\Messages as FlashMessages;
use Slim\Views\Twig as View;
class UserController extends Controller
{
    protected $entityFactory;
    protected $roleModel;
    protected $userModel;
    protected $adminAncoraModel;
    public function __construct(
        View $view,
        FlashMessages $flash,
        Model $user,
        Model $role,
        Model $adminAncoraModel,
        EntityFactory $entityFactory
    ) {
        parent::__construct($view, $flash);
        $this->userModel = $user;
        $this->roleModel = $role;
        $this->adminAncoraModel = $adminAncoraModel;
        $this->entityFactory = $entityFactory;
    }
    public function index(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();
        if (!empty($params['page'])) {
            $page = intval($params['page']);
        } else {
            $page = 1;
        }
        if (!empty($params['order'])) {
          $order = (int)$params['order'];
        } else {
          $order = 1;
        }
        if (!empty($params['filtro'])) {
          $filtro = (int)$params['filtro'];
        } else {
          $filtro = 1;
        }
        $limit = 20;
        $offset = ($page - 1) * $limit;
        $amountAdmin = $this->adminAncoraModel->getAmount();
        $amountPages = ceil($amountAdmin->amount / $limit);

        $pageTitle = 'Administradores';
        $users = $this->adminAncoraModel->getAllByTypePermission($order, $filtro, $offset, $limit);
        $admin_ancora = $_SESSION['admin_ancora'];
        foreach($users as $user) {
            $new_data = explode(" ", $user->created_at);
            $data_separado = explode("-", $new_data[0]);
            $user->created_at = "$data_separado[2]/$data_separado[1]/$data_separado[0] $new_data[1]";
      }//var_dump($admin_ancora);die;
      return $this->view->render($response, 'admin/user/index.twig', [
        'admin_ancora' => $admin_ancora,
        'users' => $users,
        'page_title' => $pageTitle,
        'page' => $page,
        'amountPages' => $amountPages,
        'order' => $order,
        'filtro' => $filtro
      ]);
    }
    public function add(Request $request, Response $response): Response
    {
      $permissao_type_user = ($_SESSION['admin_ancora']['type'] > 1 ) ? true : false;
      //$return_all_users = $this->adminAncoraModel->getAllByTypePermission();//var_dump($return_all_users);die;
        if ($permissao_type_user) {
            if (empty($request->getParsedBody())) {
            $admin_ancora = $_SESSION['admin_ancora'];
            return $this->view->render($response, 'admin/user/add.twig', [
              'admin_ancora' => $admin_ancora,
          ]);
        }
        $body = $request->getParsedBody();
        $body['type'] = (int)$body['type'];
        $user = $this->entityFactory->createAdminAncora($body);
        $user_slug = $this->adminAncoraModel->getSlug(null, $user->slug);
        if ($user_slug != NULL) {
          $this->flash->addMessage('danger', 'Não é permitido, cadastro de Login repetido.');
          return $this->httpRedirect($request, $response, "/admin/user");
        }
        $deleted = "deleted";
        $pos = strpos($user->slug, $deleted);
        //var_dump($user->slug);
        //var_dump($pos);die;
        if (!preg_match("/^([a-zA-Z0-9]+)$/", $user->slug) || ($pos != false)) {
          $this->flash->addMessage('danger', 'Não é permitido o uso de caracteres especiais ou espaços.');
          return $this->httpRedirect($request, $response, "/admin/user");
        }

        try {
          $this->adminAncoraModel->beginTransaction();
          $return_admin_ancora = $this->adminAncoraModel->add($user);
          if ($return_admin_ancora->status == false) {
            throw new ModelException($return_admin_sisgesp, "Erro no cadastro de Admin Ancora. COD:0001.");
          }
          $this->adminAncoraModel->commit();
          $this->flash->addMessage('success', 'Usuário adicionado com sucesso.');
          return $this->httpRedirect($request, $response, '/admin/user');
        } catch(ModelException $e) {
          $this->adminAncoraModel->rollback();
          CustomLogger::ModelErrorLog($e->getMessage(), $e->getdata());
          $this->flash->addMessage('danger', $e->getMessage() . ' Se o problema persistir contate um administrador.');
          return $this->httpRedirect($request, $response, "/admin/user");
        }
      } else {
        $this->flash->addMessage('danger', 'Rota não permitida para o usuário atual.');
        return $this->httpRedirect($request, $response, '/admin/user/all');
      }

    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        $userId = intval($args['id']);
        $currentUser = $this->adminAncoraModel->get();
        if ($userId == $currentUser->id) {
            $this->flash->addMessage('danger', 'Não é possível remover seu próprio usuário.');
            return $this->httpRedirect($request, $response, '/admin/user/all');
        }
        $usuario = $this->adminAncoraModel->get($userId);
        //var_dump($usuario->deleted);die;
        if ($usuario->deleted == 1) {
          $this->flash->addMessage('danger', 'Não é possível realizar esta ação.');
          return $this->httpRedirect($request, $response, '/admin/user/all');
        }
        $this->adminAncoraModel->delete($usuario);
        $this->flash->addMessage('success', 'Usuário removido com sucesso.');
        return $this->httpRedirect($request, $response, '/admin/user/all');
    }
    public function edit(Request $request, Response $response, array $args): Response
    {
        $userId = intval($args['id']);
        $user = $this->adminAncoraModel->get((int)$userId);//var_dump($user);die;
        if ($user !== false) {

          $permissao_type_user = ($_SESSION['admin_ancora']['type'] >= $user->type ) ? true : false;
        }
        if ($user === false) {

            $this->flash->addMessage('danger', 'Usuário não encontrado.');
            return $this->httpRedirect($request, $response, '/admin/user');
        }
        if ($permissao_type_user) {
          $roles = $this->roleModel->getAll();
          return $this->view->render($response, 'admin/user/edit.twig', [
              'user' => $user,
              'roles' => $roles
          ]);
        } else {
            $this->flash->addMessage('danger', 'Usuário não permissão.');
            return $this->httpRedirect($request, $response, '/admin/user');
        }
    }
    public function export(Request $request, Response $response)
    {
        $export = new Spreadsheet();
        $export->addColumn(new TextColumn('Nome'));
        $export->addColumn(new TextColumn('Email'));
        $export->addColumn(new DateColumn('Data do cadastro'));
        $export->addColumn(new TextColumn('Cargo no sistema'));
        $export->addColumn(new DateColumn('Data de nascimento'));
        $export->addColumn(new TextColumn('CPF'));
        $export->addColumn(new NumericColumn('DDD'));
        $export->addColumn(new NumericColumn('Telefone'));
        $export->addColumn(new TextColumn('Rua'));
        $export->addColumn(new TextColumn('Número'));
        $export->addColumn(new TextColumn('Complemento'));
        $export->addColumn(new TextColumn('Bairro'));
        $export->addColumn(new TextColumn('Cidade'));
        $export->addColumn(new TextColumn('Estado'));
        $export->addColumn(new TextColumn('CEP'));
        $users = $this->userModel->getAll();
        foreach ($users as $user) {
            $export->addRow([
                $user->name,
                $user->email,
                $user->created_at,
                $user->role,
                $user->nascimento,
                $user->cpf,
                $user->tel_area,
                $user->tel_numero,
                $user->end_rua,
                $user->end_numero,
                $user->end_complemento,
                $user->end_bairro,
                $user->end_cidade,
                $user->end_estado,
                $user->end_cep,
            ]);
        }
        $writer = new OdsWriter();
        $writer->includeColumnHeaders = true;
        // TODO: Refatorar para usar PSR-7
        $export->download($writer, 'Usuários-' . time());
    }
    public function view(Request $request, Response $response, array $args): Response
    {
        $userId = intval($args['id']);
        $user = $this->userModel->get((int)$userId);
        if (!$user) {
            $this->flash->addMessage('danger', 'Usuário não encontrado.');
            return $this->httpRedirect($request, $response, '/admin/user');
        }
        $roles = $this->roleModel->getAll();
        return $this->view->render($response, 'admin/user/view.twig', [
            'user' => $user,
            'roles' => $roles
        ]);
    }
    public function update(Request $request, Response $response): Response
    {
        $user = $this->entityFactory->createAdminAncora($request->getParsedBody());
        $old_user = $this->adminAncoraModel->get((int)$user->id);
        $user_slug = $this->adminAncoraModel->getSlug(null, $user->slug);

        $deleted = "deleted";
        $pos = strpos($user->slug, $deleted);

        if (($user_slug != NULL) && ($old_user->slug != $user->slug)) {
            $this->flash->addMessage('danger', 'Não é permitido, cadastro de Login repetido.');
            return $this->httpRedirect($request, $response, "/admin/user");
        }
        if (!preg_match("/^([a-zA-Z0-9]+)$/", $user->slug) || ($pos != false)) {
          $this->flash->addMessage('danger', 'Não é permitido o uso de caracteres especiais ou espaços.');
          return $this->httpRedirect($request, $response, "/admin/user");
        }
        try {
          $this->adminAncoraModel->beginTransaction();
          $return_admin_ancora = $this->adminAncoraModel->update($user);
          if ($return_admin_ancora->status == false) {
            throw new ModelException($return_admin_sisgesp, "Erro no cadastro de Admin Ancora. COD:0002.");
          }
          $this->adminAncoraModel->commit();
          $this->flash->addMessage('success', 'Usuário atualizado com sucesso.');
          return $this->httpRedirect($request, $response, '/admin/user');
        } catch(ModelException $e) {
          $this->adminAncoraModel->rollback();
          CustomLogger::ModelErrorLog($e->getMessage(), $e->getdata());
          $this->flash->addMessage('danger', $e->getMessage() . ' Se o problema persistir contate um administrador.');
          return $this->httpRedirect($request, $response, "/admin/user");
        }
    }

    public function verify_slug(Request $request, Response $response): Response {
    $body = $request->getParsedBody();
    if (isset($body['slug'])) {
      $body['slug'] = trim($body['slug']);
      $user = $this->adminAncoraModel->getSlug(null, $body['slug']);
      if ($user == false) {
        return $response->withJson(true, 200);
      }
      return $response->withJson(false, 200);
    }
    return $response->withJson(false, 200);
  }

  public function verify_slug_edit(Request $request, Response $response): Response {
    $body = $request->getParsedBody();
    $id_user = $request->getQueryParams()['user'];
    if (isset($body['slug'])) {
      $body['slug'] = trim($body['slug']);
      $user = $this->adminAncoraModel->getSlug(null, trim($body['slug']));
     //var_dump($user);die;
      // verify if not exist
      if ($user == null) {
        return $response->withJson(true, 200);
      }
      // verify if is the same.
      if ($user->id == $id_user) {
        return $response->withJson(true, 200);
      }
      return $response->withJson(false, 200);
    }
    return $response->withJson(false, 200);
  }
}
