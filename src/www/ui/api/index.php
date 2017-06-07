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
  //var_dump($request->getContent("file"));
  return new Response(json_encode($request->request->all(), JSON_PRETTY_PRINT));

});

$app->POST('/', function (Application $app, Request $request)
{
  //var_dump($request->getContent("file"));
  return new Response(json_encode($request->request->keys(), JSON_PRETTY_PRINT));

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
  $file = $request->files->get('file');
  if ($file == NULL)
  {
    return 'null';
  }
  else
  {
    return 'not null';
  }

  return new Response('How about implementing organizeUploadsIdPut as a PUT method ?');
});


$app->GET('/v1/organize/uploads', function (Application $app, Request $request)
{
  $folderHelper = new FolderHelper();
  //get the id from the fossology user
  return new Response(json_encode($folderHelper->getUploads(getUserId())));
});


$app->DELETE('/v1/organize/uploads/{id}', function (Application $app, Request $request, $id)
{
  include_once "../delagent/ui/admin-upload-delete.php";
  if(doesUploadIdExist($id))
  {
    new admin_upload_delete().Delete($id);
    return new Response('Delete job queued', 202);
  }
  else
  {
    return new Response(new Error("Id doesn't exist", 404).getJSON(), 404);
  }
});


$app->GET('/v1/organize/uploads/{id}', function (Application $app, Request $request, $id)
{


  return new Response('How about implementing organizeUploadsIdGet as a GET method ?');
});


$app->PATCH('/v1/organize/uploads/{id}', function (Application $app, Request $request, $id)
{


  return new Response('How about implementing organizeUploadsIdPatch as a PATCH method ?');
});


$app->PUT('/v1/organize/uploads/{id}', function (Application $app, Request $request, $id)
{


  return new Response('How about implementing organizeUploadsIdPut as a PUT method ?');
});


$app->run();
