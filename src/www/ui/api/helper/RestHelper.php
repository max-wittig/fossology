<?php

include_once "FolderHelper.php";
require_once "../../../delagent/ui/delete-helper.php";

use \Fossology\Lib\Dao\UploadPermissionDao;
use \Fossology\Lib\Dao\UploadDao;
use www\ui\api\FolderHelper;


class RestHelper
{
  private $dbManager;
  private $folderHelper;
  private $logger;
  private $uploadDao;
  private $uploadPermissionDao;


  /**
   * RestHelper constructor.
   */
  public function __construct()
  {
    $this->folderHelper = new FolderHelper();
    $this->logger = new \Monolog\Logger("Default");
    $this->uploadPermissionDao = new UploadPermissionDao($this->dbManager, $this->logger);
    $this->uploadDao = new UploadDao($this->dbManager, $this->logger, $this->uploadPermissionDao);
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
