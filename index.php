<?php 
session_start();
require_once("vendor/autoload.php");

use \Slim\Slim;
use \Hcode\Page;
use \Hcode\PageAdmin;
use \Hcode\Model\User;
use \Hcode\Model\Category;

//___________________________________________________________
$app = new Slim();

$app->config('debug', true);


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


//Se acessar via get, a resposta será com um html.
$app->get("/admin/users/create", function() {		

	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("users-create");

});


//Rotas com subrotas devem ficar em cima das rotas.
$app->get("/admin/users/:iduser/delete", function($iduser) {			

	User::verifyLogin();

	$user = new User();

	$user->get((int)$iduser);

	$user->delete();

	header("Location: /admin/users");
	exit;

});


$app->get("/admin/users/:iduser", function($iduser) {

	User::verifyLogin();

	$user = new User();

	$user->get((int)$iduser);


	$page = new PageAdmin();

	$page->setTpl("users-update", array(
		"user"=>$user->getValues()
	));

});


//Se acessar com post, ele vai fazer o insert do dados. Ele espera receber os dados em post para depois enviar para o bd.
$app->post("/admin/users/create", function() {		

	User::verifyLogin();

	$user = new User();

	$_POST["inadmin"] = (isset($_POST["inadmin"])) ? 1 : 0;

	$_POST['despassword'] = User::getPasswordHash($_POST['despassword']);

	$user->setData($_POST);

	$user->save();

	header("Location: /admin/users");

	exit;

});


$app->post("/admin/users/:iduser", function($iduser) {

	User::verifyLogin();

	$user = new User();

	$_POST["inadmin"] = (isset($_POST["inadmin"]))?1:0;

	$user->get((int)$iduser);

	$user->setData($_POST);

	$user->update();	

	header("Location: /admin/users");
	exit;

});


$app->get("/admin/users", function() {

	User::verifyLogin();		//Para verificar se o usuário estpá logado.
	
	$users = User::listAll();	//Traz o array com toda lista de usuários do bd.
	
	$page = new PageAdmin();
	
	$page->setTpl("users", array(
		"users"=>$users				//Passamos o array de usuários para o template users.html.
	));		

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


//_____________________________________ endereço: http://www.hcodecommerce.com.br/admin/categories
$app->get("/admin/categories", function(){

	$categories = Category::listAll();

	$page = new PageAdmin();

	$page->setTpl("categories", [
		'categories'=>$categories,
	]);

});


$app->get("/admin/categories/create", function(){

	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("categories-create");	

});


$app->post("/admin/categories/create", function(){

	User::verifyLogin();

	$category = new Category();

	$category->setData($_POST);

	$category->save();

	header('Location: /admin/categories');
	exit;

});


$app->get("/admin/categories/:idcategory/delete", function($idcategory){

	User::verifyLogin();

	$category = new Category();

	$category->get((int)$idcategory);

	$category->delete();

	header('Location: /admin/categories');
	exit;

});


$app->get("/admin/categories/:idcategory", function($idcategory){

	User::verifyLogin();

	$category = new Category();

	$category->get((int)$idcategory);

	$page = new PageAdmin();

	$page->setTpl("categories-update", [
		'category'=>$category->getValues()
	]);	

});


$app->post("/admin/categories/:idcategory", function($idcategory){

	User::verifyLogin();

	$category = new Category();

	$category->get((int)$idcategory);

	$category->setData($_POST);

	$category->save();	

	header('Location: /admin/categories');
	exit;

});



//___________________________________________________________

$app->run();

 ?>