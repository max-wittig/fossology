<?php


namespace www\ui\api\models;


class File
{
  private $filename;
  private $contentType;
  private $fileContent;

  /**
   * File constructor.
   * @param $filename
   * @param $contentType
   * @param $fileContent
   */
  public function __construct($filename, $contentType, $fileContent)
  {
    $this->filename = $filename;
    $this->contentType = $contentType;
    $this->fileContent = $fileContent;
  }

  /**
   * @return string
   */
  public function getFilename()
  {
    return $this->filename;
  }

  /**
   * @return string
   */
  public function getContentType()
  {
    return $this->contentType;
  }

  /**
   * @return string
   */
  public function getFileContent()
  {
    return $this->fileContent;
  }




}
