<?php 

use \Hcode\PageAdmin;
use \Hcode\Model\User;


//_____________________________________ endereço: http://www.hcodecommerce.com.br/admin
$app->get('/admin', function() {

	User::verifyLogin();		//Para verificar se o usuário estpá logado.

	$page = new PageAdmin();
	
	$page->setTpl("index");		//Chama o "index", que se refere ao arquivo \views\admin\index.html

});


//_____________________________________ Se acessar via get, a resposta será com um html.
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


//_____________________________________
$app->get('/admin/logout', function() {

	User::logout();
	header("Location: /admin/login");
	exit;	

});


//_____________________________________ endereço: http://www.hcodecommerce.com.br/admin/forgot
$app->get("/admin/forgot", function() {

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot");	

});


$app->post("/admin/forgot", function(){

	$user = User::getForgot($_POST["email"]);

	header("Location: /admin/forgot/sent");
	exit;

});


$app->get("/admin/forgot/sent", function(){

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot-sent");	

});


$app->get("/admin/forgot/reset", function(){

	$user = User::validForgotDecrypt($_GET["code"]);

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot-reset", array(
		"name"=>$user["desperson"],
		"code"=>$_GET["code"]
	));

});


$app->post("/admin/forgot/reset", function(){

	$forgot = User::validForgotDecrypt($_POST["code"]);	

	User::setFogotUsed($forgot["idrecovery"]);

	$user = new User();

	$user->get((int)$forgot["iduser"]);

	//$password = User::getPasswordHash($_POST["password"]);

	$password = password_hash($_POST["password"], PASSWORD_DEFAULT, ['cost'=>12]);

	$user->setPassword($password);

	$page = new PageAdmin([
		"header"=>false,
		"footer"=>false
	]);

	$page->setTpl("forgot-reset-success");

});

//_____________________________________

?>