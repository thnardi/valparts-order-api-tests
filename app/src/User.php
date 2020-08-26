<?php
declare(strict_types=1);

namespace Farol360\Ancora;

// use Farol360\Ancora\Mailer;
// use Farol360\Ancora\Model\UserModel;
use Psr\Container\ContainerInterface as Container;

class User
{
//   protected static $db;
//   protected static $userModel;

//   public static function getPermissionsValue()
//   {
//       $return = [];
//       $permissionClass = new Permission(self::$db);
//       $permissions = $permissionClass->getAll();

//       foreach ($permissions as $permission) {
//           $return[
//               'p_' .
//               str_replace('/', '', substr($permission->resource, 0, 1)) .
//               str_replace('/', '_', substr(
//                   $permission->resource,
//                   1,
//                   strlen($permission->resource) - 2
//               )) .
//               str_replace('/', '', substr(
//                   $permission->resource,
//                   strlen($permission->resource) - 1,
//                   1
//               ))] = self::hasPermission($permission->resource);
//       }

//       return $return;
//   }

//   public static function getPermissionsValueByRoleList()
//   {
//       $return = [];
//       $permissionClass = new Permission(self::$db);
//       $permissions = $permissionClass->getAllRoleList();
//       foreach ($permissions as $permission) {
//           $return[
//               'p_' .
//               str_replace('/', '', substr($permission->resource, 0, 1)) .
//               str_replace('/', '_', substr(
//                   $permission->resource,
//                   1,
//                   strlen($permission->resource) - 2
//               )) .
//               str_replace('/', '', substr(
//                   $permission->resource,
//                   strlen($permission->resource) - 1,
//                   1
//               ))] = self::hasPermissionByRoleList($permission->resource);
//       }

//       return $return;
//   }

//   public static function getEmail()
//   {
//       if (self::isAuth()) {
//           return $_SESSION["user"]["email"];
//       }
//       return '';
//   }

//   public static function getName()
//   {
//       if (self::isAuth()) {
//           return $_SESSION["user"]["name"];
//       }
//       return '';
//   }

//   public static function getUserRole()
//   {
//       if (!isset($_SESSION["user"])) {
//           $sql = "
//               SELECT
//                   id
//               FROM
//                   roles
//               WHERE
//                   roles.name = 'guest'
//           ";
//           $query = self::$db->prepare($sql);
//           $query->execute();
//           return $query->fetch()->id;
//       }
//       $sql = "
//           SELECT
//               role_id
//           FROM
//               users
//           WHERE
//               users.id = :id
//       ";
//       $query = self::$db->prepare($sql);
//       $query->execute([':id' => $_SESSION["user"]["id"]]);
//       return $query->fetch()->role_id;
//   }

//   public static function hasPermission($permission)
//   {
//       if (!$permission) {
//           return false;
//       }
//       $role = new Role(self::$db);
//       return $role->hasPermission($permission, self::getUserRole());
//   }

//   public static function hasPermissionByRoleList($permission)
//   {
//       if (!$permission) {
//           return false;
//       }

//       $permissionClass = new Permission(self::$db);
//       return $permissionClass->hasPermission($permission, self::getUserRole());
//   }

//   public static function setupUser(Container $container)
//   {
//       self::$db = $container->db;
//       self::$userModel = new UserModel(self::$db, $container->get('settings')['api']['baseUrl']);
//   }
}
