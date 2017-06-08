<?php
require_once '/usr/local/share/fossology/vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Silex\Application;
include_once "helper/rest-helper.php";
include_once "models/Error.php";
include_once "helper/FolderHelper.php";
include_once "/usr/local/share/fossology/lib/php/common-container.php";
use www\ui\api\FolderHelper;
use api\models\Error;

$app = new Silex\Application();
$app['debug'] = true;

$app->PUT('/', function (Application $app, Request $request)
{
  return new Response($request->getContent());
});

$app->DELETE('/', function (Application $app, Request $request)
{
  require_once "../../../delagent/ui/delete_helper.php";
  $upload_pk = 13;

  if(doesUploadIdExist($upload_pk))
  {
    TryToDelete($upload_pk, getUserId(), getGroupId());
    return new Response('Delete job queued', 202);
  }
  else
  {
    $error = new Error("Id doesn't exist", 404);
    return new Response($error->getJSON(), $error->getCode());
  }
});

$app->GET('/', function (Application $app, Request $request)
{
  $folderHelper = new FolderHelper();
  //get the id from the fossology user
  return new Response(json_encode($folderHelper->getUploads(getUserId(), 13), JSON_PRETTY_PRINT));

});

$app->GET('/v1/organize/uploads/{id}', function (Application $app, Request $request, $id)
{
  $folderHelper = new FolderHelper();
  //get the id from the fossology user
  return new Response(json_encode($folderHelper->getUploads(getUserId(), $id), JSON_PRETTY_PRINT));
});


$app->PATCH('/v1/organize/uploads/{id}', function (Application $app, Request $request, $id)
{
  return new Response('How about implementing organizeUploadsIdPatch as a PATCH method ?');
});


$app->PUT('/v1/organize/uploads/', function (Application $app, Request $request)
{

});


$app->GET('/v1/organize/uploads', function (Application $app, Request $request)
{
  $folderHelper = new FolderHelper();
  //get the id from the fossology user
  return new Response(json_encode($folderHelper->getUploads(getUserId())));
});


$app->DELETE('/v1/organize/uploads/{id}', function (Application $app, Request $request, $id)
{
  require_once "../../../delagent/ui/delete-helper.php";

  if(doesUploadIdExist($id))
  {
    TryToDelete($id, getUserId(), getGroupId());
    return new Response('Delete job queued', 202);
  }
  else
  {
    $error = new Error("Id doesn't exist", 404);
    return new Response($error->getJSON(), $error->getCode());
  }
});

$app->run();
