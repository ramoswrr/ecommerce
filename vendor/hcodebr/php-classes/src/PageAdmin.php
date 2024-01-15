<?php

namespace Hcode;        //Tem que especificar onde essa classe Page está.

use Rain\Tpl;           //Quando for chamar o a classe tpl tem que especificar que está no namespace Rain.

     
class PageAdmin extends Page              //Classe com mesmo nome do arquivo PageAdmin.php
{

	public function __construct($opts = array(), $tpl_dir = "/views/admin/")
    {
		parent::__construct( $opts, $tpl_dir );

	}


}

?>