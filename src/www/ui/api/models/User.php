<?php

namespace www\ui\api\models;


class User
{
  private $id;
  private $name;
  private $description;
  private $email;
  private $accessLevel;
  private $rootFolderId;
  private $emailNotification;
  private $agents;

  /**
   * User constructor.
   * @param $id integer
   * @param $name string
   * @param $description string
   * @param $email string
   * @param $accessLevel AccessLevel
   * @param $root_folder_id integer
   * @param $emailNotification boolean
   * @param $agents object
   */
  public function __construct($id, $name, $description, $email, $accessLevel, $root_folder_id, $emailNotification, $agents)
  {
    $this->id = $id;
    $this->name = $name;
    $this->description = $description;
    $this->email = $email;
    $this->accessLevel = $accessLevel;
    $this->rootFolderId = $root_folder_id;
    $this->emailNotification = $emailNotification;
    $this->agents = $agents;
  }

  /**
   * @return integer
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }

  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }

  /**
   * @return string
   */
  public function getEmail()
  {
    return $this->email;
  }

  /**
   * @return AccessLevel
   */
  public function getAccessLevel()
  {
    return $this->accessLevel;
  }

  /**
   * @return integer
   */
  public function getRootFolderId()
  {
    return $this->rootFolderId;
  }

  /**
   * @return boolean
   */
  public function getEmailNotification()
  {
    return $this->emailNotification;
  }

  /**
   * @return object
   */
  public function getAgents()
  {
    return $this->agents;
  }

  /**
   * @return array
   */
  public function getJSON()
  {
    return array(
      'userId' => $this->id,
      'description' => $this->description,
      'email' => $this->email,
      "accessLevel" => $this->accessLevel,
      "rootFolderId" => $this->rootFolderId,
      "emailNotification" => $this->emailNotification,
      "agents" => $this->agents
    );
  }




}
