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

    public function __construct(
        View $view,
        FlashMessages $flash,
        Model $adminAncoraModel,
        Model $userModel,
        $entityFactory,
        $version
    ) {
        parent::__construct($view, $flash);
        $this->adminAncoraModel = $adminAncoraModel;
        $this->userModel = $userModel;
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
        $clientes = $this->userModel->getAll($offset, $limit);
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
      if (empty($request->getParsedBody())) {
            return $this->view->render($response, 'admin/clientes/add.twig');
        }

        $clientes = $request->getParsedBody();

        $clientes = $this->entityFactory->createUser($request->getParsedBody());
        //var_dump($clientes);die;
        $this->userModel->add($clientes);
        $this->flash->addMessage('success', 'Cliente cadastrado com sucesso.');
        return $this->httpRedirect($request, $response, "/admin/clientes");
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        $post_id = intval($args['id']);
        $this->postModel->delete((int)$post_id);


        $this->flash->addMessage('success', 'Postagem removida com sucesso.');
        return $this->httpRedirect($request, $response, '/admin/posts');
    }

    public function edit(Request $request, Response $response, array $args): Response
    {

        // retrive argument id in url, if has it
        $postId = intval($args['id']);

        // select in db the post by id
        $post = $this->postModel->get($postId);

        // if post dnt exist, return error
        if (!$post) {
             $this->flash->addMessage('danger', "Post não encontrado.");
            return $this->httpRedirect($request, $response, '/admin/posts');
        }

        // get objets to render edit interface
        $postTypes = $this->postTypeModel->getPublished();

        return $this->view->render($response, 'admin/post/edit.twig', [
                'post' => $post,
                'postTypes' => $postTypes
            ]
        );

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
        // get data from body request
        $data = $request->getParsedBody();

        // if has any treatment on data, do it in here..
        // $data = ?

        // if status not set..
        if ($data['status'] == null ) {
            $data['status'] = 1;

        // just typecast to int
        } else {
            $data['status'] = (int) $data['status'];

        }

        $data['id_post_type'] = (int) $data['id_post_type'];

        // create object post
        $post = $this->entityFactory->createpost($data);

        $oldpost = $this->postModel->get((int) $post->id);

        $post->trash = $oldpost->trash;

        $files = $request->getUploadedFiles();

        // if files are empty means size == 0
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
                    $this->flash->addMessage('danger', "Imagem em formato inválido.");

                    //redirect to this url
                    return $this->httpRedirect($request, $response, '/admin/posts/add');
                }

                //verify size
                if ($image->getSize() > 400000) {

                    //inform error msg
                    $this->flash->addMessage('danger', "Imagem muito grande (max 300kb).");

                    //redirect to this url
                    return $this->httpRedirect($request, $response, '/admin/posts/add');
                }

                $filename = sprintf(
                    '%s.%s',
                    uniqid(),
                    pathinfo($image->getClientFilename(), PATHINFO_EXTENSION)
                );

                $path = 'upload/img/';
                $image->moveTo($path . $filename);
                $post->img_featured = $path . $filename;

            }

            // remove old img from disk
            if (file_exists($request->getParsedBody()['img_featured_old'])) {
                unlink($request->getParsedBody()['img_featured_old']);
            }

        // if has no image, set old as atual
        } else {
            $oldpost = $this->postModel->get((int)$post->id);
            $post->img_featured = $oldpost->img_featured;

        }


        $this->postModel->update($post);

        $this->flash->addMessage('success', "Post atualizado com sucesso.");
        return $this->httpRedirect($request, $response, '/admin/posts');



    }


    public function verify_slug(Request $request, Response $response): Response {
    $body = $request->getParsedBody();
    if (isset($body['slug'])) {
      $body['slug'] = trim($body['slug']);
      $user = $this->userModel->getSlug(null, $body['slug']);
      if ($clientes == false) {
        return $response->withJson(true, 200);
      }
      return $response->withJson(false, 200);
    }
    return $response->withJson(false, 200);
  }
}
