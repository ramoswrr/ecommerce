<?php 

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

//_____________________________________ endereço: http://www.hcodecommerce.com.br/admin/login
$app->post('/admin/login', function() {

	User::login($_POST["login"], $_POST["password"]);
	
	header("Location: /admin");
	exit;
});



//___________________________________________________________

$app->run();

 ?>