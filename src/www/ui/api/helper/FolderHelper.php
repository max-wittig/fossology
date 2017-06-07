<?php
/**
 * Created by IntelliJ IDEA.
 * User: one
 * Date: 6/6/17
 * Time: 2:09 PM
 */

namespace www\ui\api;


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
      $sql = "SELECT upload_pk, upload_ts, upload_filename, upload_desc FROM upload WHERE user_fk=".pg_escape_string($userId);
    }
    else
    {
      $sql = "SELECT upload_pk, upload_ts,upload_filename, upload_desc FROM upload WHERE upload_pk=".pg_escape_string($uploadId) . "AND user_fk=".pg_escape_string($userId);
    }

    $result = pg_query($this->PG_CONN, $sql);
    //DBCheckResult($result, $sql, __FILE__, __LINE__);
    $uploads = [];
    while ($row = pg_fetch_assoc($result))
    {
      array_push($uploads, $row);
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


}
