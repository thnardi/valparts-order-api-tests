<?php
declare(strict_types=1);

// includes
use Farol360\Ancora\Controller\Admin\IndexController as IndexAdmin;
use Farol360\Ancora\Controller\Admin\ClientesController as ClientesAdmin;
use Farol360\Ancora\Controller\Admin\PermissionController as PermissionAdmin;
use Farol360\Ancora\Controller\Admin\PostController as PostController;
use Farol360\Ancora\Controller\Admin\RelatoriosController as RelatoriosAdmin;
use Farol360\Ancora\Controller\Admin\RoleController as RoleAdmin;
use Farol360\Ancora\Controller\Admin\UserController as UserAdmin;

use Farol360\Ancora\Controller\PageController as Page;
use Farol360\Ancora\Controller\UserController as User;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('[/]', Page::class . ':index');

$app->group('/admin', function () {
    $this->get('[/]', IndexAdmin::class . ':index');
    $this->map(['GET', 'POST'], '/login', IndexAdmin::class . ':login');
    $this->map(['GET', 'POST'], '/logout', IndexAdmin::class . ':logout');

    $this->group('/clientes', function () {
      $this->get('[/]', ClientesAdmin::class . ':index');
      //$this->map(['GET', 'POST'], '/add', ClientesAdmin::class . ':add');
      $this->group('/add', function () {
          $this->map(['GET', 'POST'], '[/]', ClientesAdmin::class . ':add');
          $this->post('/verify_slug', ClientesAdmin::class . ':verify_slug');
        });
      $this->get('/{id:[0-9]+}', ClientesAdmin::class . ':view');
      $this->get('/delete/{id:[0-9]+}', ClientesAdmin::class . ':delete');
      $this->get('/edit/{id:[0-9]+}', ClientesAdmin::class . ':edit');
      $this->post('/edit/verify_slug', ClientesAdmin::class . ':verify_slug_edit');
      $this->post('/update', ClientesAdmin::class . ':update');
    });
    $this->group('/tipos_de_cliente', function () {
      $this->get('[/]', ClientesAdmin::class . ':tipos_de_cliente');
      //$this->map(['GET', 'POST'], '/add', ClientesAdmin::class . ':tipos_de_cliente_add');
      $this->group('/add', function () {
          $this->map(['GET', 'POST'], '[/]', ClientesAdmin::class . ':tipos_de_cliente_add');
          $this->post('/verify_slug', ClientesAdmin::class . ':verify_slug_type');
        });
      $this->get('/edit/{id:[0-9]+}', ClientesAdmin::class . ':tipos_de_cliente_edit');
      $this->post('/edit/verify_slug', ClientesAdmin::class . ':verify_slug_edit_type');
      $this->post('/update', ClientesAdmin::class . ':tipos_de_cliente_update');
    });

    $this->group('/permission', function () {
        $this->get('[/]', PermissionAdmin::class . ':index');
        $this->map(['GET', 'POST'], '/add', PermissionAdmin::class . ':add');
        $this->get('/delete/{id:[0-9]+}', PermissionAdmin::class . ':delete');
        $this->get('/edit/{id:[0-9]+}', PermissionAdmin::class . ':edit');
        $this->post('/update', PermissionAdmin::class . ':update');
    });


    $this->group('/posts', function () {
        $this->get('[/]', PostController::class . ':index');
        $this->map(['GET', 'POST'], '/add', PostController::class . ':add');
        $this->get('/delete/{id:[0-9]+}', PostController::class . ':delete');
        $this->get('/edit/{id:[0-9]+}', PostController::class . ':edit');
        $this->post('/update', PostController::class . ':update');
    });

    $this->group('/relatorios', function () {
      $this->get('[/]', RelatoriosAdmin::class . ':index');
    });

    $this->group('/role', function () {
        $this->get('[/]', RoleAdmin::class . ':index');
        $this->map(['GET', 'POST'], '/add', RoleAdmin::class . ':add');
        $this->get('/delete/{id:[0-9]+}', RoleAdmin::class . ':delete');
        $this->get('/edit/{id:[0-9]+}', RoleAdmin::class . ':edit');
        $this->post('/update', RoleAdmin::class . ':update');
    });

    $this->get('/sobre', IndexAdmin::class . ':about');

    $this->group('/user', function () {
        $this->get('[/]', UserAdmin::class . ':index');
        $this->get('/all', UserAdmin::class . ':index');
        $this->get('/export', UserAdmin::class . ':export');
        $this->get('/{id:[0-9]+}', UserAdmin::class . ':view');
        $this->group('/add', function () {
            $this->map(['GET', 'POST'], '[/]', UserAdmin::class . ':add');
            $this->post('/verify_slug', UserAdmin::class . ':verify_slug');

        });
        //$this->map(['GET', 'POST'], '/add', UserAdmin::class . ':add');
        //$this->post('/verify_slug', UserAdmin::class . ':verify_slug');
        $this->get('/delete/{id:[0-9]+}', UserAdmin::class . ':delete');
        $this->get('/edit/{id:[0-9]+}', UserAdmin::class . ':edit');
        $this->post('/edit/verify_slug', UserAdmin::class . ':verify_slug_edit');
        $this->post('/update', UserAdmin::class . ':update');
    });
});

$app->get('/create_captcha', Page::class .':create_captcha');

$app->group('/posts', function() {
    $this->get('[/]', Page::class . ':posts');
    $this->get('/{id:[0-9]+}', Page::class . ':post');
});



$app->group('/users', function () {
    $this->get('/dashboard', User::class . ':dashboard');
    $this->map(['GET', 'POST'], '/profile', User::class . ':profile');
    $this->map(['GET', 'POST'], '/recover', User::class . ':recover');
    $this->map(['GET', 'POST'], '/recover/token/{token}', User::class . ':recoverPassword');
    $this->map(['GET', 'POST'], '/signin', User::class . ':signIn');
    $this->get('/signout', User::class . ':signOut');
    $this->map(['GET', 'POST'], '/signup', User::class . ':signUp');
    $this->get('/verify/{token}', User::class . ':verify');

});
