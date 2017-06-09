<?php
/**
 * Created by IntelliJ IDEA.
 * User: one
 * Date: 6/6/17
 * Time: 2:09 PM
 */

namespace www\ui\api;

require_once '/usr/local/share/fossology/www/ui/api/models/Upload.php';

use api\models\Upload;
use Fossology\Lib\Dao\FolderDao;
use Fossology\Lib\Auth\Auth;
use Fossology\Lib\Dao\ShowJobsDao;
use Fossology\Lib\Dao\UserDao;
use Fossology\Lib\Db\DbManager;
use Fossology\Lib\Db\Driver\Postgres;
use Fossology\Lib\Db\ModernDbManager;
use Fossology\Lib\Db\SolidDbManager;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Logger;

class FolderHelper
{
  private $folderDao;
  private $dbManager;
  private $PG_CONN;

  /**
   * FolderHelper constructor.
   */
  public function __construct()
  {
    $container = $GLOBALS['container'];
    $this->dbManager = $container->get('db.manager');
    $logLevel = Logger::INFO;
    $logger = new Logger(__FILE__);
    $logger->pushHandler(new ErrorLogHandler(ErrorLogHandler::OPERATING_SYSTEM, $logLevel));
    $this->dbManager = new SolidDbManager($logger);
    $this->PG_CONN = pg_connect("host=localhost port=5432 dbname=fossology user=fossy password=fossy")
    or die("Could not connect");
    $pgDriver = new Postgres($this->PG_CONN);
    $this->dbManager->setDriver($pgDriver);
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

    $result = pg_query($this->PG_CONN, $sql);
    //DBCheckResult($result, $sql, __FILE__, __LINE__);
    $uploads = [];
    while ($row = pg_fetch_assoc($result))
    {
      $upload = new Upload($row["folder_pk"],$row["folder_name"], $row["upload_pk"], $row["upload_desc"],
        $row["upload_filename"], $row["upload_ts"],$row["pfile_size"]);
      array_push($uploads, $upload);
    }
    pg_free_result($result);
    return $uploads;
  }

  /**
   * @return FolderDao
   */
  public function getFolderDao()
  {
    return $this->folderDao;
  }

  /**
   * @return SolidDbManager
   */
  public function getDbManager()
  {
    return $this->dbManager;
  }




}
