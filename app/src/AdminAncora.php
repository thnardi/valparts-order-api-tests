<?php
declare(strict_types=1);

namespace Farol360\Ancora;

use Farol360\Ancora\Mailer;
use Farol360\Ancora\Model\AdminAncoraModel;
//use Farol360\Ancora\Model\PrefeituraModel;
use Psr\Container\ContainerInterface as Container;

class AdminAncora {

  protected static $db;
  protected static $adminAncoraModel;

  public static function getAdminAncoraRoutes()
  {
    $return = [];
    $permissionClass = new Permission(self::$db);
    $permissions = $permissionClass->getAllAdminAncora();

    foreach ($permissions as $permission) {
      $return[
        'p_' .
        str_replace('/', '', substr($permission->resource, 0, 1)) .
        str_replace('/', '_', substr(
          $permission->resource,
          1,
          strlen($permission->resource) - 2
        )) .
        str_replace('/', '', substr(
          $permission->resource,
          strlen($permission->resource) - 1,
          1
        ))] = self::hasPermissionAdminAncora($permission->resource);
    }

    return $return;
  }

  public static function getName()
    {
        if (self::isAuth()) {
            return $_SESSION["admin_ancora"]["slug"];
        }
        return '';
    }

  // return true or false
  public static function hasPermissionAdminAncora($permission)
  {
    if (!$permission) {
      return false;
    }


    $permissionClass = new Permission(self::$db);

    $role = self::getUserRole();

    $return = $permissionClass->hasAdminAncoraPermission($permission, $role);

    return $return;
  }


  // return integer type from admin_sisgesp table
  public static function getUserRole()
  {
    if (!isset($_SESSION["admin_ancora"])) {

      return 0;
    }
    $sql = "
      SELECT
        type
      FROM
        admin_ancora
      WHERE
        admin_ancora.id = :id
    ";
    $query = self::$db->prepare($sql);
    $query->execute([':id' => $_SESSION["admin_ancora"]["id"]]);
    $return = $query->fetch()->type;
    return (int) $return;
  }

  public static function isAuth()
  {
    if (isset($_SESSION["admin_ancora"])) {
      return true;
    }
    return false;
  }

  public static function loginSlug($slug, $password)
    {
        self::logout();
        $admin_ancora = self::$adminAncoraModel->getSlug(null, $slug);


        if (!empty($admin_ancora) && (hash_equals($admin_ancora->password, crypt($password, $admin_ancora->password)))) {
            $_SESSION["admin_ancora"] = (array) $admin_ancora;

            $sessionId = session_id();
            self::setSessionId($sessionId);
            $_SESSION["admin_ancora"]["session_id"] = $sessionId;

            return true;
        }
        return false;
    }

    public static function logout()
    {
        unset($_SESSION["admin_ancora"]);
    }

    public static function setSessionId($sessionId)
    {
        // var_dump(self::$adminSisgespModel->get());
        // die;
        $admin_ancora_id = self::$adminAncoraModel->get()->id;

        $sql = "
            UPDATE
                admin_ancora
            SET
                session = :session_id
            WHERE
                id = :id
        ";
        $query = self::$db->prepare($sql);
        $parameters = [
            ':session_id' => $sessionId,
            ':id' => $admin_ancora_id
        ];
        return $query->execute($parameters);
    }

  public static function setupUser(Container $container)
  {
    self::$db = $container->db;
    self::$adminAncoraModel = new AdminAncoraModel(self::$db);
  }


}
