<?php
/***********************************************************
 * Copyright (C) 2008-2013 Hewlett-Packard Development Company, L.P.
 * Copyright (C) 2014-2017 Siemens AG
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 ***********************************************************/

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Fossology\Lib\Plugin\AgentPlugin;
use Fossology\Lib\UI\MenuHook;
/**
 * @param $request Request
 * @param $adj2nestplugin Adj2nestAgentPlugin
 * @param $projectGroup
 * @param $SYSCONFDIR
 * @param $MODDIR
 * @param $originalFileName
 * @param $publicPermission
 * @param $description
 * @param $uploadedFile UploadedFile
 * @param $folderId
 * @param $userId
 * @param $groupId
 * @param $uploadMode
 * @return array
 */
function uploadFile($parmAgentList, $plainAgentList, $adj2nestplugin, $request, $projectGroup, $SYSCONFDIR, $MODDIR, $uploadMode, $uploadedFile,
                    $originalFileName, $description, $publicPermission, $folderId,
                    $userId, $groupId)
{

  $uploadId = JobAddUpload($userId, $groupId, $originalFileName, $originalFileName,
    $description, $uploadMode, $folderId, $publicPermission);
  if (empty($uploadId))
  {
    return array(false, _("Failed to insert upload record"), $description);
  }

  try
  {
    $uploadedTempFile = $uploadedFile->move($uploadedFile->getPath(),
      $uploadedFile->getFilename() . '-uploaded')->getPathname();
  } catch (FileException $e)
  {
    return array(false, _("Could not save uploaded file"), $description);
  }

  $wgetAgentCall = "$MODDIR/wget_agent/agent/wget_agent -C -g $projectGroup -k $uploadId '$uploadedTempFile' -c '$SYSCONFDIR'";
  $wgetOutput = array();
  exec($wgetAgentCall, $wgetOutput, $wgetReturnValue);
  unlink($uploadedTempFile);

  if ($wgetReturnValue != 0)
  {
    $message = implode(' ', $wgetOutput);
    if (empty($message))
    {
      $message = _("File upload failed.  Error:") . $wgetReturnValue;
    }
    return array(false, $message, $description);
  }

  $message = $this->postUploadAddJobs($parmAgentList, $plainAgentList,$request,$adj2nestplugin, $originalFileName, $uploadId);

  return array(true, $message, $description);
}

function postUploadAddJobs($parmAgentList, $plainAgentList, Request $request,
                           $adj2nestplugin, $userId, $groupId, $fileName, $uploadId,
                           $jobId = null, $wgetDependency = false)
{
  if ($jobId === null) {
    $jobId = JobAddJob($userId, $groupId, $fileName, $uploadId);
  }
  $dummy = "";
  $adj2nestDependencies = array();
  if ($wgetDependency)
  {
    $adj2nestDependencies = array(array('name'=>'agent_unpack',AgentPlugin::PRE_JOB_QUEUE=>array('wget_agent')));
  }

  $adj2nestplugin->AgentAdd($jobId, $uploadId, $dummy, $adj2nestDependencies);

  $checkedAgents = checkedAgents();
  AgentSchedule($jobId, $uploadId, $checkedAgents);

  $errorMsg = '';

  $agentList = array_merge($plainAgentList, $parmAgentList);
  foreach($parmAgentList as $parmAgent) {
    $agent = plugin_find($parmAgent);
    $agent->scheduleAgent($jobId, $uploadId, $errorMsg, $request, $agentList);
  }

  $status = GetRunnableJobList();
  $message = empty($status) ? _("Is the scheduler running? ") : "";
  $jobUrl = Traceback_uri() . "?mod=showjobs&upload=$uploadId";
  $message .= _("The file") . " " . $fileName . " " . _("has been uploaded. It is") . ' <a href=' . $jobUrl . '>upload #' . $uploadId . "</a>.\n";
  if ($request->get('public')==self::PUBLIC_GROUPS)
  {
    $this->getObject('dao.upload.permission')->makeAccessibleToAllGroupsOf($uploadId, $userId);
  }
  return $message;
}
