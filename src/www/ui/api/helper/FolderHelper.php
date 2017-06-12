<?php

namespace www\ui\api;

require_once '/usr/local/share/fossology/www/ui/api/models/Upload.php';
require_once 'DbHelper.php';

use api\models\Upload;
use Fossology\Lib\Dao\FolderDao;
use www\ui\api\helper\DbHelper;

class FolderHelper
{
  private $folderDao;
  private $dbHelper;

  /**
   * FolderHelper constructor.
   */
  public function __construct()
  {
    $this->dbHelper = new DbHelper();
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

    $result = pg_query($this->dbHelper->getPGCONN(), $sql);
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
}
