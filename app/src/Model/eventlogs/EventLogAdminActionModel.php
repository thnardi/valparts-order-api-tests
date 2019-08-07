<?php
declare(strict_types=1);

namespace Farol360\Ancora\Model\eventlogs;

use Farol360\Ancora\Model;
use Farol360\Ancora\Model\ModelReturn;
use Farol360\Ancora\Model\eventlogs\EventLogAdminAction;

class EventLogAdminActionModel extends Model
{
  public function add(EventLogAdminAction $eventLog)
  {
    $sql =
      "INSERT INTO event_logs_admin_actions (
        id_event_log_type,
        id_admin_ancora,
        id_object,
        description
      )
      VALUES (
        :id_event_log_type,
        :id_admin_ancora,
        :id_object,
        :description
      )";
    $parameters = [
        ':id_event_log_type'    => $eventLog->id_event_log_types_admin_action,
        ':id_admin_ancora'      => $eventLog->id_admin_ancora,
        ':id_object'            => $eventLog->id_object,
        ':description'          => $eventLog->description
    ];
    $stmt = $this->db->prepare($sql);
    $exec = $stmt->execute($parameters);
    // verifica se ocorreu com sucesso o execute
    if ($exec) {
      $data['data'] = $this->db->lastInsertId();
      $data['errorCode'] = null;
      $data['errorInfo'] = null;
    } else {
      $data['data'] = false;
      $data['errorCode'] = $stmt->errorCode();
      $data['errorInfo'] = $stmt->errorInfo();
    }
    // completa demais dados
    $data['status'] = $exec;
    $data['table'] = 'event_logs_admin_action';
    $data['function'] = 'add';
    $modelReturn = new ModelReturn($data);
    return $modelReturn;
  }

  public function get(int $id)
  {
    $sql =
      "SELECT
          *
      FROM
          event_logs_admin_action
      WHERE
          id = :id
      LIMIT 1
    ";
    $parameters = [':id' => $id];
    $stmt = $this->db->prepare($sql);
    $exec = $stmt->execute($parameters);
    // verifica se ocorreu com sucesso o execute
    if ($exec) {
      $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, EventLogAdminAction::class);
      $data['data'] = $stmt->fetch();
      $data['errorCode'] = null;
      $data['errorInfo'] = null;
    } else {
      $data['data'] = false;
      $data['errorCode'] = $stmt->errorCode();
      $data['errorInfo'] = $stmt->errorInfo();
    }
    // completa demais dados
    $data['status'] = $exec;
    $data['table'] = 'event_logs_admin_action';
    $data['function'] = 'get';
    $modelReturn = new ModelReturn($data);
    return $modelReturn;
  }

  public function getAmount($filtro)
  {
      $sql =
        "SELECT
              COUNT(id) AS amount
          FROM
            event_logs_admin_actions ";
      if ($filtro == 2) {
        $sql .= "WHERE id_event_log_type = 1 ";
      }
      if ($filtro == 3) {
        $sql .= "WHERE id_event_log_type = 2 ";
      }
      $query = $this->db->prepare($sql);
      $query->execute();
      return $query->fetch();
  }

  public function getAll($order, $filtro, int $offset = 0, int $limit = PHP_INT_MAX)
  {
    $sql =
      "SELECT
        event_logs_admin_actions.*,
        admin_ancora.name as admin_name,
        admin_ancora.slug as admin_slug
      FROM
        event_logs_admin_actions
        LEFT JOIN admin_ancora ON admin_ancora.id = event_logs_admin_actions.id_admin_ancora ";
    if ($filtro == 1) {

    }
    if ($filtro == 2) {
      $sql .= "WHERE event_logs_admin_actions.id_event_log_type = 1 ";
    }
    if ($filtro == 3) {
      $sql .= "WHERE event_logs_admin_actions.id_event_log_type = 2 ";
    }
    if ($order == 1) {
      $sql .= "ORDER BY created_at DESC ";
    }
    if ($order == 2) {
      $sql .= "ORDER BY created_at ASC ";
    }
    $sql .= "LIMIT ? , ?";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(1, $offset, \PDO::PARAM_INT);
    $stmt->bindValue(2, $limit, \PDO::PARAM_INT);
    $exec = $stmt->execute();
    // verifica se ocorreu com sucesso o execute
    if ($exec) {
      $stmt->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, EventLogAdminAccess::class);
      $data['data'] = $stmt->fetchAll();
      $data['errorCode'] = null;
      $data['errorInfo'] = null;
    } else {
      $data['data'] = false;
      $data['errorCode'] = $stmt->errorCode();
      $data['errorInfo'] = $stmt->errorInfo();
    }
    // completa demais dados
    $data['status'] = $exec;
    $data['table'] = 'event_logs_admin_actions';
    $data['function'] = 'getAll';
    $modelReturn = new ModelReturn($data);
    return $modelReturn;
  }
}
