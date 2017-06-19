<?php
require_once '/usr/local/share/fossology/vendor/autoload.php';
require_once "helper/RestHelper.php";
require_once "models/InfoType.php";
require_once "models/Info.php";
require_once "helper/DbHelper.php";
require_once "/usr/local/share/fossology/www/ui/search-helper.php";
require_once "/usr/local/share/fossology/lib/php/common.php";

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

$app = new Silex\Application();
$app['debug'] = true;

////////////////////////////UPLOADS/////////////////////

$app->GET('/repo/api/v1/organize/uploads/{id}', function (Application $app, Request $request, $id)
{
  $restHelper = new RestHelper();
  $dbHelper = new DbHelper();

  //checks if user has access to this functionality
  if($restHelper->hasUserAccess("SIMPLE_API_KEY"))
  {
    //get the id from the fossology user
    if (is_numeric($id))
    {
      if($dbHelper->doesIdExist("upload","upload_pk", $id))
      {
        return new Response($dbHelper->getUploads($restHelper->getUserId(), $id));
      }
      else
      {
        $error = new Info(404, "File does not exist", InfoType::ERROR);
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
  $dbHelper = new DbHelper();

  if($restHelper->hasUserAccess("SIMPLE_KEY"))
  {
    //get the id from the fossology user
    return new Response($dbHelper->getUploads($restHelper->getUserId()));
  }
  else
  {
    $error = new Info(403, "No authorized to PUT upload", InfoType::ERROR);
    return new Response($error->getJSON(), $error->getCode());
  }
});

$app->DELETE('/repo/api/v1/organize/uploads/{id}', function (Application $app, Request $request, $id)
{
  require_once "../../../delagent/ui/delete-helper.php";
  $restHelper = new RestHelper();
  $dbHelper = new DbHelper();
  $id = intval($id);
  if($restHelper->hasUserAccess("SIMPLE_KEY"))
  {
    if (is_integer($id))
    {
      if($dbHelper->doesIdExist("upload","upload_pk", $id))
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
    $error = new Info(403, "Not authorized to PUT upload", InfoType::ERROR);
    return new Response($error->getJSON(), $error->getCode());
  }
});

////////////////////////////SEARCH/////////////////////

$app->GET('/repo/api/v1/search/', function(Application $app, Request $request)
{
  $restHelper = new RestHelper();

  //check user access to search
  if($restHelper->hasUserAccess("SIMPLE_KEY"))
  {
    $searchType = $request->headers->get("searchType");
    $filename = $request->headers->get("filename");
    $tag = $request->headers->get("tag");
    $filesizeMin = $request->headers->get("filesizemin");
    $filesizeMax = $request->headers->get("filesizemax");
    $license = $request->headers->get("license");
    $copyright = $request->headers->get("copyright");

    //set searchtype to search allfiles by default
    if (!isset($search_type))
    {
      $searchType = "allfiles";
    }

    /**
     * check if at least one parameter was given
     */
    if (!isset($filename) && !isset($tag) && !isset($filesizeMin)
      && !isset($filesizeMax) && !isset($license) && !isset($copyright))
    {
      $error = new Info(400, "Bad Request. At least one parameter is required",
        InfoType::ERROR);
      return new Response($error->getJSON(), $error->getCode());
    }

    /**
     * check if filesizeMin && filesizeMax are numeric, if existing
     */
    if(isset($filesizeMax) || isset($filesizeMin))
    {
      if (!is_numeric($filesizeMin) && !is_numeric($filesizeMax))
      {
        $error = new Info(400, "Bad Request. filesizemin and filesizemax need to be numeric",
          InfoType::ERROR);
        return new Response($error->getJSON(), $error->getCode());
      }
    }

    $restHelper = new RestHelper();
    $dbHelper = new DbHelper();

    $item = GetParm("item", PARM_INTEGER);
    $results = GetResults($item, $filename, $tag, 0, $filesizeMin, $filesizeMax, $searchType,
      $license, $copyright, $restHelper->getUploadDao(), $restHelper->getGroupId(), $dbHelper->getPGCONN());
    return new Response(json_encode($results, JSON_PRETTY_PRINT));
  }
  else
  {
    //401 because every user can search. Only not logged in user can't
    $error = new Info(401, "Not authorized to search", InfoType::ERROR);
    return new Response($error->getJSON(), $error->getCode());
  }
});

////////////////////////////ADMIN-USERS/////////////////////

$app->GET('/repo/api/v1/admin/users/', function(Application $app, Request $request)
{
  $restHelper = new RestHelper();
  $dbHelper = new DbHelper();
  //check user access to search
  if($restHelper->hasUserAccess("SIMPLE_KEY"))
  {
    $users = $dbHelper->getUsers();
    return new Response($users, 200);
  }
  else
  {
    //401 because every user can search. Only not logged in user can't
    $error = new Info(403, "Not authorized to access users", InfoType::ERROR);
    return new Response($error->getJSON(), $error->getCode());
  }


});

$app->GET('/repo/api/v1/admin/users/{id}', function(Application $app, Request $request, $id)
{
  $restHelper = new RestHelper();
  $dbHelper = new DbHelper();
  //check user access to search
  if($restHelper->hasUserAccess("SIMPLE_KEY"))
  {
    if(is_numeric($id))
    {
      if($dbHelper->doesIdExist("users","user_pk", $id))
      {
        $users = $dbHelper->getUsers($id);
        return new Response($users, 200);
      }
      else
      {
        $error = new Info(404, "UserId doesn't exist", InfoType::ERROR);
        return new Response($error->getJSON(), $error->getCode());
      }

    }
    else
    {
      $error = new Info(400, "Bad request. $id is not a number!", InfoType::ERROR);
      return new Response($error->getJSON(), $error->getCode());
    }
  }
  else
  {
    //401 because every user can search. Only not logged in user can't
    $error = new Info(403, "Not authorized to access users", InfoType::ERROR);
    return new Response($error->getJSON(), $error->getCode());
  }


});

$app->DELETE('/repo/api/v1/admin/users/{id}', function(Application $app, Request $request, $id)
{
  $restHelper = new RestHelper();
  $dbHelper = new DbHelper();
  //check user access to search
  if($restHelper->hasUserAccess("SIMPLE_KEY"))
  {
    if(is_numeric($id))
    {
      if($dbHelper->doesIdExist("users","user_pk", $id))
      {
        $dbHelper->deleteUser($id);
        $info = new Info(202, "User will be deleted", InfoType::INFO);
        return new Response($info->getJSON(), $info->getCode());
      }
      else
      {
        $error = new Info(404, "UserId doesn't exist", InfoType::ERROR);
        return new Response($error->getJSON(), $error->getCode());
      }

    }
    else
    {
      $error = new Info(400, "Bad request. $id is not a number!", InfoType::ERROR);
      return new Response($error->getJSON(), $error->getCode());
    }
  }
  else
  {
    //401 because every user can search. Only not logged in user can't
    $error = new Info(403, "Not authorized to access users", InfoType::ERROR);
    return new Response($error->getJSON(), $error->getCode());
  }


});

$app->GET('/repo/api/v1/jobs/', function(Application $app, Request $request)
{
  $dbHelper = new DbHelper();
  $limit = $request->headers->get("limit");
  if(isset($limit) && (!is_numeric($limit) || $limit < 0))
  {
    $error = new Info(400, "Limit cannot be smaller than 1 and has to be numeric!", InfoType::ERROR);
    return new Response($error->getJSON(), $error->getCode());
  }
  return new Response($dbHelper->getJobs($limit), 200);
});

$app->GET('/repo/api/v1/jobs/{id}', function(Application $app, Request $request, $id)
{
  $dbHelper = new DbHelper();
  $limit = $request->headers->get("limit");
  if(isset($limit) && (!is_numeric($limit) || $limit < 0))
  {
    $error = new Info(400, "Limit cannot be smaller than 1 and has to be numeric!", InfoType::ERROR);
    return new Response($error->getJSON(), $error->getCode());
  }

  if(isset($id) && is_numeric($id))
  {
    if($dbHelper->doesIdExist("job", "job_pk", $id))
    {
      return new Response($dbHelper->getJobs($limit, $id), 200);
    }
    else
    {
      $error = new Info(404, "Job id ".$id." doesn't exist", InfoType::ERROR);
      return new Response($error->getJSON(), $error->getCode());
    }
  }
  else
  {
    $error = new Info(400, "Id has to be numeric!", InfoType::ERROR);
    return new Response($error->getJSON(), $error->getCode());
  }
});

$app->run();
