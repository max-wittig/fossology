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
  private $queueDate;
  private $uploadId;

  /**
   * Job constructor.
   * @param $id integer
   * @param $name string
   * @param $queueDate string
   * @param $uploadId integer
   * @param $userId integer
   * @param $groupId integer
   */
  public function __construct($id, $name, $queueDate, $uploadId, $userId, $groupId)
  {
    $this->id = $id;
    $this->name = $name;
    $this->queueDate = $queueDate;
    $this->uploadId = $uploadId;
    $this->userId = $userId;
    $this->groupId = $groupId;
  }

  /**
   * @return array
   */
  public function getJSON()
  {
    return array(
      'id' => $this->id,
      'name' => $this->name,
      'queueDate' => $this->queueDate,
      'uploadId' => $this->uploadId,
      'userId' => $this->userId,
      'groupId' => $this->groupId
    );
  }

}
