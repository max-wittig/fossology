<?php
include_once '/usr/local/share/fossology/vendor/autoload.php';
include_once "helper/RestHelper.php";
include_once "../../../delagent/ui/delete-helper.php";

use Symfony\Component\HttpKernel\Debug\ErrorHandler;
use Symfony\Component\HttpKernel\Debug\ExceptionHandler;
ini_set('display_errors', 1);
error_reporting(-1);
ErrorHandler::register();


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Silex\Application;
use api\models\Error;


$app = new Silex\Application();
$app['debug'] = true;

$_SESSION['UserLevel'] = "admin";

$app->PUT('/', function (Application $app, Request $request)
{
  return new Response($request->getContent());
});

$app->DELETE('/', function (Application $app, Request $request)
{
  $upload_pk = 8;
  $restHelper = new RestHelper();

  if($restHelper->doesUploadIdExist($upload_pk))
  {
    define("PLUGIN_DB_ADMIN", 0);
    TryToDelete($upload_pk, $restHelper->getUserId(), $restHelper->getGroupId(), $restHelper->getUploadDao());

    return new Response('Delete job queued.', 202);
  }
  else
  {
    $error = new Error("Id doesn't exist", 404);
    return new Response($error->getJSON(), $error->getCode());
  }
});

$app->GET('/', function (Application $app, Request $request)
{
  //get the id from the fossology user
  $restHelper = new RestHelper();
  return new Response(json_encode($restHelper->getFolderHelper()->getUploads($restHelper->getUserId()), JSON_PRETTY_PRINT));
});

$app->GET('/v1/organize/uploads/{id}', function (Application $app, Request $request, $id)
{
  $restHelper = new RestHelper();
  //get the id from the fossology user
  return new Response(json_encode($restHelper->getFolderHelper()->getUploads($restHelper->getUserId(), $id), JSON_PRETTY_PRINT));
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
  $restHelper = new RestHelper();
  //get the id from the fossology user
  return new Response(json_encode($restHelper->getFolderHelper()->getUploads($restHelper->getUserId())));
});


$app->DELETE('/v1/organize/uploads/{id}', function (Application $app, Request $request, $id)
{
  $restHelper = new RestHelper();
  if($restHelper->doesUploadIdExist($id))
  {
    TryToDelete($id, $restHelper->getUserId(), $restHelper->getGroupId(), $restHelper->getUploadDao());
    return new Response('Delete job queued', 202);
  }
  else
  {
    $error = new Error("Id doesn't exist", 404);
    return new Response($error->getJSON(), $error->getCode());
  }
});

$app->run();
