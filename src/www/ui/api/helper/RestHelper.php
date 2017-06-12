<?php

include_once "FolderHelper.php";

use \Fossology\Lib\Dao\UploadPermissionDao;
use \Fossology\Lib\Dao\UploadDao;
use www\ui\api\FolderHelper;
use Monolog\Logger;
use www\ui\api\helper\DbHelper;
use \www\ui\api\models\File;

class RestHelper
{
  private $folderHelper;
  private $logger;
  private $uploadDao;
  private $dbHelper;

  /**
   * RestHelper constructor.
   */
  public function __construct()
  {
    $this->dbHelper = new DbHelper();
    $this->folderHelper = new FolderHelper();
    $this->logger = new Logger(__FILE__);
    $this->uploadPermissionDao = new UploadPermissionDao($this->dbHelper->getDbManager(), $this->logger);
    $this->uploadDao = new UploadDao($this->dbHelper->getDbManager(), $this->logger, $this->uploadPermissionDao);
  }

  public function doesUploadIdExist($id)
  {
    return true;
  }

  public function getUserId()
  {
    return 3;
  }

  public function getGroupId()
  {
    return 3;
  }

  /**
   * This method filters content that starts with ------WebKitFormBoundaryXXXXXXXXX
   * and ends with ------WebKitFormBoundaryXXXXXXXXX---
   * This is required, because the silex framework can't do that natively on put request
   * @param $rawOutput
   * @return string
   */
  public function getFilteredFile($rawOutput)
  {
    $cutString = explode("\n",$rawOutput);
    $webKitBoundaryString = trim($cutString[0]);
    $contentDispositionString = trim($cutString[1]);
    $contentTypeString = trim($cutString[2]);


    $filename = explode("filename=", $contentDispositionString)[1];
    $contentTypeCut = explode("Content-Type:", $contentTypeString)[1];
    $content = $this->getContentBetweenString($rawOutput, 3, $webKitBoundaryString);
    return new File($filename, $contentTypeCut, $content);
  }

  /**
   * @return FolderHelper
   */
  public function getFolderHelper()
  {
    return $this->folderHelper;
  }

  /**
   * @return \Monolog\Logger
   */
  public function getLogger()
  {
    return $this->logger;
  }

  /**
   * @return UploadDao
   */
  public function getUploadDao()
  {
    return $this->uploadDao;
  }

  /**
   * @return UploadPermissionDao
   */

  public function getUploadPermissionDao()
  {
    return $this->uploadPermissionDao;
  }
}
