<?php
declare(strict_types=1);

namespace Farol360\Ancora\Controller\Admin;

use Farol360\Ancora\Controller;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use Slim\Flash\Messages as FlashMessages;
use Slim\Views\Twig as View;

use Farol360\Ancora\Model;
use Farol360\Ancora\Model\EntityFactory;

class PostController extends Controller
{

    protected $postModel;
    protected $postTypeModel;
    protected $entityFactory;

    public function __construct(View $view, FlashMessages $flash, Model $postModel, Model $postTypeModel, EntityFactory $entityFactory) {

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
        $posts = $this->postModel->getAll($offset, $limit, $trash);
        $postsType = $this->postTypeModel->getAll();

        // pagination controll;
        $amountPosts = $this->postModel->getAmount();
        $amountPages = ceil($amountPosts->amount / $limit);


        return $this->view->render($response, 'admin/post/index.twig', [
            'posts' => $posts,
            'postsType' => $postsType,
            'page' => $page,
            'amountPages' => $amountPages
            ]);
    }

    public function add(Request $request, Response $response, array $args)
    {

        // if has nothing on body, it is a plain empty page.
        if (empty($request->getParsedBody())) {
            $postTypes = $this->postTypeModel->getPublished();


            return $this->view->render($response, 'admin/post/add.twig', [
                'postTypes' => $postTypes

                ]);
        }

        // --------
        // if has something in request body.
        // --------

        // getting data from request;
        $data = $request->getParsedBody();

        // if has any treatment on data, do it in here..
        // $data = ?

        $data['img_featured'] = '';

        if ($data['status'] == null) {
            $data['status'] = 1;
        } else {
            $data['status'] = (int) $data['status'];
        }

        $data['trash'] = 0;


        $data['id_post_type'] = (int) $data['id_post_type'];
        // create post from data;
        $post = $this->entityFactory->createPost($data);

        // add post on db
        $post->id = (int) $this->postModel->add($post);

        // if has all ok in add on db
        if($post->id !== null) {

            // -------
            // working on uploaded images by usr
            // -------

            // get uploaded files
            $files = $request->getUploadedFiles();

            // if has file in img_featured key
            if (!empty($files['img_featured'])) {
                $image = $files['img_featured'];

                //if has no error on upload
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
                        return $this->httpRedirect($request, $response, '/admin/post/add');
                    }

                    //verify size
                    if ($image->getSize() > 400000) {

                        //inform error msg
                        $this->flash->addMessage('danger', "Imagem muito grande (max 300kb).");

                        //redirect to this url
                        return $this->httpRedirect($request, $response, '/admin/posts/add');
                    }

                    // --------
                    // if pass by all verificators..
                    // --------

                    // cabulous function
                    $filename = sprintf(
                        '%s.%s',
                        uniqid(),
                        pathinfo($image->getClientFilename(), PATHINFO_EXTENSION)
                    );

                    // path to usr img
                    $path = 'upload/img/';

                    // move img to path
                    $image->moveTo($path . $filename);

                    // update path in db post
                    $post->img_featured = $path . $filename;

                    $this->postModel->update($post);

                    // add sucess msg
                    $this->flash->addMessage('success', "Post adicionado com sucesso.");

                    // redirect to posts list
                    return $this->httpRedirect($request, $response, '/admin/posts');

                // if has error on $image
                } else {
                    $size = $image->getSize();
                    if ($size == 0) {
                        $this->flash->addMessage('success', "Post adicionado com sucesso. Imagem padrão utilizada.");
                        return $this->httpRedirect($request, $response, '/admin/posts');
                    }
                    $this->flash->addMessage('danger', "Erro ao adicionar imagem. Favor contactar a Farol 360. Erro número: " . $image->getError());
                    return $this->httpRedirect($request, $response, '/admin/posts');
                }
            // if $files['img_featured'] is empty
            } else {

                $this->flash->addMessage('danger', "Erro ao adicionar (imagem vazia). Favor contactar a Farol 360.");
                return $this->httpRedirect($request, $response, '/admin/posts');
            }
        }

        // if get this point, something unforeseen happened
        $this->flash->addMessage('danger', "Erro indefinido ao adicionar Posts. Por favor entre em contato com a Farol 360.");
        return $this->httpRedirect($request, $response, '/admin/posts');
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
}
