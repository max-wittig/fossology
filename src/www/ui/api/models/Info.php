<?php

namespace api\models;


class Info
{
  private $code;
  private $message;
  private $type;
  /**
   * Error constructor.
   * @param $code
   * @param $message
   * @param $type
   */
  public function __construct($code, $message, $type)
  {
    $this->code = $code;
    $this->message = $message;
    $this->type = $type;
  }

  public function getJSON()
  {
    return json_encode(array(
      'code' => $this->code,
      'message' => $this->message,
      'type' => $this->type
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

  /**
   * @return mixed
   */
  public function getType()
  {
    return $this->type;
  }


}
