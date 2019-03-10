<?php
declare(strict_types=1);

namespace Farol360\Ancora\Twig;

use Twig_Extension;
use Twig_Function;

class AuthExtension extends Twig_Extension
{
    public function getFunctions()
    {
        return [
            new Twig_Function(
                'is_auth',
                'Farol360\Ancora\User::isAuth'
            ),
            new Twig_Function(
                'get_email',
                'Farol360\Ancora\User::getEmail'
            ),
            new Twig_Function(
                'get_name',
                'Farol360\Ancora\User::getName'
            ),
        ];
    }
}
