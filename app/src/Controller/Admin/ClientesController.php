<?php
declare(strict_types=1);

namespace Farol360\Ancora\Controller\Admin;
use Farol360\Ancora\Model\ModelException;
use Farol360\Ancora\CustomLogger;

use Farol360\Ancora\Controller;
use Farol360\Ancora\Model;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Flash\Messages as FlashMessages;
use Slim\Views\Twig as View;

use Farol360\Ancora\User;
use Farol360\Ancora\AdminAncora;
use Farol360\Ancora\UserModel;

class ClientesController extends Controller
{

    protected $version;
    protected $adminAncoraModel;
    protected $userModel;
    protected $entityFactory;
    protected $eventLogAdminActionModel;
    protected $eventLogAdminActionTypeModel;

    public function __construct(
        View $view,
        FlashMessages $flash,
        Model $adminAncoraModel,
        Model $userModel,
        Model $eventLogAdminActionModel,
        Model $eventLogAdminActionTypeModel,
        $entityFactory,
        $version
    ) {
        parent::__construct($view, $flash);
        $this->adminAncoraModel = $adminAncoraModel;
        $this->userModel = $userModel;
        $this->eventLogAdminActionModel = $eventLogAdminActionModel;
        $this->eventLogAdminActionTypeModel = $eventLogAdminActionTypeModel;
        $this->entityFactory = $entityFactory;
        $this->version = $version;
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
        $amountCliente = $this->userModel->getAmount();
        $amountPages = ceil($amountCliente->amount / $limit);

        $pageTitle = 'Clientes';
        $clientes = $this->userModel->getAllOrder($order, $filtro, $offset, $limit);
        //var_dump($clientes);die;
        //$admin_ancora = $_SESSION['admin_ancora'];
        foreach($clientes as $cliente) {
            $new_data = explode(" ", $cliente->created_at);
            $data_separado = explode("-", $new_data[0]);
            $cliente->created_at = "$data_separado[2]/$data_separado[1]/$data_separado[0] $new_data[1]";
      }//var_dump($admin_ancora);die;
      return $this->view->render($response, 'admin/clientes/index.twig', [
        'clientes' => $clientes,
        //'users' => $users,
        'page_title' => $pageTitle,
        'page' => $page,
        'amountPages' => $amountPages,
        'order' => $order,
        'filtro' => $filtro
      ]);
    }

    public function add(Request $request, Response $response, array $args)
    {
      $permissao_type_user = ($_SESSION['admin_ancora']['type'] > 1 ) ? true : false;
      if ($permissao_type_user) {
        if (empty($request->getParsedBody())) {
          $admin_ancora = $_SESSION['admin_ancora'];
            return $this->view->render($response, 'admin/clientes/add.twig', [
            'admin_ancora' => $admin_ancora
          ]);
        }

        $clientes = $request->getParsedBody();
        //var_dump($clientes);die;
        $clientes = $this->entityFactory->createUser($request->getParsedBody());
        //var_dump($clientes);
        $clientes_slug = $this->userModel->getSlug(null, $clientes->slug);
        //var_dump($clientes_slug);die;
        if ($clientes_slug != NULL) {
          $this->flash->addMessage('danger', 'Não é permitido, cadastro de Login repetido.');
          return $this->httpRedirect($request, $response, "/admin/clientes");
        }
        $deleted = "deleted";
        $pos = strpos($clientes->slug, $deleted);
        //var_dump($clientes->slug);
        //var_dump($pos);die;
        if (!preg_match("/^([a-zA-Z0-9]+)$/", $clientes->slug) || ($pos != false)) {
          $this->flash->addMessage('danger', 'Não é permitido o uso de caracteres especiais ou espaços.');
          return $this->httpRedirect($request, $response, "/admin/clientes");
        }
        try {
          $this->userModel->beginTransaction();
          //var_dump($clientes);die;
          $return_clientes = $this->userModel->add($clientes);
          if ($return_clientes->status == false) {
            var_dump($return_clientes);die;
            throw new ModelException($return_clientes, "Erro no cadastro de Admin Ancora. COD:0001.");
          }
          $this->userModel->commit();
          $this->flash->addMessage('success', 'Cliente cadastrado com sucesso.');
          return $this->httpRedirect($request, $response, "/admin/clientes");
        } catch(ModelException $e) {
          $this->userModel->rollback();
          CustomLogger::ModelErrorLog($e->getMessage(), $e->getdata());
          $this->flash->addMessage('danger', $e->getMessage() . ' Se o problema persistir contate um administrador.');
          return $this->httpRedirect($request, $response, "/admin/clientes");
        }
        } else {
        $this->flash->addMessage('danger', 'Rota não permitida para o usuário atual.');
        return $this->httpRedirect($request, $response, '/admin/clientes');
      }
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
      $clienteId = intval($args['id']);
      $currentCliente = $this->userModel->get();
      if ($clienteId == $currentCliente->id) {
          $this->flash->addMessage('danger', 'Não é possível remover seu próprio usuário.');
          return $this->httpRedirect($request, $response, '/admin/clientes');
      }
      $cliente = $this->userModel->get($clienteId);
      //var_dump($cliente->deleted);die;
      if ($cliente->deleted == 1) {
        $this->flash->addMessage('danger', 'Não é possível realizar esta ação.');
        return $this->httpRedirect($request, $response, '/admin/clientes');
      }
      $this->userModel->delete($cliente);
      $this->flash->addMessage('success', 'Usuário removido com sucesso.');
      return $this->httpRedirect($request, $response, '/admin/clientes');
    }

    public function edit(Request $request, Response $response, array $args): Response
    {
      $userId = intval($args['id']);
        $user = $this->userModel->get((int)$userId);//var_dump($user);die;
        if ($user !== false) {

          $permissao_type_user = ($_SESSION['admin_ancora']['type'] > 1 ) ? true : false;
        }
        if ($user === false) {

            $this->flash->addMessage('danger', 'Usuário não encontrado.');
            return $this->httpRedirect($request, $response, '/admin/clientes');
        }
        if ($permissao_type_user) {
          //$roles = $this->roleModel->getAll();
          return $this->view->render($response, 'admin/clientes/edit.twig', [
              'user' => $user
              //'roles' => $roles
          ]);
        } else {
            $this->flash->addMessage('danger', 'Usuário não permissão.');
            return $this->httpRedirect($request, $response, '/admin/clientes');
        }
    }

    public function disable(Request $request, Response $response, array $args): Response
    {
        $postId = intval($args['id']);
        $this->postModel->disable($postId);
        $this->flash->addMessage('success', "Post desabilitado com sucesso.");
        return $this->httpRedirect($request, $response, '/admin/posts');
    }

    public function enable(Request $request, Response $response, array $args): Response
    {
        $postId = intval($args['id']);
        $this->postModel->enable($postId);
        $this->flash->addMessage('success', "Post habilitado com sucesso.");
        return $this->httpRedirect($request, $response, '/admin/posts');
    }

    /**
        To remove an post from trash
    */
    public function trashRemove(Request $request, Response $response, array $args): Response
    {

        // get id
        $postId = intval($args['id']);
        $post = $this->postModel->get($postId);

        // if post exist
        if ($post != null) {

            // set to send trash or recover
            $this->postModel->trashRemove($postId);
            $this->flash->addMessage('success', "Post removido da lixeira.");
            return $this->httpRedirect($request, $response, '/admin/trash');

        } else {
            $this->flash->addMessage('danger', "Post não encontrado.");
            return $this->httpRedirect($request, $response, '/admin/trash');
        }


    }

    /**
        To send an post to trash
    */
    public function trashSend(Request $request, Response $response, array $args): Response
    {

        // get id
        $postId = intval($args['id']);
        $post = $this->postModel->get($postId);

        // if post exist
        if ($post != null) {

            // set to send trash or recover
            $this->postModel->trashSend($postId);
            $this->flash->addMessage('success', "Post enviado para a lixeira.");
            return $this->httpRedirect($request, $response, '/admin/posts');

        } else {
            $this->flash->addMessage('danger', "Post não encontrado.");
            return $this->httpRedirect($request, $response, '/admin/posts');
        }


    }

    public function trashIndex(Request $request, Response $response, array $args): Response
    {

        // get params
        $params = $request->getQueryParams();

        // pagination params
        if (!empty($params['page'])) {
            $page = intval($params['page']);
        } else {
            $page = 1;
        }
        $limit = 10;
        $offset = ($page - 1) * $limit;

        // list of posts and types
        $trash = 1;
        $posts = $this->postModel->getAll($offset, $limit, $trash);
        $postsType = $this->postTypeModel->getAll();

        // pagination controll;
        $amountPosts = $this->postModel->getAmount();
        $amountPages = ceil($amountPosts->amount / $limit);


        return $this->view->render($response, 'admin/post/trashIndex.twig', [
            'posts' => $posts,
            'postsType' => $postsType,
            'page' => $page,
            'amountPages' => $amountPages
            ]);



    }

    public function update(Request $request, Response $response): Response
    {
      $user = $this->entityFactory->createUser($request->getParsedBody());
      //var_dump($user);die;
        $old_user = $this->userModel->get((int)$user->id);
        $user_slug = $this->userModel->getSlug(null, $user->slug);
        //var_dump($old_user);
        //var_dump($user);die;
        $deleted = "deleted";
        $pos = strpos($user->slug, $deleted);

        if (($user_slug != NULL) && ($old_user->slug != $user->slug)) {
            $this->flash->addMessage('danger', 'Não é permitido, cadastro de Login repetido.');
            return $this->httpRedirect($request, $response, "/admin/clientes");
        }
        if (!preg_match("/^([a-zA-Z0-9]+)$/", $user->slug) || ($pos != false)) {
          $this->flash->addMessage('danger', 'Não é permitido o uso de caracteres especiais ou espaços.');
          return $this->httpRedirect($request, $response, "/admin/clientes");
        }
        try {
          $this->userModel->beginTransaction();
          $return_user = $this->userModel->update($user);
          //var_dump($return_user);die;
          if ($return_user->status == false) {
            throw new ModelException($return_user, "Erro no cadastro de Admin Ancora. COD:0002.");
          }
          $this->userModel->commit();
          $this->flash->addMessage('success', 'Usuário atualizado com sucesso.');
          return $this->httpRedirect($request, $response, '/admin/clientes');
        } catch(ModelException $e) {
          $this->userModel->rollback();
          CustomLogger::ModelErrorLog($e->getMessage(), $e->getdata());
          $this->flash->addMessage('danger', $e->getMessage() . ' Se o problema persistir contate um administrador.');
          return $this->httpRedirect($request, $response, "/admin/clientes");
        }
    }


    public function verify_slug(Request $request, Response $response): Response {
    $body = $request->getParsedBody();
    if (isset($body['slug'])) {
      $body['slug'] = trim($body['slug']);
      $cliente = $this->userModel->getSlug(null, $body['slug']);
      if ($cliente == false) {
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
      $user = $this->userModel->getSlug(null, trim($body['slug']));
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

  public function view(Request $request, Response $response, array $args): Response
    {
        $userId = intval($args['id']);
        $user = $this->userModel->get((int)$userId);
        if (!$user) {
            $this->flash->addMessage('danger', 'Usuário não encontrado.');
            return $this->httpRedirect($request, $response, '/admin/clientes');
        }
        //$roles = $this->roleModel->getAll();
        return $this->view->render($response, 'admin/clientes/view.twig', [
            'user' => $user
            //'roles' => $roles
        ]);
    }
  public function clientes_types(Request $request, Response $response): Response
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
        $amountCliente = $this->userModel->getAmount();
        $amountPages = ceil($amountCliente->amount / $limit);

        $pageTitle = 'Clientes';
        $clientes = $this->userModel->getAllOrder($order, $filtro, $offset, $limit);
        //var_dump($clientes);die;
        //$admin_ancora = $_SESSION['admin_ancora'];
        foreach($clientes as $cliente) {
            $new_data = explode(" ", $cliente->created_at);
            $data_separado = explode("-", $new_data[0]);
            $cliente->created_at = "$data_separado[2]/$data_separado[1]/$data_separado[0] $new_data[1]";
      }//var_dump($admin_ancora);die;
      return $this->view->render($response, 'admin/clientes_types/index.twig', [
        'clientes' => $clientes,
        //'users' => $users,
        'page_title' => $pageTitle,
        'page' => $page,
        'amountPages' => $amountPages,
        'order' => $order,
        'filtro' => $filtro
      ]);
    }
}
