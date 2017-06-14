<?php


namespace www\ui\api\helper;

use Fossology\Lib\Db\ModernDbManager;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Logger;
use Fossology\Lib\Db\Driver\Postgres;

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

  public function doesUploadIdExist($id)
  {
    return (0 < (intval($this->getDbManager()->getSingleRow("SELECT COUNT(*) FROM upload WHERE upload_pk= ".pg_escape_string($id))["count"])));
  }

  public function getAllUsers()
  {
    
  }

}
