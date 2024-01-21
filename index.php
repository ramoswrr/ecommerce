<?php 
session_start();
require_once("vendor/autoload.php");

use \Slim\Slim;
use \Hcode\Page;
use \Hcode\PageAdmin;
use \Hcode\Model\User;

//___________________________________________________________
$app = new Slim();

$app->config('debug', true);


//_____________________________________ endereço: http://www.hcodecommerce.com.br/admin
$app->get('/admin', function() {

	User::verifyLogin();		//Para verificar se o usuário estpá logado.

	$page = new PageAdmin();
	$page->setTpl("index");		//Chama o "index", que se refere ao arquivo \views\admin\index.html

});

//_____________________________________ endereço: http://www.hcodecommerce.com.br/
$app->get('/', function() {
    
	// $sql = new Hcode\DB\Sql();
	// $results = $sql->select("SELECT * FROM tb_users");
	// echo json_encode($results);

	$page = new Page();
	$page->setTpl("index");		//Chama o "index", que se refere ao arquivo \views\index.html

});


//_____________________________________ endereço: http://www.hcodecommerce.com.br/admin/login
$app->get('/admin/login', function() {

	$page = new PageAdmin([
		"header"=>false,		//Desabilitando o header e footer padrões.
		"footer"=>false
	]);
	$page->setTpl("login");		

});


$app->post('/admin/login', function() {

	User::login($_POST["login"], $_POST["password"]);
	
	header("Location: /admin");
	exit;
});


//_____________________________________ endereço: http://www.hcodecommerce.com.br/admin/logout
$app->get('/admin/logout', function() {

	User::logout();
	header("Location: /admin/login");
	exit;	

});

//___________________________________________________________

$app->run();

 ?>