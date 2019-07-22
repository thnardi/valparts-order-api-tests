<?php
declare(strict_types=1);

namespace Farol360\Ancora\Model;

class ModelException extends \Exception
{
  private $_data = '';
  private $_message = '';

  public function __construct($data, $message)
  {
    $this->_data = $data;
    $this->_message = $message;
    parent::__construct($message);
  }

  public function getData()
  {
    return $this->_data;
  }

  // public function getMessage()
  // {
  //   return $this->_message;
  // }
}
