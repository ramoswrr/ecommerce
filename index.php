<?php 

require_once("vendor/autoload.php");

use \Slim\Slim;
use \Hcode\Page;

//___________________________________________________________
$app = new Slim();

$app->config('debug', true);

//_____________________________________ endereço: http://www.hcodecommerce.com.br/
$app->get('/', function() {
    
	// $sql = new Hcode\DB\Sql();
	// $results = $sql->select("SELECT * FROM tb_users");
	// echo json_encode($results);

	$page = new Page();
	$page->setTpl("index");		//Chama o "index", que se refere ao arquivo \views\index.html

});

//___________________________________________________________

$app->run();

 ?>