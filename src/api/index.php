<?php
require_once __DIR__ . '../../../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Silex\Application;
include_once "helper/helper.php";
include_once "models/Error.php";
$app = new Silex\Application();
use api\models\Error;


$app->GET('/v1/organize/uploads/{id}', function (Application $app, Request $request, $id)
{


  return new Response('How about implementing organizeUploadsIdGet as a GET method ?');
});


$app->PATCH('/v1/organize/uploads/{id}', function (Application $app, Request $request, $id)
{


  return new Response('How about implementing organizeUploadsIdPatch as a PATCH method ?');
});


$app->PUT('/v1/organize/uploads/', function (Application $app, Request $request, $id)
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


  return new Response('How about implementing organizeUploadsGet as a GET method ?');
});


$app->DELETE('/v1/organize/uploads/{id}', function (Application $app, Request $request, $id)
{
  include_once "../delagent/ui/admin-upload-delete.php";
  if(doesUploadIdExist($id))
  {
    new admin_upload_delete.Delete($id);
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
