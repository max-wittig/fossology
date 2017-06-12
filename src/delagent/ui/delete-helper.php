<?php

include_once "/usr/local/share/fossology/lib/php/common.php";

/**
 * \brief Given a folder_pk, try to add a job after checking permissions.
 * @param $uploadpk - the upload(upload_id) you want to delete
 * @param $user_pk - the user_id
 * @param $group_pk - the group_id
 * @param $uploadDao - an instance of a uploadDao
 * @return string with the message.
 */
function TryToDelete($uploadpk, $user_pk, $group_pk, $uploadDao) {
  if(!$uploadDao->isEditable($uploadpk, $group_pk)){
    $text=_("You dont have permissions to delete the upload");
    return DisplayMessage($text);
  }

  $rc = Delete($uploadpk, $user_pk, $group_pk, NULL);

  if (! empty($rc)) {
    $text=_("Deletion Scheduling failed: ");
    return DisplayMessage($text.$rc);
  }

  /* Need to refresh the screen */
  $URL = Traceback_uri() . "?mod=showjobs&upload=$uploadpk ";
  $LinkText = _("View Jobs");
  $text=_("Deletion added to job queue.");
  $msg = "$text <a href=$URL>$LinkText</a>";
  return displayMessage($msg);
}

/**
 * \brief Given a folder_pk, add a job.
 * @param $uploadpk - the upload(upload_id) you want to delete
 * @param $Depends - Depends is not used for now
 * @param $user_pk - Id of a user
 * @param $group_pk - Id of the group
 * @param $dbManager - an instance of the db manager
 * @return NULL on success, string on failure.
 */
function Delete($uploadpk, $user_pk, $group_pk, $Depends = NULL)
{
  /* Prepare the job: job "Delete" */
  $jobpk = JobAddJob($user_pk, $group_pk, "Delete", $uploadpk);
  if (empty($jobpk) || ($jobpk < 0)) {
    $text = _("Failed to create job record");
    return ($text);
  }
  /* Add job: job "Delete" has jobqueue item "delagent" */
  $jqargs = "DELETE UPLOAD $uploadpk";
  $jobqueuepk = JobQueueAdd($jobpk, "delagent", $jqargs, NULL, NULL);
  if (empty($jobqueuepk)) {
    $text = _("Failed to place delete in job queue");
    return ($text);
  }

  /* Tell the scheduler to check the queue. */
  $success  = fo_communicate_with_scheduler("database", $output, $error_msg);
  if (!$success)
  {
    $error_msg = _("Is the scheduler running? Your jobs have been added to job queue.");
    $URL = Traceback_uri() . "?mod=showjobs&upload=$uploadpk ";
    $LinkText = _("View Jobs");
    $msg = "$error_msg <a href=$URL>$LinkText</a>";
    return $msg;
  }
  return (NULL);
} // Delete()
