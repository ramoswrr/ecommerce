<?php 

use \Hcode\Page;
// use \Hcode\Model\Product;
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

	$page = new Page();
	
	$page->setTpl("index");		//Chama o "index", que se refere ao arquivo \views\index.html

});




 ?>