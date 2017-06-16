<?php

require_once "/usr/local/share/fossology/lib/php/common-db.php";
require_once "/usr/local/share/fossology/lib/php/common-perms.php";
/**
 * \brief Delete a user.
 *
 * \return NULL on success, string on failure.
 */
function DeleteUser($UserId, $PG_CONN)
{
  /* See if the user already exists */
  $sql = "SELECT * FROM users WHERE user_pk = '$UserId' LIMIT 1;";
  $result = pg_query($PG_CONN, $sql);
  DBCheckResult($result, $sql, __FILE__, __LINE__);
  $row = pg_fetch_assoc($result);
  pg_free_result($result);
  if (empty($row['user_name']))
  {
    $text = _("User does not exist.");
    return($text);
  }

  /* Delete the users group
   * First look up the users group_pk
   */
  $sql = "SELECT group_pk FROM groups WHERE group_name = '$row[user_name]' LIMIT 1;";
  $result = pg_query($PG_CONN, $sql);
  DBCheckResult($result, $sql, __FILE__, __LINE__);
  $GroupRow = pg_fetch_assoc($result);
  pg_free_result($result);

  /* Delete all the group user members for this user_pk */
  $sql = "DELETE FROM group_user_member WHERE user_fk = '$UserId'";
  $result = pg_query($PG_CONN, $sql);
  DBCheckResult($result, $sql, __FILE__, __LINE__);
  pg_free_result($result);

  /* Delete the user */
  $sql = "DELETE FROM users WHERE user_pk = '$UserId';";
  $result = pg_query($PG_CONN, $sql);
  DBCheckResult($result, $sql, __FILE__, __LINE__);
  pg_free_result($result);

  /* Now delete their group */
  DeleteGroup($GroupRow['group_pk'], $PG_CONN);

  /* Make sure it was deleted */
  $sql = "SELECT * FROM users WHERE user_name = '$UserId' LIMIT 1;";
  $result = pg_query($PG_CONN, $sql);
  DBCheckResult($result, $sql, __FILE__, __LINE__);
  $rowCount = pg_num_rows($result);
  pg_free_result($result);
  if ($rowCount != 0)
  {
    $text = _("Failed to delete user.");
    return($text);
  }

  return(NULL);
} // Delete()
