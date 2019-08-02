<?php
 namespace Farol360\Ancora\Model;

class EventLogAdminAccess
{
    public $id;
    public $id_event_log_types_admin_access;
    public $date;
    public $time;
    public $description;


    public function __construct(array $data = [])
    {
      $this->id                  = $data['id'] ?? null;
      $this->id_event_log_types_admin_access   = $data['id_event_log_types_admin_access'] ?? null;
      $this->date                = $data['date'] ?? null;
      $this->time                = $data['time'] ?? null;
      $this->description         = $data['description'] ?? null;
    }
}
