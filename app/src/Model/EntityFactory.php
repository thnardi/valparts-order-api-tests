<?php
declare(strict_types=1);

namespace Farol360\Ancora\Model;

// business objects
use Farol360\Ancora\Model\AncoraAdmin;
use Farol360\Ancora\Model\Post;
// Ancora objects
use Farol360\Ancora\Model\Permission;
use Farol360\Ancora\Model\Role;
use Farol360\Ancora\Model\User;

class EntityFactory
{
    public function createAdminAncora(array $data = []) : AdminAncora
    {
        return new AdminAncora($data);
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

    public function createRole(array $data = []): Role
    {
        return new Role($data);
    }

    public function createUser(array $data = []): User
    {
        return new User($data);
    }
}
