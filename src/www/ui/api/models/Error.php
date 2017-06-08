<?php

namespace api\models;


class Error
{
  private $code;
  private $message;
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
    return json_encode(array(
      'code' => $this->code,
      'message' => $this->message
    ));
  }

  /**
   * @return mixed
   */
  public function getCode()
  {
    return $this->code;
  }

  /**
   * @return mixed
   */
  public function getMessage()
  {
    return $this->message;
  }


}
