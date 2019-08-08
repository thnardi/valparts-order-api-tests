<?php
declare(strict_types=1);

namespace Farol360\Ancora\Model;


// Ancora objects
use Farol360\Ancora\Model\AncoraAdmin;
use Farol360\Ancora\Model\eventlogs\EventLogAdminAccess;
use Farol360\Ancora\Model\eventlogs\EventLogAdminAccessType;
use Farol360\Ancora\Model\eventlogs\EventLogAdminAction;
use Farol360\Ancora\Model\eventlogs\EventLogAdminActionType;
use Farol360\Ancora\Model\eventlogs\EventLogUserAccess;
use Farol360\Ancora\Model\eventlogs\EventLogUserAccessType;
use Farol360\Ancora\Model\eventlogs\EventLogUserAction;
use Farol360\Ancora\Model\eventlogs\EventLogUserActionType;
use Farol360\Ancora\Model\Relatorio;
use Farol360\Ancora\Model\Post;
use Farol360\Ancora\Model\Permission;
use Farol360\Ancora\Model\Role;
use Farol360\Ancora\Model\User;
use Farol360\Ancora\Model\UserType;

class EntityFactory
{
    public function createAdminAncora(array $data = []) : AdminAncora
    {
        return new AdminAncora($data);
    }
    public function createClientesAdmin(array $data = []) : ClientesAdmin
    {
        return new ClientesAdmin($data);
    }
    public function createEventLogAdminAccess(array $data = []) : EventLogAdminAccess
    {
        return new EventLogAdminAccess($data);
    }
    public function createEventLogAdminAction(array $data = []) : EventLogAdminAction
    {
        return new EventLogAdminAction($data);
    }
    public function createEventLogUserAccess(array $data = []) : EventLogUserAccess
    {
        return new EventLogUserAccess($data);
    }
    public function createEventLogUserAction(array $data = []) : EventLogUserAction
    {
        return new EventLogUserAction($data);
    }
    public function createPost(array $data = []) : Post
    {
        return new Post($data);
    }

    public function createPostType(array $data = []) : PostType
    {
        return new PostType($data);
    }

    // permission, users and role Classes
    public function createPermission(array $data = []): Permission
    {
        return new Permission($data);
    }

    public function createRelatorio(array $data = []): Relatorio
    {
        return new Relatorio($data);
    }

    public function createRole(array $data = []): Role
    {
        return new Role($data);
    }

    public function createUser(array $data = []): User
    {
        return new User($data);
    }
    public function createUserType(array $data = []): UserType
    {
        return new UserType($data);
    }
}
