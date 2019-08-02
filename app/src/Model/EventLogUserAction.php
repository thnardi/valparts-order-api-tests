<?php
 namespace Farol360\Ancora\Model;

class EventLogUserAction
{
  public $id;
  public $id_user;
  public $id_event_log_types_user_action;
  public $id_objects;
  public $date;
  public $time;
  public $description;


  public function __construct(array $data = [])
  {
    $this->id                  = $data['id'] ?? null;
    $this->id_user             = $data['id_user'] ?? null;
    $this->id_event_log_types_user_action   = $data['id_event_log_types_user_action'] ?? null;
    $this->id_object           = $data['id_object'] ?? null;
    $this->date                = $data['date'] ?? null;
    $this->time                = $data['time'] ?? null;
    $this->description         = $data['description'] ?? null;
  }
}
