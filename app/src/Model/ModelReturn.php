<?php
declare(strict_types=1);

namespace Farol360\Ancora\Model;

class ModelReturn
{
    public $data;
    public $status;
    public $table;
    public $function;
    public $errorCode;
    public $errorInfo;

    public function __construct(array $data = [])
    {
        $this->data      = $data['data']      ?? null;
        $this->status    = $data['status']    ?? null;
        $this->table     = $data['table']    ?? null;
        $this->function  = $data['function']    ?? null;
        $this->errorCode = $data['errorCode'] ?? null;
        $this->errorInfo = $data['errorInfo'] ?? null;
    }
}
