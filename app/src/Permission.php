<?php
declare(strict_types=1);

namespace Farol360\Ancora;

class Permission
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getAll()
    {
        $sql = "
            SELECT
                resource
            FROM
                permissions
        ";
        $query = $this->db->prepare($sql);
        $query->execute();
        return $query->fetchAll();
    }

    public function getAllRoleList()
    {
        $sql = "
            SELECT
                resource,
                role_list
            FROM
                permissions
        ";
        $query = $this->db->prepare($sql);
        $query->execute();
        return $query->fetchAll();
    }

    public function hasPermission($permission, $roleId)
    {
        $sql = "
            SELECT
                permissions.id
            FROM
                permissions
            WHERE
                permissions.resource = :permission_resource AND
                permissions.role_list LIKE CONCAT('%', :role_id, '%')
        ";
        $query = $this->db->prepare($sql);
        $params = [
            ":permission_resource" => $permission,
            ":role_id" => '"'.$roleId.'"'
        ];
        $query->execute($params);
        if ($query->fetch()) {
            return true;
        }
        return false;
    }
}
