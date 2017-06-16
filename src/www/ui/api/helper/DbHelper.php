<?php


namespace www\ui\api\helper;

include_once "/usr/local/share/fossology/www/ui/api/models/Upload.php";
include_once "/usr/local/share/fossology/www/ui/api/models/User.php";

use api\models\Info;
use Fossology\Lib\Db\ModernDbManager;
use Guzzle\Http\Message\Response;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Logger;
use Fossology\Lib\Db\Driver\Postgres;
use api\models\Upload;
use www\ui\api\models\InfoType;
use www\ui\api\models\User;


class DbHelper
{
  private $dbManager;
  private $PG_CONN;


  /**
   * DbHelper constructor.
   */
  public function __construct()
  {
    $logLevel = Logger::DEBUG;
    $logger = new Logger(__FILE__);
    $logger->pushHandler(new ErrorLogHandler(ErrorLogHandler::OPERATING_SYSTEM, $logLevel));
    $this->dbManager = new ModernDbManager($logger);
    $this->PG_CONN = pg_connect("host=localhost port=5432 dbname=fossology user=fossy password=fossy")
    or die("Could not connect");
    $pgDriver = new Postgres($this->PG_CONN);
    $this->dbManager->setDriver($pgDriver);
  }

  /**
   * @return ModernDbManager
   */
  public function getDbManager()
  {
    return $this->dbManager;
  }

  /**
   * @return resource
   */
  public function getPGCONN()
  {
    return $this->PG_CONN;
  }

  public function getUploads($userId, $uploadId = NULL)
  {
    if($uploadId == NULL)
    {
      $sql = "SELECT DISTINCT upload.upload_pk, upload.upload_ts, upload.upload_filename, upload.upload_desc,folder.folder_pk, folder.folder_name, pfile.pfile_size
FROM upload, folderlist, folder, pfile
  WHERE upload.user_fk=".pg_escape_string($userId)."
  AND folderlist.upload_pk=upload.upload_pk
  AND pfile.pfile_pk=folderlist.pfile_fk
";
    }
    else
    {
      $sql = "SELECT DISTINCT upload.upload_pk, upload.upload_ts, upload.upload_filename, upload.upload_desc,folder.folder_pk, folder.folder_name, pfile.pfile_size
FROM upload, folderlist, folder, pfile
  WHERE upload.user_fk=".pg_escape_string($userId)."
  AND folderlist.upload_pk=upload.upload_pk
  AND folderlist.upload_pk=".pg_escape_string($uploadId)."
  AND pfile.pfile_pk=folderlist.pfile_fk
";
    }

    $result = pg_query($this->getPGCONN(), $sql);
    $uploads = [];
    while ($row = pg_fetch_assoc($result))
    {
      $upload = new Upload($row["folder_pk"],$row["folder_name"], $row["upload_pk"], $row["upload_desc"],
        $row["upload_filename"], $row["upload_ts"],$row["pfile_size"]);
      array_push($uploads, $upload);
    }
    pg_free_result($result);
    return json_encode($uploads, JSON_PRETTY_PRINT);
  }

  public function doesUploadIdExist($id)
  {
    return (0 < (intval($this->getDbManager()->getSingleRow("SELECT COUNT(*) FROM upload WHERE upload_pk= ".pg_escape_string($id))["count"])));
  }

  public function doesUserIdExist($id)
  {
    return (0 < (intval($this->getDbManager()->getSingleRow("SELECT COUNT(*) FROM users WHERE user_pk= ".pg_escape_string($id))["count"])));
  }

  public function getUsers($id = NULL)
  {
    if($id == NULL)
    {
      $usersSQL = "SELECT user_pk, user_name, user_desc, user_email, 
                  email_notify, root_folder_fk, user_perm, user_agent_list FROM users";
    }
    else
    {
      $usersSQL = "SELECT user_pk, user_name, user_desc, user_email, 
                email_notify, root_folder_fk, user_perm, user_agent_list FROM users WHERE user_pk=" . pg_escape_string($id);
    }
    $users = [];
    $result = pg_query($this->getPGCONN(), $usersSQL);
    while ($row = pg_fetch_assoc($result))
    {
      $user = new User($row["user_pk"], $row["user_name"], $row["user_desc"],
        $row["user_email"], $row["user_perm"],
        $row["root_folder_fk"], $row["email_notify"], $row["user_agent_list"]);
      $users[] = $user->getJSON();
    }

    return json_encode($users, JSON_PRETTY_PRINT);
  }

}
