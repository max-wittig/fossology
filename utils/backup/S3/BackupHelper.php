<?php
/*
 * Copyright Siemens AG, 2017
 * SPDX-License-Identifier:   GPL-2.0
 */

class BackupHelper
{
    private $backupDir;
    private $repoFilename = "repo.zip";
    private $databaseFilename = "database.txt";
    private $rootZipFilePath;
    private $rootZipFileName;

    function __construct($backupDir)
    {
        $currentDate = date("d.m.Y_H:i:s");
        $this->rootZipFileName = $currentDate . ".zip";
        //backupDir == where backup folder will be created
        $this->backupDir = $backupDir;
        echoln($backupDir);
        $this->createBackupFolder();
    }

    function createBackupFolder()
    {
        //  backup/21.04.2017/
        $this->rootZipFilePath = $this->backupDir . date("d.m.Y_H:i:s");
        mkdir($this->rootZipFilePath);
    }

    function createRepoZip($sourceFolder)
    {
        $currentFolder = getcwd();
        chdir($sourceFolder);
        echoln("Repo zip name " . $this->rootZipFilePath . "/" . $this->repoFilename);
        echoln("Packing repo. This may take a while...");
        exec("zip -r --display-dots $this->rootZipFilePath/$this->repoFilename *");
        echoln("Zip file: $this->repoFilename created");
        chdir($currentFolder);
    }

    function createDatabaseZip()
    {
        exec("su postgres -c 'pg_dumpall' > $this->rootZipFilePath/$this->databaseFilename");
    }

    function createRootZip()
    {
        chdir($this->rootZipFilePath);
        exec("zip -r --display-dots $this->rootZipFilePath/$this->rootZipFileName *");
    }

    /**
     * @return string
     */
    public function getRootZipFilePath()
    {
        return $this->rootZipFilePath;
    }

    /**
     * @return string
     */
    public function getRootZipFileName()
    {
        return $this->rootZipFileName;
    }
}
