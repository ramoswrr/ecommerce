<?php 

use \Hcode\PageAdmin;
use \Hcode\Model\User;


//_____________________________________
$app->get("/admin/users", function() {

	User::verifyLogin();		//Para verificar se o usuário estpá logado.
	
	$users = User::listAll();	//Traz o array com toda lista de usuários do bd.
	
	$page = new PageAdmin();
	
	$page->setTpl("users", array(
		"users"=>$users				//Passamos o array de usuários para o template users.html.
	));		

});


//_____________________________________ Se acessar via get, a resposta será com um html.
$app->get("/admin/users/create", function() {		

	User::verifyLogin();

	$page = new PageAdmin();

	$page->setTpl("users-create");

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


//_____________________________________
//Rotas com subrotas devem ficar em cima das rotas.
$app->get("/admin/users/:iduser/delete", function($iduser) {			

	User::verifyLogin();

	$user = new User();

	$user->get((int)$iduser);

	$user->delete();

	header("Location: /admin/users");
	exit;

});


//_____________________________________
$app->get("/admin/users/:iduser", function($iduser) {

	User::verifyLogin();

	$user = new User();

	$user->get((int)$iduser);


	$page = new PageAdmin();

	$page->setTpl("users-update", array(
		"user"=>$user->getValues()
	));

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

//_____________________________________

?>