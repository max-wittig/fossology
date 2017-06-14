<?php
include_once '/usr/local/share/fossology/vendor/autoload.php';
include_once "helper/RestHelper.php";
include_once "../../../delagent/ui/delete-helper.php";
include_once "models/InfoType.php";
include_once "models/Info.php";
include_once "helper/DbHelper.php";
include_once "models/Search.php";
include_once "/usr/local/share/fossology/www/ui/search-helper.php";

//TODO: REMOVE ERROR_DISPLAY
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
use www\ui\api\models\Search;

$app = new Silex\Application();
$app['debug'] = true;

////////////////////////////UPLOADS/////////////////////

$app->GET('/repo/api/v1/organize/uploads/{id}', function (Application $app, Request $request, $id)
{
  $restHelper = new RestHelper();

  if($restHelper->hasUserAccess("SIMPLE_API_KEY"))
  {
    //get the id from the fossology user
    if (is_integer($id))
    {
      return new Response($restHelper->getFolderHelper()->getUploads($restHelper->getUserId(), $id));
    }
    else
    {
      $error = new Info(400, "Bad Request. $id is not a number!", InfoType::ERROR);
      return new Response($error->getJSON());
    }
  }
  else
  {
    $error = new Info(403, "No authorized to GET upload with id " . $id, InfoType::ERROR);
    return new Response($error->getJSON(), $error->getCode());
  }
});

$app->PATCH('/repo/api/v1/organize/uploads/{id}', function (Application $app, Request $request, $id)
{
  $restHelper = new RestHelper();

  if($restHelper->hasUserAccess("SIMPLE_KEY"))
  {
    if (is_integer($id))
    {
      return new Response("TODO");
      //TODO implement patch method
    }
    else
    {
      $error = new Info(400, "Bad Request. $id is not a number!", InfoType::ERROR);
      return new Response($error->getJSON());
    }
  }
  else
  {
    $error = new Info(403, "No authorized to PATCH upload with id " . $id, InfoType::ERROR);
    return new Response($error->getJSON(), $error->getCode());
  }

});

$app->PUT('/repo/api/v1/organize/uploads/', function (Application $app, Request $request)
{

  $restHelper = new RestHelper();

  if($restHelper->hasUserAccess("SIMPLE_KEY"))
  {
    try
    {
      $put = array();
      parse_str(file_get_contents('php://input'), $put);
      return new Response("fdsfds");
    }
    catch (Exception $e)
    {
      $error = new Info(400, "Bad Request. Invalid Input", InfoType::ERROR);
      return new Response($error->getJSON(),$error->getCode());
    }
  }
  else
  {
    $error = new Info(403, "No authorized to PUT upload", InfoType::ERROR);
    return new Response($error->getJSON(), $error->getCode());
  }
});

$app->GET('/repo/api/v1/organize/uploads/', function (Application $app, Request $request)
{
  $restHelper = new RestHelper();

  if($restHelper->hasUserAccess("SIMPLE_KEY"))
  {
    //get the id from the fossology user
    return new Response($restHelper->getFolderHelper()->getUploads($restHelper->getUserId()));
  }
  else
  {
    $error = new Info(403, "No authorized to PUT upload", InfoType::ERROR);
    return new Response($error->getJSON(), $error->getCode());
  }
});

$app->DELETE('/repo/api/v1/organize/uploads/{id}', function (Application $app, Request $request, $id)
{
  $restHelper = new RestHelper();
  $dbHelper = new DbHelper();
  $id = intval($id);
  if($restHelper->hasUserAccess("SIMPLE_KEY"))
  {
    if (is_integer($id))
    {
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
    }
    else
    {
      $error = new Info(400, "Bad Request. $id is not a number!", InfoType::ERROR);
      return new Response($error->getJSON());
    }
  }
  else
  {
    $error = new Info(403, "No authorized to PUT upload", InfoType::ERROR);
    return new Response($error->getJSON(), $error->getCode());
  }
});

////////////////////////////SEARCH/////////////////////

$app->GET('/repo/api/v1/search/', function(Application $app, Request $request)
{
  $limit = $request->headers->get("limit");
  $filename = $request->headers->get("filename");
  $tag = $request->headers->get("tag");
  $filesize_min = $request->headers->get("filesize_min");
  $filesize_max = $request->headers->get("filesize_max");
  $license = $request->headers->get("license");
  $copyright = $request->headers->get("copyright");

  $restHelper = new RestHelper();
  $dbHelper = new DbHelper();

  $item = GetParm("item",PARM_INTEGER);
  $results = GetResults($item, $filename, $tag, NULL, $filesize_min, $filesize_max, NULL,
    $license, $copyright, $restHelper->getUploadDao(), $restHelper->getGroupId(), $dbHelper->getPGCONN());
  return new Response(json_encode($results));
});

$app->run();
