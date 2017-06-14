<?php

include_once "FolderHelper.php";
include_once "StringHelper.php";
include_once '/usr/local/share/fossology/www/ui/api/models/File.php';

use \Fossology\Lib\Dao\UploadPermissionDao;
use \Fossology\Lib\Dao\UploadDao;
use www\ui\api\FolderHelper;
use Monolog\Logger;
use www\ui\api\helper\DbHelper;
use \www\ui\api\models\File;
use \www\ui\api\helper\StringHelper;

class RestHelper
{
  private $folderHelper;
  private $stringHelper;
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
    $this->stringHelper = new StringHelper();
    $this->logger = new Logger(__FILE__);
    $this->uploadPermissionDao = new UploadPermissionDao($this->dbHelper->getDbManager(), $this->logger);
    $this->uploadDao = new UploadDao($this->dbHelper->getDbManager(), $this->logger, $this->uploadPermissionDao);
  }

  public function hasUserAccess($apiKey)
  {
    $this->authorizeUser("fossy", "fossy");
    return true;
  }

  public function authorizeUser($username, $password)
  {
    //TODO provide OAuth to user
    $_SESSION['UserLevel'] = $username;
    define("PLUGIN_DB_ADMIN",0);
    $_SESSION['api_key'] = "SIMPLE_API_KEY";
  }

  public function getUserId()
  {
    //TODO provide some way for the user to authorize!
    //Currently user fossy is logged in id=3
    return 3;
  }

  public function getGroupId()
  {
    //TODO provide some way for the user to authorize!
    //Currently user fossy is logged in id=3
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
    $webKitBoundaryString = trim(str_replace("-", "",$cutString[0]));
    $contentDispositionString = trim(str_replace("-", "",$cutString[1]));
    $contentTypeString = trim($cutString[2]);

    $filename = explode("filename=", str_replace("\"", "",$contentDispositionString))[1];
    $contentTypeCut = explode("Content-Type:", $contentTypeString)[1];
    $content = $this->stringHelper->getContentBetweenString($rawOutput, array(0,1,2,3), $webKitBoundaryString);
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
