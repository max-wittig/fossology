<?php
/***************************************************************
Copyright (C) 2017 Siemens AG

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
version 2 as published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License along
with this program; if not, write to the Free Software Foundation, Inc.,
51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 ***************************************************************/

require_once "StringHelper.php";
require_once '/usr/local/share/fossology/www/ui/api/models/File.php';

use \Fossology\Lib\Dao\UploadPermissionDao;
use \Fossology\Lib\Dao\UploadDao;
use Monolog\Logger;
use www\ui\api\helper\DbHelper;
use \www\ui\api\models\File;
use \www\ui\api\helper\StringHelper;

class RestHelper
{
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
    $this->stringHelper = new StringHelper();
    $this->logger = new Logger(__FILE__);
    $this->uploadPermissionDao = new UploadPermissionDao($this->dbHelper->getDbManager(), $this->logger);
    $this->uploadDao = new UploadDao($this->dbHelper->getDbManager(), $this->logger, $this->uploadPermissionDao);
  }

  public function hasUserAccess($apiKey, $context = "default")
  {
    $this->authorizeUser("fossy", "fossy");
    if($context == "admin")
    {
      //do something else
    }
    return true;
  }

  public function authorizeUser($username, $password)
  {
    //TODO provide OAuth to user
    $_SESSION['UserLevel'] = $username;
    define("PLUGIN_DB_ADMIN",0);
    $_SESSION['api_key'] = "SIMPLE_API_KEY";
    $GLOBALS['SysConf']['auth'] = 3;
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
