<?php
/**
 * Created by IntelliJ IDEA.
 * User: one
 * Date: 6/16/17
 * Time: 10:31 AM
 */

namespace www\ui\api\models;


class User
{
  private $id;
  private $name;
  private $description;
  private $email;
  private $access;
  private $root_folder_id;
  private $email_notification;
  private $default_agents;

  /**
   * User constructor.
   * @param $id
   * @param $name
   * @param $description
   * @param $email
   * @param $access
   * @param $root_folder_id
   * @param $email_notification
   * @param $default_agents
   */
  public function __construct($id, $name, $description, $email, $access, $root_folder_id, $email_notification, $default_agents)
  {
    $this->id = $id;
    $this->name = $name;
    $this->description = $description;
    $this->email = $email;
    $this->access = $access;
    $this->root_folder_id = $root_folder_id;
    $this->email_notification = $email_notification;
    $this->default_agents = $default_agents;
  }


}
