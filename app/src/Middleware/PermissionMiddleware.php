<?php
declare(strict_types=1);

namespace Farol360\Ancora\Middleware;

use Farol360\Ancora\User;
use Farol360\Ancora\AdminAncora;
use Slim\Views\Twig;

class PermissionMiddleware
{
    private $view;

    public function __construct(Twig $view)
    {
        $this->view = $view;
    }

    /**
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param  callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke($request, $response, $next)
    {
        // unset($_SESSION);
        // var_dump($_SESSION);
        // die;
        $permissions = User::getPermissionsValueByRoleList();

        //remove ':' as twig's variable doesn't accept it
        foreach ($permissions as $key => $value) {
            $key = str_replace(':', '', $key);
            $permissions_new[$key] = $value;
        }

        $this->view->offsetSet('permissions', $permissions_new);

        $permissions_admin_ancora = AdminAncora::getAdminAncoraRoutes();

        foreach ($permissions_admin_ancora as $key => $value) {
            $key = str_replace(':', '', $key);
            $permissions_admin_ancora_new[$key] = $value;
        }

        $this->view->offsetSet('permissions_admin_ancora', $permissions_admin_ancora_new);
        if (isset($_SESSION['admin_ancora'])) {
          $this->view->offsetSet('admin_ancora_loged', $_SESSION['admin_ancora']);
        }

        $response = $next($request, $response);
        return $response;
    }
}
