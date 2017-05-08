#!/usr/bin/php
<?php
/*
 * Copyright Siemens AG, 2017
 * SPDX-License-Identifier:   GPL-2.0
 */

require '../../../src/vendor/autoload.php';
require "BackupHelper.php";
use Ulrichsg\Getopt\Getopt;
use Ulrichsg\Getopt\Option;

$sourceDir = "/srv/fossology/";

if (posix_getuid() != 0)
    exit("This script can only be run as root\n");

function echoln($s="")
{
    echo $s."\n";
}

function controlFossology($state)
{
    shell_exec("/etc/init.d/fossology $state");
    echoln("$state fossology");
}

function printHelp()
{
    echo <<<EOL
-----------------------------------------------
fo-backup-s3.php
Required Parameter:
-u --username Aws Username
-p --password Aws Password
-d --dir Backupdir -> directory where to create backup files
-b --bucket Bucketname -> Name of the Amazon S3 Bucket

Optional Parameter
-e S3 Endpoint -> e.g http://localhost:4572 for local testing of S3
-----------------------------------------------

EOL;
}

function createBucket($s3, $bucket)
{
    echoln("Creating bucket named {$bucket}");
    $s3->createBucket(['Bucket' => $bucket]);
    $s3->waitUntil('BucketExists', ['Bucket' => $bucket]);
}

$getopt = new Getopt((array(
    new Option('u', 'username', GetOpt::OPTIONAL_ARGUMENT),
    new Option('p', 'password', GetOpt::OPTIONAL_ARGUMENT),
    new Option('b', 'bucket', GetOpt::OPTIONAL_ARGUMENT),
    new Option('d', 'dir', GetOpt::OPTIONAL_ARGUMENT),
    new Option('e', 'endpoint', GetOpt::OPTIONAL_ARGUMENT),
    new Option('h', 'help', Getopt::NO_ARGUMENT),
)));

$getopt->parse();
$help = $getopt->getOption("help");
$awsUser = $getopt->getOption("username");
$awsPass = $getopt->getOption("password");
$backupDir = $getopt->getOption("dir");
$bucket = $getopt->getOption("bucket");
$awsEndpoint = $getopt->getOption("endpoint");

if(!empty($help) || empty($getopt->getOptions()))
{
    printHelp();
    exit(0);
}

//Required means something else to the library author. Needs to be detected manually
//https://github.com/ulrichsg/getopt-php/issues/36
if(empty($awsUser) || $awsUser == 1){exit("Username is required\n");}
if(empty($awsPass) || $awsPass == 1){exit("Password is required\n");}
if(empty($bucket) || $bucket == 1){exit("Bucket is required\n");}
if(empty($backupDir) || $backupDir == 1){exit("Backup directory is required\n");}

$s3 = new Aws\S3\S3Client([
    'version' => 'latest',
    'region'  => 'eu-central-1',
    'endpoint' => $awsEndpoint,
    'credentials' => [
        'key'    => $awsUser,
        'secret' => $awsPass
    ]
]);

controlFossology("stop");
$backupHelper = new BackupHelper($backupDir);
$backupHelper->createRepoZip($sourceDir);
$backupHelper->createDatabaseZip();
$backupHelper->createRootZip();

createBucket($s3, $bucket);
$rootZipFullPath = $backupHelper->getRootZipFilePath()."/".$backupHelper->getRootZipFileName();

echoln("Creating a new object with key {$rootZipFullPath}");

try
{
    $s3->putObject([
        'Bucket' => $bucket,
        'Key' => $backupHelper->getRootZipFileName(),
        'SourceFile' => $rootZipFullPath,
        'ServerSideEncryption' => 'AES256'
    ]);
}
catch (\Aws\S3\Exception\S3Exception $e)
{
    echo $e;
}

echoln("Uploaded " . $backupHelper->getRootZipFileName() . " into bucket " . $bucket);
controlFossology("start");
