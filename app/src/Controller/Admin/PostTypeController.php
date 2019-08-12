<?php
declare(strict_types=1);

namespace Farol360\Ancora\Controller\Admin;
use Farol360\Ancora\Model\ModelException;
use Farol360\Ancora\CustomLogger;
use Farol360\Ancora\Controller;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Slim\Flash\Messages as FlashMessages;
use Slim\Views\Twig as View;

use Farol360\Ancora\Model;
use Farol360\Ancora\Model\EntityFactory;

class PostTypeController extends Controller
{
  protected $postModel;
  protected $postTypeModel;
  protected $entityFactory;

  public function __construct(View $view, FlashMessages $flash,Model $postModel, Model $postTypeModel, EntityFactory $entityFactory) {
    parent::__construct($view, $flash);
    $this->postModel = $postModel;
    $this->postTypeModel = $postTypeModel;
    $this->entityFactory = $entityFactory;
  }

  public function index(Request $request, Response $response): Response
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
    $trash = 0;
    $post_types = $this->postTypeModel->getAll($offset, $limit);
    // pagination controll;
    $amountPosts = $this->postTypeModel->getAmount();
    $amountPages = ceil($amountPosts->amount / $limit);
    return $this->view->render($response, 'admin/post_types/index.twig', [
      'post_types' => $post_types,
      'page' => $page,
      'amountPages' => $amountPages
    ]);
  }

  public function add(Request $request, Response $response, array $args)
  {
    // if has nothing on body, it is a plain empty page.
    if (empty($request->getParsedBody())) {
      return $this->view->render($response, 'admin/post_types/add.twig');
    }
    // getting data from request;
    $data = $request->getParsedBody();
    // if has any treatment on data, do it in here..
    if (!isset($data['status'])) {
      $data['status'] = 0;
    } else {
      $data['status'] = (int) $data['status'];
    }
    try {
      $this->postTypeModel->beginTransaction();
      // create postType from data;
      $postType = $this->entityFactory->createPostType($data);
      // var_dump($data);
      // var_dump($postType);
      // die;
      // add postType on db
      $return_post_type = $this->postTypeModel->add($postType);

      $this->postTypeModel->commit();
      $this->flash->addMessage('success', "Tipo de post adicionado com Sucesso.");
      return $this->httpRedirect($request, $response, '/admin/post_types');
    } catch (ModelException $e) {
      $this->postTypeModel->rollback();
      CustomLogger::ModelErrorLog($e->getMessage(), $e->getdata());
      $this->flash->addMessage('danger', $e->getMessage() . ' Se o problema persistir contate um administrador.');
      return $this->httpRedirect($request, $response, "/admin/post_types");
    }

    // if get this point, something unforeseen happened
    $this->flash->addMessage('danger', "Erro indefinido ao adicionar posts. Por favor entre em contato com a Farol 360.");
    return $this->httpRedirect($request, $response, '/admin/post_types');
  }

  public function edit(Request $request, Response $response, array $args): Response
  {
    // retrive argument id in url, if has it
    $postTypeId = intval($args['id']);
    // select in db the post by id
    $postType = $this->postTypeModel->get($postTypeId);
    // if post dnt exist, return error
    if (!$postType) {
         $this->flash->addMessage('danger', "Tipo de posto não encontrado.");
        return $this->httpRedirect($request, $response, '/admin/post_types');
    }
    return $this->view->render($response, 'admin/post_types/edit.twig', [
            'post_type' => $postType,
        ]
    );
  }

  public function disable(Request $request, Response $response, array $args): Response
  {
    $postId = intval($args['id']);
    $posts = $this->postModel->disableBypostType($postId);
    $this->postTypeModel->disable($postId);
    $this->flash->addMessage('success', "Tipo de posto desabilitado com sucesso.");
    return $this->httpRedirect($request, $response, '/admin/post_types');
  }

  public function enable(Request $request, Response $response, array $args): Response
  {
      $postId = intval($args['id']);
      $this->postTypeModel->enable($postId);
      $this->flash->addMessage('success', "Tipo de posto habilitado com sucesso.");
      return $this->httpRedirect($request, $response, '/admin/post_types');
  }

  public function delete(Request $request, Response $response, array $args): Response
    {
      $postId = intval($args['id']);
      //$currentPost = $this->postTypeModel->get();
      //if ($postId == $currentPost->id) {
          //$this->flash->addMessage('danger', 'Não é possível remover seu próprio tipo de post.');
          //return $this->httpRedirect($request, $response, '/admin/post_types');
      //}
      $post_type = $this->postTypeModel->get($postId);
      //var_dump($post_type->deleted);die;
      if ($post_type->deleted == 1) {
        $this->flash->addMessage('danger', 'Não é possível realizar esta ação.');
        return $this->httpRedirect($request, $response, '/admin/post_types');
      }
      $this->postTypeModel->delete($post_type);
      $this->flash->addMessage('success', 'Tipo de post removido com sucesso.');
      return $this->httpRedirect($request, $response, '/admin/post_types');
    }

  public function update(Request $request, Response $response): Response
  {
      // get data from body request
      $data = $request->getParsedBody();

      // if has any treatment on data, do it in here..
      // $data = ?

      $oldpost = $this->postTypeModel->get((int)$data['id']);
      $data['trash'] = (int)$oldpost->trash;

      // if status not set..
      if (!isset($data['status']) ) {
          $data['status'] = 0;

      // just typecast to int
      } else {
          $data['status'] = (int) $data['status'];
      }

      // create object post
      $postType = $this->entityFactory->createpostType($data);

      $this->postTypeModel->update($postType);

      $this->flash->addMessage('success', "Tipo de posto atualizado com sucesso.");
      return $this->httpRedirect($request, $response, '/admin/post_types');
  }

  public function verifytoremove(Request $request, Response $response, array $args)
  {
      $postTypeId = (int) $args['id'];
      $post_types = $this->postTypeModel->get($postTypeId);
      if (isset($post_types)) {
          $postAmount = $this->postModel->getAmountBypostType((int) $post_types->id);
          echo $postAmount->amount ;
      }
  }

  public function verifytounpublish(Request $request, Response $response, array $args)
  {
      $postTypeId = (int) $args['id'];
      $post_types = $this->postTypeModel->get($postTypeId);
      if (isset($post_types)) {
          $postAmount = $this->postModel->getAmountPublishedBypostType((int) $post_types->id);
          return $response->withJson($postAmount->amount, 200);
      }
  }

  public function verify_slug(Request $request, Response $response): Response
    {
    $body = $request->getParsedBody();
    if (isset($body['slug'])) {
      $body['slug'] = trim($body['slug']);
      $post_type = $this->postTypeModel->getSlug(null, $body['slug']);
      if ($post_type == false) {
        return $response->withJson(true, 200);
      }
      return $response->withJson(false, 200);
    }
      return $response->withJson(false, 200);
    }

  public function verify_slug_edit(Request $request, Response $response): Response
    {
      $body = $request->getParsedBody();
      $id_post_type = $request->getQueryParams()['post_types'];
      if (isset($body['slug'])) {
        $body['slug'] = trim($body['slug']);
        $post = $this->postTypeModel->getSlug(null, trim($body['slug']));
       //var_dump($post);die;
        // verify if not exist
        if ($post == null) {
          return $response->withJson(true, 200);
        }
        // verify if is the same.
        if ($post->id == $id_post_type) {
          return $response->withJson(true, 200);
        }
        return $response->withJson(false, 200);
      }
      return $response->withJson(false, 200);//var_dump('teste');die;
    }
}
