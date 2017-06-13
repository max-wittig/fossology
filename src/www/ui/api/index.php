<?php
include_once '/usr/local/share/fossology/vendor/autoload.php';
include_once "helper/RestHelper.php";
include_once "../../../delagent/ui/delete-helper.php";
include_once "models/InfoType.php";
include_once "models/Info.php";
include_once "helper/DbHelper.php";

use Symfony\Component\HttpKernel\Debug\ErrorHandler;
ini_set('display_errors', 1);
error_reporting(-1);
ErrorHandler::register();


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Silex\Application;
use api\models\Info;
use \www\ui\api\models\InfoType;
use \www\ui\api\helper\DbHelper;

$app = new Silex\Application();
$app['debug'] = true;
$_SESSION['UserLevel'] = "fossy";

$app->GET('/repo/api/v1/organize/uploads/{id}', function (Application $app, Request $request, $id)
{
  $restHelper = new RestHelper();
  //get the id from the fossology user
  if(is_integer($id))
  {
    return new Response(json_encode($restHelper->getFolderHelper()->getUploads($restHelper->getUserId(), $id), JSON_PRETTY_PRINT));
  }
  else
  {
    $error = new Info(400, "Bad Request. $id is not a number!", InfoType::ERROR);
    return new Response($error->getJSON());
  }

});

$app->PATCH('/repo/api/v1/organize/uploads/{id}', function (Application $app, Request $request, $id)
{
  return new Response('How about implementing organizeUploadsIdPatch as a PATCH method ?');
});

$app->PUT('/repo/api/v1/organize/uploads/', function (Application $app, Request $request)
{
  $restHelper = new RestHelper();
  $filteredFile = $restHelper->getFilteredFile($request->getContent());
  return new Response($filteredFile->getJSON());
});

$app->GET('/repo/api/v1/organize/uploads', function (Application $app, Request $request)
{
  $restHelper = new RestHelper();
  //get the id from the fossology user
  return new Response(json_encode($restHelper->getFolderHelper()->getUploads($restHelper->getUserId())));
});


$app->DELETE('/repo/api/v1/organize/uploads/{id}', function (Application $app, Request $request, $id)
{
  $restHelper = new RestHelper();
  $dbHelper = new DbHelper();

  if ($dbHelper->doesUploadIdExist($id))
  {
    TryToDelete($id, $restHelper->getUserId(), $restHelper->getGroupId(), $restHelper->getUploadDao());
    $info = new Info(202, "Delete Job for file with id " . $id, InfoType::INFO);
    return new Response($info->getJSON(), $info->getCode());
  }
  else
  {
    $error = new Info(404, "Id " . $id . " doesn't exist", InfoType::ERROR);
    return new Response($error->getJSON(), $error->getCode());
  }

});

$app->run();
