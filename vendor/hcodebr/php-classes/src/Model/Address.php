<?php 

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;

class Address extends Model {

	const SESSION_ERROR = "AddressError";

	public static function getCEP($nrcep)
	{

		$nrcep = str_replace("-", "", $nrcep);

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, "http://viacep.com.br/ws/$nrcep/json/");

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		$data = json_decode(curl_exec($ch), true);

		curl_close($ch);

		return $data;

	}

	public function loadFromCEP($nrcep)
	{

		$data = Address::getCEP($nrcep);

		if (isset($data['logradouro']) && $data['logradouro']) {

			$this->setdesaddress($data['logradouro']);
			$this->setdescomplement($data['complemento']);
			$this->setdesdistrict($data['bairro']);
			$this->setdescity($data['localidade']);
			$this->setdesstate($data['uf']);
			$this->setdescountry('Brasil');
			$this->setdeszipcode($nrcep);

		}

	}

	public function save()
	{

		$sql = new Sql();

		$results = $sql->select("CALL sp_addresses_save(:idaddress, :idperson, :desaddress, :desnumber, :descomplement, :descity, :desstate, :descountry, :deszipcode, :desdistrict)", [
			':idaddress'=>$this->getidaddress(),
			':idperson'=>$this->getidperson(),
			':desaddress'=>$this->getdesaddress(),
			':desnumber'=>$this->getdesnumber(),
			':descomplement'=>$this->getdescomplement(),
			':descity'=>$this->getdescity(),
			':desstate'=>$this->getdesstate(),
			':descountry'=>$this->getdescountry(),
			':deszipcode'=>$this->getdeszipcode(),
			':desdistrict'=>$this->getdesdistrict()
		]);

		////Obs: utf8_decode is deprecated
		////Alterando a classe Sql é possível padronizar as inserções com utf8 (...array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")); )
		// $results = $sql->select("CALL sp_addresses_save(:idaddress, :idperson, :desaddress, :desnumber, :descomplement, :descity, :desstate, :descountry, :deszipcode, :desdistrict)", [
		// 	':idaddress'=>$this->getidaddress(),
		// 	':idperson'=>$this->getidperson(),
		// 	':desaddress'=>utf8_decode($this->getdesaddress()),
		// 	':desnumber'=>$this->getdesnumber(),
		// 	':descomplement'=>utf8_decode($this->getdescomplement()),
		// 	':descity'=>utf8_decode($this->getdescity()),
		// 	':desstate'=>utf8_decode($this->getdesstate()),
		// 	':descountry'=>utf8_decode($this->getdescountry()),
		// 	':deszipcode'=>$this->getdeszipcode(),
		// 	':desdistrict'=>$this->getdesdistrict()
		// ]);

		////Obs: substituindo utf8_decode por mb_convert_encoding
		// $results = $sql->select("CALL sp_addresses_save(:idaddress, :idperson, :desaddress, :desnumber, :descomplement, :descity, :desstate, :descountry, :deszipcode, :desdistrict)", [
		// 	':idaddress'=>$this->getidaddress(),
		// 	':idperson'=>$this->getidperson(),
		// 	':desaddress'=>mb_convert_encoding($this->getdesaddress(), "Windows-1252", "UTF-8"),
		// 	':desnumber'=>$this->getdesnumber(),
		// 	':descomplement'=>mb_convert_encoding($this->getdescomplement(), "Windows-1252", "UTF-8"),
		// 	':descity'=>mb_convert_encoding($this->getdescity(), "Windows-1252", "UTF-8"),
		// 	':desstate'=>mb_convert_encoding($this->getdesstate(), "Windows-1252", "UTF-8"),
		// 	':descountry'=>mb_convert_encoding($this->getdescountry(), "Windows-1252", "UTF-8"),
		// 	':deszipcode'=>$this->getdeszipcode(),
		// 	':desdistrict'=>$this->getdesdistrict()
		// ]);

		if (count($results) > 0) {
			$this->setData($results[0]);
		}

	}

	public static function setMsgError($msg)
	{

		$_SESSION[Address::SESSION_ERROR] = $msg;

	}

	public static function getMsgError()
	{

		$msg = (isset($_SESSION[Address::SESSION_ERROR])) ? $_SESSION[Address::SESSION_ERROR] : "";

		Address::clearMsgError();

		return $msg;

	}

	public static function clearMsgError()
	{

		$_SESSION[Address::SESSION_ERROR] = NULL;

	}

}

 ?>