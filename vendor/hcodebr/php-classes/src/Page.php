<?php

namespace Hcode;        //Tem que especificar onde essa classe Page está.

use Rain\Tpl;           //Quando for chamar o a classe tpl tem que especificar que está no namespace Rain.

     
class Page              //Classe com mesmo nome do arquivo Page.php
{

	private $tpl;
	private $options = [];
	private $defaults = [
		"header"=>true,
		"footer"=>true,
		"data"=>[]
	];

	public function __construct($opts = array(), $tpl_dir = "/views/")
    {
		
		$this->options = array_merge($this->defaults, $opts);	//Funde os elementos de 2 ou mais arrays de forma que os elementos de um são colocados no final do array anterior. Retorna o array resultante da fusão. No merger prevalece o que mandamos pelo método no index.

		$config = array(
			"tpl_dir"       => $_SERVER["DOCUMENT_ROOT"].$tpl_dir,
			"cache_dir"     => $_SERVER["DOCUMENT_ROOT"]."/views-cache/",
			"debug"         => false
	    );

		Tpl::configure( $config );

		$this->tpl = new Tpl;

        // foreach ($this->options["data"] as $key => $value) {
		// 	$this->tpl->assign($key, $value);
		// } //// chame a $this->setData()

		$this->setData($this->options["data"]);	//na posicao "data" do options temos um array com as informações da sessão. linha 25

		if ($this->options["header"] === true) $this->tpl->draw("header");

	}



	private function setData($data = array())   //classe que será inserida em __construct e setTpl
	{
		foreach ($data as $key => $value) {
			$this->tpl->assign($key, $value);
		}
	}



	public function setTpl($name, $data = array(), $returnHTML = false)
	{
        // foreach ($this->options["data"] as $key => $value) {
		// 	$this->tpl->assign($key, $value);
		// } //// chame a $this->setData()

		$this->setData($data);

		return $this->tpl->draw($name, $returnHTML);
	}



	public function __destruct()
    {

		if ($this->options["footer"] === true) $this->tpl->draw("footer");

	}

}

?>