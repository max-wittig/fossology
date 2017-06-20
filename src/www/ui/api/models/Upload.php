<?php
/***************************************************************
Copyright (C) 2017 Siemens AG

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
 ***************************************************************/

namespace api\models;


class Upload
{

  /**
   * Upload constructor.
   * @param $folderId
   * @param $folderName
   * @param $uploadId
   * @param $description
   * @param $uploadName
   * @param $uploadDate
   * @param $fileSize
   */
  public function __construct($folderId, $folderName, $uploadId, $description, $uploadName, $uploadDate, $fileSize)
  {
    $this->folderId = $folderId;
    $this->folderName = $folderName;
    $this->uploadId = $uploadId;
    $this->description = $description;
    $this->uploadName = $uploadName;
    $this->uploadDate = $uploadDate;
    $this->fileSize = $fileSize;
  }

  /**
   * @return array associative array
   */
  public function getJSON()
  {
    return array(
      'folderId' => $this->folderId,
      'folderName' => $this->folderName,
      'uploadId' => $this->uploadId,
      "description" => $this->description,
      "uploadName" => $this->uploadName,
      "uploadDate" => $this->uploadDate,
      "fileSize" => $this->fileSize
    );
  }
}
