<?php
 namespace Farol360\Ancora\Model;

class EventLogAdminAccess
{
    public $id;
    public $id_event_log_types_admin_access;
    public $description;
    public $created_at;
    public $updated_at;

    public function __construct(array $data = [])
    {
      $this->id                  = $data['id'] ?? null;
      $this->id_event_log_types_admin_access   = $data['id_event_log_types_admin_access'] ?? null;
      $this->description        = $data['description'] ?? null;
      $this->created_at         = $data['created_at'] ?? null;
      $this->updated_at         = $data['updated_at'] ?? null;
    }
}
