<?php
/*
  Copyright (C) 2014-2016 Siemens AG

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
*/

use Fossology\Lib\Auth\Auth;
use Fossology\Lib\Dao\FolderDao;
use Fossology\Lib\Dao\UploadDao;
use Fossology\Lib\Data\Upload\Upload;
use Fossology\Lib\Plugin\DefaultPlugin;
use Symfony\Component\HttpFoundation\Request;

class SpdxTwoImportPlugin extends DefaultPlugin
{
  const NAME = 'ui_spdx2Import';

  /** @var UploadDao */
  private $uploadDao;
  /** @var FolderDao */
  private $folderDao;

  function __construct()
  {
    parent::__construct(self::NAME, array(
      self::TITLE => _("SPDX2 Import"),
      self::PERMISSION => Auth::PERM_WRITE,
      self::REQUIRES_LOGIN => TRUE
    ));
    $this->uploadDao = $GLOBALS['container']->get('dao.upload');
    $this->folderDao = $GLOBALS['container']->get('dao.folder');
  }

  function preInstall()
  {
    $text = _("Import SPDX2 RDF");
    menu_insert("Browse-Pfile::Import&nbsp;SPDX2&nbsp;RDF", 0, self::NAME, $text);
  }

  protected function handle(Request $request)
  {
    $spdxReport = intval($request->get('spdxReport'));
    $uploadId = intval(GetArrayVal("upload_pk", $_POST));
    if (empty($uploadId) ||
        !array_key_exists('spdxReport',$_FILES) ||
        sizeof($_FILES['spdxReport']['name']) != 1)
    {
      return $this->showUiToChoose($uploadId);
    }
    else
    {
      $jobMetaData = $this->runImport($uploadId, $_FILES['spdxReport']);
      $showJobsPlugin = \plugin_find('showjobs');
      $showJobsPlugin->OutputOpen();
      return $showJobsPlugin->getResponse();
    }
  }

  protected function showUiToChoose($uploadId)
  {
    $vars=array();
    $groupId = Auth::getGroupId();

    $rootFolder = $this->folderDao->getRootFolder(Auth::getUserId());
    $folder_pk = GetParm('folder', PARM_INTEGER);
    if (empty($folder_pk)) {
      $folder_pk = $rootFolder->getId();
    }
    $vars['folderId'] = $folder_pk;

    $folderUploads = $this->folderDao->getFolderUploads($folder_pk, $groupId);
    $uploadsById = array();
    /* @var $uploadProgress UploadProgress */
    foreach ($folderUploads as $uploadProgress)
    {
      if ($uploadProgress->getGroupId() != $groupId) {
        continue;
      }
      if (!$this->uploadDao->isEditable($uploadProgress->getId(), $groupId)) {
        continue;
      }
      $display = $uploadProgress->getFilename() . _(" from ") . date("Y-m-d H:i",$uploadProgress->getTimestamp());
      $uploadsById[$uploadProgress->getId()] = $display;
    }
    $vars['uploadList'] = $uploadsById;

    if (empty($uploadId)) {
      $uploadId = GetParm('upload', PARM_INTEGER);
    }
    if (empty($uploadId))
    {
      reset($uploadsById);
      $uploadId = key($uploadsById);
    }
    $vars['uploadId'] = $uploadId;

    $folderStructure = $this->folderDao->getFolderStructure($rootFolder->getId());
    $vars['folderStructure'] = $folderStructure;
    $vars['baseUri'] = $Uri = Traceback_uri() . "?mod=" . self::NAME . "&folder=";

    $baseFolderUri = $vars['baseUri']."$folder_pk&upload=";
    $vars['uploadAction'] = "onchange=\"js_url(this.value, '$baseFolderUri')\"";

    return $this->render('SpdxTwoImportPlugin.html.twig', $this->mergeWithDefault($vars));
  }

  protected function runImport($uploadId, $spdxReport)
  {
    $spdx2ImportAgent = plugin_find('agent_spdx2Import');
    $jqCmdArgs = $spdx2ImportAgent->addSpdxReport($spdxReport);

    $userId = Auth::getUserId();
    $groupId = Auth::getGroupId();
    $dbManager = $this->getObject('db.manager');
    $sql = 'SELECT jq_pk,job_pk FROM jobqueue, job '
         . 'WHERE jq_job_fk=job_pk AND jq_type=$1 AND job_group_fk=$4 AND job_user_fk=$3 AND jq_args=$2 AND jq_endtime IS NULL';
    $params = array($spdx2ImportAgent->AgentName,$uploadId,$userId,$groupId);
    $log = __METHOD__;
    if ($jqCmdArgs) {
      $sql .= ' AND jq_cmd_args=$5';
      $params[] = $jqCmdArgs;
      $log .= '.args';
    }
    else {
      $sql .= ' AND jq_cmd_args IS NULL';
    }

    $scheduled = $dbManager->getSingleRow($sql,$params,$log);
    if (!empty($scheduled)) {
      return array($scheduled['job_pk'],$scheduled['jq_pk']);
    }

    $upload = $this->getUpload($uploadId, $groupId);
    $jobId = JobAddJob($userId, $groupId, $upload->getFilename(), $uploadId);
    $error = "";
    $jobQueueId = $spdx2ImportAgent->AgentAdd($jobId, $uploadId, $error, array(), $jqCmdArgs);
    if ($jobQueueId<0)
    {
      throw new Exception(_("Cannot schedule").": ".$error);
    }
    return array($jobId,$jobQueueId);
  }

  protected function getUpload($uploadId, $groupId)
  {
    if ($uploadId <=0)
    {
      throw new Exception(_("parameter error: $uploadId"));
    }
    if (!$this->uploadDao->isAccessible($uploadId, $groupId))
    {
      throw new Exception(_("permission denied"));
    }
    /** @var Upload */
    $upload = $this->uploadDao->getUpload($uploadId);
    if ($upload === null)
    {
      throw new Exception(_('cannot find uploadId'));
    }
    return $upload;
  }
}

register_plugin(new SpdxTwoImportPlugin());
