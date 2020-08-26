<?php
declare(strict_types=1);

namespace Farol360\Ancora\Model;

// business objects
use Farol360\Ancora\Model\Order;

class EntityFactory
{
  public function createOrder(array $data = []) : Order
  {
    return new Order($data);
  }

  
}
