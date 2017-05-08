#!/usr/bin/php
<?php
/*
 * Copyright Siemens AG, 2017
 * SPDX-License-Identifier:   GPL-2.0
 */
require "BackupHelper.php";
require '../../../src/vendor/autoload.php';
use Ulrichsg\Getopt\Getopt;
use Ulrichsg\Getopt\Option;

$databaseName = "database.txt";
$repoName = "repo.zip";
$destinationDir = "/srv/fossology/";

if (posix_getuid() != 0)
    exit("This script can only be run as root\n");

function printHelp()
{
    echo <<<EOL
-----------------------------------------------
fo-restore-s3.php
Required Parameter:
-u --username Aws Username
-p --password Aws Password
-b --bucket Aws Bucket
-f --filename -> filename of the backup in the S3 amazon cloud

Optional Parameter
-e S3 Endpoint -> e.g http://localhost:4572 for local testing of S3
-----------------------------------------------
EOL;
}

//chdir doesn't work, because of missing sudo permissions
function changeDirRoot($dir)
{
    shell_exec("cd $dir");
}

function echoln($s="")
{
    echo $s."\n";
}

function controlFossology($state)
{
    shell_exec("/etc/init.d/fossology $state");
    echoln("$state fossology");
}

function restoreRepo($repoName)
{
    exec("unzip $repoName");
    unlink($repoName);
}

function restoreDatabase($filename)
{
    exec("service postgresql restart");
    exec("su postgres -c 'dropdb fossology'");
    exec("su postgres -c 'psql -f $filename'");
    unlink($filename);
}

function cleanFossologyRepo()
{
    exec("rm -rf /srv/fossology/repository/");
    exec("/usr/local/lib/fossology/fo-postinstall");
}

$getopt = new Getopt((array(
    new Option('u', 'username', GetOpt::OPTIONAL_ARGUMENT),
    new Option('p', 'password', Getopt::OPTIONAL_ARGUMENT),
    new Option('b', 'bucket', GetOpt::OPTIONAL_ARGUMENT),
    new Option('f', 'filename', Getopt::OPTIONAL_ARGUMENT),
    new Option('e', 'endpoint', Getopt::OPTIONAL_ARGUMENT),
    new Option('C', 'cleanup', Getopt::NO_ARGUMENT)
)));

$getopt->parse();

$awsUser = $getopt->getOption("username");
$awsPass = $getopt->getOption("password");
$bucket = $getopt->getOption("bucket");
$backupFilename = $getopt->getOption("filename");
$awsEndpoint = $getopt->getOption("endpoint");
$doCleanup = ($getopt->getOption("cleanup") == 1);

//Required means something else to the library author. Needs to be detected manually
//https://github.com/ulrichsg/getopt-php/issues/36
if(empty($awsUser) || $awsUser == 1){exit("Username is required\n");}
if(empty($awsPass) || $awsPass == 1){exit("Password is required\n");}
if(empty($bucket) || $bucket == 1){exit("Bucket is required\n");}
if(empty($backupFilename) || $backupFilename == 1){exit("Backup filename is required\n");}

$s3 = new Aws\S3\S3Client([
    'version' => 'latest',
    'region'  => 'eu-central-1',
    'endpoint' => $awsEndpoint,
    'credentials' => [
        'key'    => $awsUser,
        'secret' => $awsPass
    ]
]);

cleanFossologyRepo();
controlFossology("stop");
echoln("Downloading ".$backupFilename." to ".$destinationDir);
$result = $s3->getObject([
    'Bucket' => $bucket,
    'Key'    => $backupFilename,
    'SaveAs' => $destinationDir . $backupFilename
]);

$fullPath = $destinationDir . $backupFilename;
if(!file_exists($fullPath))
    exit("Can't restore. File doesn't exist.");

exec("unzip " . $fullPath);

restoreRepo($repoName);
restoreDatabase($databaseName);
unlink($fullPath);
controlFossology("start");
echoln("Fossology successfully restored");
