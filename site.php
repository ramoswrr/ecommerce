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



$app->get("/categories/:idcategory", function($idcategory){

	$page = (isset($_GET['page'])) ? (int)$_GET['page'] : 1;

	$category = new Category();

	$category->get((int)$idcategory);

	$pagination = $category->getProductsPage($page, 8);

	$pages = [];

	for ($i=1; $i <= $pagination['pages']; $i++) { 
		array_push($pages, [
			'link'=>'/categories/'.$category->getidcategory().'?page='.$i,
			'page'=>$i
		]);
	}

	$page = new Page();

	$page->setTpl("category", [
		'category'=>$category->getValues(),
		'products'=>$pagination["data"],
		'pages'=>$pages
	]);

});


$app->get("/products/:desurl", function($desurl){

	$product = new Product();

	$product->getFromURL($desurl);

	$page = new Page();

	$page->setTpl("product-detail", [
		'product'=>$product->getValues(),
		'categories'=>$product->getCategories()
	]);

});




?>