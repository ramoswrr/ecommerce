<?php 

use \Hcode\Page;
use \Hcode\Model\Product;
use \Hcode\Model\Category;
// use \Hcode\Model\Cart;
// use \Hcode\Model\Address;
use \Hcode\Model\User;
// use \Hcode\Model\Order;
// use \Hcode\Model\OrderStatus;


//_____________________________________ endereço: http://www.hcodecommerce.com.br/
$app->get('/', function() {
    
	// $sql = new Hcode\DB\Sql();
	// $results = $sql->select("SELECT * FROM tb_users");
	// echo json_encode($results);

	$products = Product::listAll();

	$page = new Page();

	$page->setTpl("index", [
		'products'=>Product::checkList($products)		//O método estático Product::checkList vai verificar se cada foto do produto foi feita o upload. 
	]);		//Chama o "index", que se refere ao arquivo \views\index.html

});



$app->get("/categories/:idcategory", function($idcategory){		/////////////////////////

	$category = new Category();

	$category->get((int)$idcategory);

	$page = new Page();

	$page->setTpl("category", [
		'category'=>$category->getValues(),
		'products'=>Product::checkList($category->getProducts())	//O método estático Product::checkList vai verificar se cada foto do produto foi feita o upload. 
	]);

});


?>