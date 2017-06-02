<?php

namespace api\models;


class Error
{

  /**
   * Error constructor.
   * @param $code
   * @param $message
   */
  public function __construct($code, $message)
  {
    $this->code = $code;
    $this->message = $message;
  }

  public function getJSON()
  {
    return json_encode($this);
  }
}
