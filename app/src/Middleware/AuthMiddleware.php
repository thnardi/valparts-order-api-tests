<?php
declare(strict_types=1);

namespace Farol360\Ancora\Middleware;

use Farol360\Ancora\User;
use Farol360\Ancora\Model;
use Farol360\Ancora\AdminAncora;
use Slim\Flash\Messages as FlashMessages;
use Slim\Views\Twig;

class AuthMiddleware
{
    private $flash;
    private $userModel;
    private $adminAncoraModel;
    private $view;

    public function __construct(Twig $view, FlashMessages $flash, Model $user, Model $adminAncoraModel)
    {
        $this->flash = $flash;
        $this->userModel = $user;
        $this->adminAncoraModel = $adminAncoraModel;
        $this->view = $view;
    }

    /**
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param  callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */

    /**
     *
     */
    public function __invoke($request, $response, $next)
    {
        // unset($_SESSION['user']);
        // var_dump($_SESSION);
        // die;

        /*
        *   vars
        */

        // route mean the actual route, the one who is accessed at the time this function is called.
        $route = $request->getUri()->getPath();

        // get admin routes
        $admin_ancora_routes = AdminAncora::getAdminAncoraRoutes();

        // a list of allowed routes to this user
        $permissions = User::getPermissionsValueByRoleList();

        if (isset($request->getAttributes()['route'])) {
            $arguments = $request->getAttributes()['route']->getArguments();
        } else {
            $arguments = '';
        }

        //remove last '/' if present
        $route = substr(
            $route,
            0,
            (strlen($route) - 1)
        ) . str_replace(
            '/',
            '',
            substr(
                $route,
                (strlen($route) - 1),
                (strlen($route) - 0)
            )
        );

        //remove first '/', replace others '/' for '_'
        //and replace '_\d+' for '_:id'
        $route = "p_" .
        preg_replace(
            "/(_\d+)/im",
            "_:id",
            str_replace(
                '/',
                '',
                substr($route, 0, 1)
            ) . substr(
                str_replace('/', '_', $route),
                1,
                strlen($route)
            )
        );

        //replace token for :token
        $route = preg_replace("/(!!\w+)/im", ":token", $route);
        $route = preg_replace("/(%21%21\w+)/im", ":token", $route);

        if ($arguments != '') {
            foreach ($arguments as $key => $value) {
                $route = str_replace($value, ':'.$key, $route);
            }
        }

        if (isset($admin_ancora_routes[$route])) {
            // tratamento especial para a rota de login administrativo
            if ($route == 'p_admin_login') {
              // se estiver logado
              if (AdminAncora::isAuth()) {
                $this->flash->addMessage('errorLogin', "Você já está logado!");
                return $response->withStatus(302)->withHeader(
                  'Location',
                  $request->getUri()->getBaseUrl() .'/admin'
                );
              } else {

              }
            }
            // se a rota for diferente de login
            if ($route != 'p_admin_login') {
              // se estiver logado
              if (AdminAncora::isAuth()) {
                //echo "autenticado";
                // se não estiver logado, redireciona
              } else {
                $_SESSION['return'] = $route;
                $this->flash->addMessage('errorLogin', "É necessário estar autenticado!");
                return $response->withStatus(302)->withHeader(
                'Location',
                $request->getUri()->getBaseUrl() .'/admin/login'
                );
              }
            }
            $response = $next($request, $response);
            return $response;
          }

        // Authentication Flow:

        // 1 - if $route do not exist, then 404.
        if (!isset($permissions[$route])) {
            return $this->view->render($response->withStatus(404), '404.twig');
        }

        // 2 - if $route exist but not allowed ($permission[$route] == false)
        if ($permissions[$route] === false) {
            return $this->view->render($response->withStatus(403), '403.twig', ['is_auth' => User::isAuth()]);
        } else {

        }

        if (isset($permissions[$route])) {
            $allowed = $permissions[$route];
        } else {
            $allowed = false;
        }


        //var_dump($route);
        //var_dump($permissions);
        // var_dump($allowed);
        // var_dump($route);
        // var_dump($admin_ancora_routes);
        // var_dump($permissions);
        // die;

        // 2 - if $route exist but not allowed ($permission[$route] == false)
        if ($permissions[$route] === false) {
        // 2.a - if is a admin route
        if ( $route == 'p_admin') {
          // verify if user is not auth. redirect to login.
          if (!User::isAuth()) {
            $_SESSION['return'] = $route;
            $this->flash->addMessage('errorLogin', "É necessário estar autenticado!");
            return $response->withStatus(302)->withHeader(
              'Location',
              $request->getUri()->getBaseUrl() . '/admin/login'
            );
          }
        }

        if ( strpos($route, "perfil") !== false) {
          if (!User::isAuth()) {
            $_SESSION['return'] = $route;
            $this->flash->addMessage('errorLogin', "É necessário estar autenticado!");
            return $response->withStatus(302)->withHeader(
              'Location',
              $request->getUri()->getBaseUrl() .'/acesso'
            );
          }
        }

        return $this->view->render($response->withStatus(403), '403.twig', ['is_auth' => User::isAuth()]);
      } else {

      }
      // var_dump($_SESSION);
      // var_dump($route);
      //var_dump($admin_sisgesp_routes);
      //var_dump($admin_prefeituras_routes);
      // var_dump($permissions);
      // die;

      if (isset($permissions[$route])) {
        $allowed = $permissions[$route];
      } else {
        $allowed = false;
      }


      if (!User::isAuth()) {
        if (!$allowed) {
          $_SESSION['return'] = $route;
          $this->flash->addMessage('errorLogin', "É necessário estar autenticado!");
          return $response->withStatus(302)->withHeader(
            'Location',
            $request->getUri()->getBaseUrl() . '/acesso'
          );
        }
      } else {
        if ((session_id() != $this->userModel->get()->session_id)) {
          User::logout();
          $this->flash->addMessage('errorLogin', "Sua sessão não é mais válida!");
          return $response->withStatus(302)->withHeader(
            'Location',
            $request->getUri()->getBaseUrl() . '/acesso'
          );
        } else {
          if ($route == "p_acesso") {
            return $response->withStatus(302)->withHeader(
              'Location',
              $request->getUri()->getBaseUrl() . '/perfil'
            );
          }
        }
      }
      $response = $next($request, $response);
      return $response;
    }
}
