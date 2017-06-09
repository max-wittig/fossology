<?php

include_once "FolderHelper.php";

use \Fossology\Lib\Dao\UploadPermissionDao;
use \Fossology\Lib\Dao\UploadDao;
use www\ui\api\FolderHelper;
use Monolog\Logger;

class RestHelper
{
  private $dbManager;
  private $folderHelper;
  private $logger;
  private $uploadDao;
  //private $uploadPermissionDao;

  /**
   * RestHelper constructor.
   */
  public function __construct()
  {
    $this->folderHelper = new FolderHelper();
    $this->logger = new Logger("Test");
    $this->uploadPermissionDao = new UploadPermissionDao($this->folderHelper->getDbManager(), $this->logger);
    $this->uploadDao = new UploadDao($this->folderHelper->getDbManager(), $this->logger, $this->uploadPermissionDao);
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
   * @return mixed
   */
  public function getDbManager()
  {
    return $this->dbManager;
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
