<?php

namespace www\ui\api\models;

/**
 * Class Job
 * @package www\ui\api\models
 */
class Job
{
  private $id;
  private $name;
  private $status;

  /**
   * Job constructor.
   * @param $id
   * @param $name
   * @param $status
   */
  public function __construct($id, $name, $status)
  {
    $this->id = $id;
    $this->name = $name;
    $this->status = $status;
  }

  /**
   * @return string
   */
  public function getJSON()
  {
    return json_encode(array(
      'id' => $this->id,
      'name' => $this->name,
      'status' => $this->status
    ));
  }

}
