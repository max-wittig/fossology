<?php


namespace api\models;


class Upload
{

  /**
   * Upload constructor.
   * @param $folderId
   * @param $folderName
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

  /**
   * @return string UploadJSON
   */
  public function getJSON()
  {
    return json_encode(array(
      'folderId' => $this->folderId,
      'folderName' => $this->folderName,
      'uploadId' => $this->uploadId,
      "description" => $this->description,
      "uploadName" => $this->uploadName,
      "uploadDate" => $this->uploadDate,
      "fileSize" => $this->fileSize
    ));
  }
}
