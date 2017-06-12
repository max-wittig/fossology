<?php

include_once "FolderHelper.php";

use \Fossology\Lib\Dao\UploadPermissionDao;
use \Fossology\Lib\Dao\UploadDao;
use www\ui\api\FolderHelper;
use Monolog\Logger;
use www\ui\api\helper\DbHelper;

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
