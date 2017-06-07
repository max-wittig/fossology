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
    $PG_CONN = pg_connect("host=localhost port=5432 dbname=fossology user=fossy password=fossy")
    or die("Could not connect");
    $pgDriver = new Postgres($PG_CONN);
    $this->dbManager->setDriver($pgDriver);

    //$this->folderDao = $container->get('dao.folder');
  }

  public function getUploads($id = NULL)
  {
    if($id == NULL)
    {
      $res = $this->dbManager->getSingleRow("SELECT array_agg(ufile_name) FROM uploadtree");
    }
    else
    {
      $res = $this->dbManager->getSingleRow("SELECT array_agg(ufile_name) FROM uploadtree WHERE upload_fk=$1",array($id));
    }
    return $res["array_agg"];
  }

  /**
   * @return FolderDao
   */
  public function getFolderDao()
  {
    return $this->folderDao;
  }


}
