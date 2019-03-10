<?php
declare(strict_types=1);

namespace Farol360\Ancora;

abstract class Model
{
    protected $db;

    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }
}
