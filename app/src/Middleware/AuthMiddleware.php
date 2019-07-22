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
        
        /* 
        *   vars
        */

        // route mean the actual route, the one who is accessed at the time this function is called.
        $route = $request->getUri()->getPath();
        
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
        //var_dump($allowed);
        //die;

        if (!User::isAuth()) {
            if (!$allowed) {
                $_SESSION['return'] = $route;
                $this->flash->addMessage('errorLogin', "É necessário estar autenticado!");
                return $response->withStatus(302)->withHeader(
                    'Location',
                    $request->getUri()->getBaseUrl() . '/users/signin'
                );
            }
        } else {
            if ((session_id() != $this->userModel->get()->session)) {
                User::logout();
                $this->flash->addMessage('errorLogin', "Sua sessão não é mais válida!");
                return $response->withStatus(302)->withHeader(
                    'Location',
                    $request->getUri()->getBaseUrl() . '/users/signin'
                );
            } else {
                if ($route != "p_users_signin") {
                    if (!$allowed) {
                        return $response->withStatus(403);
                    }
                } else {
                    return $response->withStatus(302)->withHeader(
                        'Location',
                        $request->getUri()->getBaseUrl()
                    );
                }
            }
        }

        $response = $next($request, $response);
        return $response;
    }
}
