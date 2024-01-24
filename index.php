<?php 
session_start();
require_once("vendor/autoload.php");

use \Slim\Slim;

//___________________________________________________________
$app = new Slim();

$app->config('debug', true);

//___________________________________________________________
require_once("site.php");
require_once("admin.php");
require_once("admin-users.php");
require_once("admin-categories.php");
require_once("admin-products.php");


//___________________________________________________________

$app->run();

 ?>