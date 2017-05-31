<?php
require_once(dirname(dirname(dirname(__FILE__))) . "/vendor/autoload.php");

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Silex\Application;

$app = new Silex\Application();


$app->DELETE('/v1/organize/uploads/{id}', function(Application $app, Request $request, $id) {
            
            
            return new Response('How about implementing organizeUploadsIdDelete as a DELETE method ?');
            });


$app->GET('/v1/organize/uploads/{id}', function(Application $app, Request $request, $id) {
            
            
            return new Response('How about implementing organizeUploadsIdGet as a GET method ?');
            });


$app->PATCH('/v1/organize/uploads/{id}', function(Application $app, Request $request, $id) {
            
            
            return new Response('How about implementing organizeUploadsIdPatch as a PATCH method ?');
            });


$app->PUT('/v1/organize/uploads/{id}', function(Application $app, Request $request, $id) {
            
            
            return new Response('How about implementing organizeUploadsIdPut as a PUT method ?');
            });


$app->GET('/v1/organize/uploads', function(Application $app, Request $request) {
            
            
            return new Response('How about implementing organizeUploadsGet as a GET method ?');
            });


$app->DELETE('/v1/organize/uploads/{id}', function(Application $app, Request $request, $id) {
            
            
            return new Response('How about implementing organizeUploadsIdDelete as a DELETE method ?');
            });


$app->GET('/v1/organize/uploads/{id}', function(Application $app, Request $request, $id) {
            
            
            return new Response('How about implementing organizeUploadsIdGet as a GET method ?');
            });


$app->PATCH('/v1/organize/uploads/{id}', function(Application $app, Request $request, $id) {
            
            
            return new Response('How about implementing organizeUploadsIdPatch as a PATCH method ?');
            });


$app->PUT('/v1/organize/uploads/{id}', function(Application $app, Request $request, $id) {
            
            
            return new Response('How about implementing organizeUploadsIdPut as a PUT method ?');
            });


$app->run();
