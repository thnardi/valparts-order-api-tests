<?php
 namespace Farol360\Ancora\Model\eventlogs;

class EventLogUserAction
{
  public $id;
  public $id_user;
  public $id_event_log_types_user_action;
  public $id_objects;
  public $description;
  public $created_at;
  public $updated_at;

  public function __construct(array $data = [])
  {
    $this->id                  = $data['id'] ?? null;
    $this->id_user             = $data['id_user'] ?? null;
    $this->id_event_log_types_user_action   = $data['id_event_log_types_user_action'] ?? null;
    $this->id_object           = $data['id_object'] ?? null;
    $this->description         = $data['description'] ?? null;
    $this->created_at         = $data['created_at'] ?? null;
    $this->updated_at         = $data['updated_at'] ?? null;

  }
}
