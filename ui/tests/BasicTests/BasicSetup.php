<?php
/***********************************************************
 Copyright (C) 2011 Hewlett-Packard Development Company, L.P.

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
 ***********************************************************/

/**
 * Create a folder for use by the Basic tests
 *
 * @version "$Id$"
 *
 * Created on Oct. 15, 2008
 */

// old requires when run by testFOSSology.php
//require_once('../../../tests/fossologyTestCase.php');
//require_once ('../../../tests/TestEnvironment.php');

require_once('../../tests/fossologyTestCase.php');
require_once ('../../tests/TestEnvironment.php');

//require_once ('../../../tests/fossologyTest.php');

/* every test must use these globals, at least $URL */
global $URL;

class BasicFolderTest extends fossologyTestCase
{
  public $folder_name;
  public $mybrowser;

  function setUp()
  {
    global $URL;
    $this->Login();
  }

  function testBasicFolder()
  {
    global $URL;

    print "starting BasicFoldertest\n";
    $this->createFolder(null, 'Basic-Testing', null);
  }
}
?>
