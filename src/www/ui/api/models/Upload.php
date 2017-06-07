<?php


namespace api\models;


class Upload
{

  /**
   * Upload constructor.
   * @param $folderId
   * @param $uploadId
   * @param $description
   * @param $uploadName
   * @param $uploadDate
   * @param $fileSize
   */
  public function __construct($folderId, $folderName, $uploadId, $description, $uploadName, $uploadDate, $fileSize)
  {
    $this->folderId = $folderId;
    $this->folderName = $folderName;
    $this->uploadId = $uploadId;
    $this->description = $description;
    $this->uploadName = $uploadName;
    $this->uploadDate = $uploadDate;
    $this->fileSize = $fileSize;
  }

  public function getJSON()
  {
    return json_encode($this);
  }
}
