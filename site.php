<?php 

use \Hcode\Page;
use \Hcode\Model\Product;
// use \Hcode\Model\Category;
// use \Hcode\Model\Cart;
// use \Hcode\Model\Address;
// use \Hcode\Model\User;
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
		'products'=>Product::checkList($products)
	]);		//Chama o "index", que se refere ao arquivo \views\index.html

});




 ?>