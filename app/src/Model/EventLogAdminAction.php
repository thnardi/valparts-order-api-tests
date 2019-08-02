<?php
 namespace Farol360\Ancora\Model;

class EventLogAdminAction
{
  public $id;
  public $id_admin_ancora;
  public $id_event_log_types_admin_action;
  public $id_objects;
  public $date;
  public $time;
  public $description;


  public function __construct(array $data = [])
  {
    $this->id                  = $data['id'] ?? null;
    $this->id_admin_ancora     = $data['id_admin_ancora'] ?? null;
    $this->id_event_log_types_admin_action   = $data['id_event_log_types_admin_action'] ?? null;
    $this->id_object           = $data['id_object'] ?? null;
    $this->date                = $data['date'] ?? null;
    $this->time                = $data['time'] ?? null;
    $this->description         = $data['description'] ?? null;
  }
}
