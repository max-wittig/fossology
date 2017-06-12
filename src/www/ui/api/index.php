<?php
include_once '/usr/local/share/fossology/vendor/autoload.php';
include_once "helper/RestHelper.php";
include_once "../../../delagent/ui/delete-helper.php";
include_once "models/InfoType.php";
include_once "models/Info.php";

use Symfony\Component\HttpKernel\Debug\ErrorHandler;
ini_set('display_errors', 1);
error_reporting(-1);
ErrorHandler::register();


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Silex\Application;
use api\models\Info;
use \www\ui\api\models\InfoType;


$app = new Silex\Application();
$app['debug'] = true;

$_SESSION['UserLevel'] = "admin";

$app->PUT('/', function (Application $app, Request $request)
{
  $restHelper = new RestHelper();
  $filteredFile = $restHelper->getFilteredFile($request->getContent());
  return new Response($filteredFile->getJSON());
});

$app->DELETE('/', function (Application $app, Request $request)
{
  $restHelper = new RestHelper();
  $id = 8;
  if($restHelper->doesUploadIdExist($id))
  {
    define("PLUGIN_DB_ADMIN", 0);
    TryToDelete($id, $restHelper->getUserId(), $restHelper->getGroupId(), $restHelper->getUploadDao());
    $info = new Info(202, "Delete Job for file with id ". $id,InfoType::INFO);
    return new Response($info->getJSON(), $info->getCode());
  }
  else
  {
    $error = new Info("Id doesn't exist", 404, InfoType::ERROR);
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
    $error = new Info("Id doesn't exist", 404, InfoType::ERROR);
    return new Response($error->getJSON(), $error->getCode());
  }
});

$app->run();
