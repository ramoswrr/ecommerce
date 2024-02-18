<?php 

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Mailer;

//Essa classe User é um Model. E todo Model vai ter gets e sets. 
//É mais inteligente criar uma classe Model que saiba fazer seus gets e sets. 
//E cada classe, cada DAO e qualquer outra coisa, ela já vai extender de um Model que já vai saber fazer esses gets e sets.
class User extends Model 
{

	const SESSION = "User";
	const SECRET = "HcodePhp7_Secret";
	const SECRET_IV = "HcodePhp7_Secret_IV";
	const ERROR = "UserError";
	const ERROR_REGISTER = "UserErrorRegister";
	const SUCCESS = "UserSucesss";


	public static function getFromSession()
	{

		$user = new User();

		if (isset($_SESSION[User::SESSION]) && (int)$_SESSION[User::SESSION]['iduser'] > 0) {

			$user->setData($_SESSION[User::SESSION]);

		}

		return $user;

	}


	public static function checkLogin($inadmin = true)
	{

		if (
			!isset($_SESSION[User::SESSION])
			||
			!$_SESSION[User::SESSION]
			||
			!(int)$_SESSION[User::SESSION]["iduser"] > 0
		) {
			//Não está logado
			return false;

		} else {

			if ($inadmin === true && (bool)$_SESSION[User::SESSION]['inadmin'] === true) {

				return true;

			} else if ($inadmin === false) {

				return true;

			} else {

				return false;

			}

		}

	}



    public static function login($login, $password)
    {
        $sql = new Sql();

        $results = $sql->select("SELECT * FROM tb_users WHERE deslogin = :LOGIN", array(
            ":LOGIN"=>$login
            ));

        if (count($results) === 0)
        {
            throw new \Exception("Usuário inexistente ou senha inválida.", 1);     //Como é uma classe nativa do php, insirir \Exception
        }

        $data = $results[0];
        
        if( password_verify($password, $data["despassword"]) === true)  //Se a senha digitada corresponde a senha criptografada do bd.
        {
            $user = new User();       //Criando uma instância dentro da própria classe. Vai trazer os métodos de Model, pois User extende de Model.

            //$user->setiduser($data["iduser"]); //o método __call($name, $args) da classe Model vai identificar que se tratar de um set (que "seta" o atributo iduser)

            //$data['desperson'] = utf8_encode($data['desperson']); //utf8_encode id deprecated
			//$data['desperson'] = iconv('ISO-8859-1', 'UTF-8', $data['desperson']);

			$user->setData($data);          //ao invés de passar cada campo (iduser, despassaword...), vamos criar o método que faça isso para buscar todos os campos.

            // var_dump($user);
            // exit;

            //Para funcionar um login vc precisa criar uma sessão. Em outras páginas precisa verificar se essa sessão já existe. Se ela existir, quer dizer que está logado. Se não, vamos redirecionar para página de login.
            //Vamos definir o nome. Esse nome será uma constante da própria classe por questão de organização e para o caso de usar em outros lugares nós usaremos a constante que ficará em um lugar só.
            //dentro da sessão vamos colocar só os dados desse objeto usuário como um array, que será trazido pelo método getValues().
            $_SESSION[User::SESSION] = $user->getValues();   

            return $user;

        }
        else
        {
            throw new \Exception("Usuário inexistente ou senha inválida.", 1);
        }

    }


	public static function verifyLogin($inadmin = true) 
	{

		if (!User::checkLogin($inadmin)) {

			if ($inadmin) {
				header("Location: /admin/login");
			} else {
				header("Location: /login");
			}
			exit;

		}
		
		// if (   !isset($_SESSION[User::SESSION])                 //Se a sessão não for definida
        //     || !$_SESSION[User::SESSION]                        //ou se a sessão for falsa
        //     || !(int)$_SESSION[User::SESSION]["iduser"] > 0   //ou se o iduser que está dentro da sessão não for maior que 0. obs: (int) é para casting. 
        //     || (bool)$_SESSION[User::SESSION]["inadmin"] !== $inadmin   //verificar se está logado na administração, porque se for um usuário da loja, não pode entrar na área de adm.
        //     )
        //     //(casting é o processo de converter um valor de um tipo de dados para outro. Por exemplo, se você tem uma variável $x que contém um valor inteiro e deseja convertê-la em uma string, você pode fazer um cast da variável $x para string usando a sintaxe (string) $x ).
        // {

        //     header("Location: /admin/login");       //Redirecionar para a tela de login
        //     exit;

		// }

	}


    public static function logout()
	{

		$_SESSION[User::SESSION] = NULL;

	}


    public static function listAll()
	{

		$sql = new Sql();

		return $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) ORDER BY b.desperson");

	}


	public function save()
	{

		$sql = new Sql();

		//":desperson"=>utf8_decode($this->getdesperson()), // utf8_decode is deprecated
		//":desperson"=>mb_convert_encoding($this->getdesperson(), 'UTF-8', 'ISO-8859-1'), 
		$results = $sql->select("CALL sp_users_save(:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
			":desperson"=>$this->getdesperson(), 
            ":deslogin"=>$this->getdeslogin(),
            ":despassword"=>User::getPasswordHash($this->getdespassword()),
            ":desemail"=>$this->getdesemail(),
            ":nrphone"=>$this->getnrphone(),
            ":inadmin"=>$this->getinadmin()
		));

		$this->setData($results[0]);

        /*
        Aula 107
        Slim Application Error
        The application could not run because of the following error:

        Details
        Code: 8
        Message: Undefined offset: 0
        File: C:\ecommerce\vendor\hcodebr\php-classes\src\Model\User.php
        Line: 109
        Trace
        #0 C:\ecommerce\vendor\hcodebr\php-classes\src\Model\User.php(109): Slim\Slim::handleErrors(8, 'Undefined offse...', 'C:\\ecommerce\\ve...', 109, Array)
        #1 C:\ecommerce\index.php(146): Hcode\Model\User->save()
        #2 [internal function]: {closure}()
        #3 C:\ecommerce\vendor\slim\slim\Slim\Router.php(200): call_user_func_array(Object(Closure), Array)
        #4 C:\ecommerce\vendor\slim\slim\Slim\Slim.php(1210): Slim\Router->dispatch(Object(Slim\Route))
        #5 C:\ecommerce\vendor\slim\slim\Slim\Middleware\Flash.php(86): Slim\Slim->call()
        #6 C:\ecommerce\vendor\slim\slim\Slim\Middleware\MethodOverride.php(94): Slim\Middleware\Flash->call()
        #7 C:\ecommerce\vendor\slim\slim\Slim\Middleware\PrettyExceptions.php(67): Slim\Middleware\MethodOverride->call()
        #8 C:\ecommerce\vendor\slim\slim\Slim\Slim.php(1159): Slim\Middleware\PrettyExceptions->call()
        #9 C:\ecommerce\index.php(179): Slim\Slim->run()
        #10 {main}
        */
	}


    public function get($iduser)
	{

		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_users a INNER JOIN tb_persons b USING(idperson) WHERE a.iduser = :iduser", array(
			":iduser"=>$iduser
		));

		$data = $results[0];
		//$data['desperson'] = utf8_encode($data['desperson']); //utf8_encode id deprecated
		//$data['desperson'] = iconv('ISO-8859-1', 'UTF-8', $data['desperson']);

		$this->setData($data);

	}


    public function update()
	{

		$sql = new Sql();

        //Essa Call chama uma Procedure que atualiza duas tabelas de uma vez (tb_users e tb_persons)
		//":desperson"=>utf8_decode($this->getdesperson()), // utf8_decode is deprecated
		//":desperson"=>mb_convert_encoding($this->getdesperson(), 'UTF-8', 'ISO-8859-1'),
		$results = $sql->select("CALL sp_usersupdate_save(:iduser, :desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", array(
			":iduser"=>$this->getiduser(),
            ":desperson"=>$this->getdesperson(),
            ":deslogin"=>$this->getdeslogin(),
            ":despassword"=>User::getPasswordHash($this->getdespassword()),
            ":desemail"=>$this->getdesemail(),
            ":nrphone"=>$this->getnrphone(),
            ":inadmin"=>$this->getinadmin()
		));

		$this->setData($results[0]);	

	}


    public function delete()
	{

		$sql = new Sql();

		$sql->query("CALL sp_users_delete(:iduser)", array(
			":iduser"=>$this->getiduser()
		));

	}


    public static function getForgot($email, $inadmin = true)
	{

		$sql = new Sql();

		$results = $sql->select(
        "
			SELECT *
			FROM tb_persons a
			INNER JOIN tb_users b USING(idperson)
			WHERE a.desemail = :email;
		", array(
			":email"=>$email
		));

		if (count($results) === 0)
		{

			throw new \Exception("Não foi possível recuperar a senha.");

		}
		else
		{

			$data = $results[0];

			$results2 = $sql->select("CALL sp_userspasswordsrecoveries_create(:iduser, :desip)", array(
				":iduser"=>$data['iduser'],
				":desip"=>$_SERVER['REMOTE_ADDR']
			));

			if (count($results2) === 0)
			{

				throw new \Exception("Não foi possível recuperar a senha.");

			}
			else
			{

                $data = $results[0];

				$results2 = $sql->select("CALL sp_userpasswordsrecoveries_create(:iduser, :desip", array(
                    "iduser"=>$data["iduser"],
                    ":desip"=>$_SERVER["REMOTE_ADDR"]       //Pega o IP do usuário.
                ));


                if (count($results2) === 0)
                {
                    throw new \Exception("Não foi possível recuperar a senha.");
                } 
                else
                {
                    $dataRecovery = $results2[0];
                    
                    //$code = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, User::SECRET, $dataRecovery["idrecovery"], MCRYPT_MODE_ECB));  // depreciada no PHP 7.1
                    $code = openssl_encrypt($dataRecovery['idrecovery'], 'AES-128-CBC', pack("a16", User::SECRET), 0, pack("a16", User::SECRET_IV));

					if ($inadmin === true) {

						$link = "http://www.hcodecommerce.com.br/admin/forgot/reset?code=$code";
	
					} else {
	
						$link = "http://www.hcodecommerce.com.br/forgot/reset?code=$code";
						
					}	

                    $mailer = new Mailer( $data["desemail"], $data["desperson"], "Redefinir Senha", "forgot", 
                        array(
                            "name"=>$data["desperson"],
                            "link"=>$link
                    ));

                    $mailer->send();

                    return $data;
                }



			}

		}

	}


    public static function validForgotDecrypt($code)
	{

		$code = base64_decode($code);

		$idrecovery = openssl_decrypt($code, 'AES-128-CBC', pack("a16", User::SECRET), 0, pack("a16", User::SECRET_IV));

		$sql = new Sql();

		$results = $sql->select("
			SELECT *
			FROM tb_userspasswordsrecoveries a
			INNER JOIN tb_users b USING(iduser)
			INNER JOIN tb_persons c USING(idperson)
			WHERE
				a.idrecovery = :idrecovery
				AND
				a.dtrecovery IS NULL
				AND
				DATE_ADD(a.dtregister, INTERVAL 1 HOUR) >= NOW();
		", array(
			":idrecovery"=>$idrecovery
		));

		if (count($results) === 0)
		{
			throw new \Exception("Não foi possível recuperar a senha.");
		}
		else
		{

			return $results[0];

		}

	}


    public static function setFogotUsed($idrecovery)
	{

		$sql = new Sql();

		$sql->query("UPDATE tb_userspasswordsrecoveries SET dtrecovery = NOW() WHERE idrecovery = :idrecovery", array(
			":idrecovery"=>$idrecovery
		));

	}


    public static function getPasswordHash($password)
	{

		return password_hash($password, PASSWORD_DEFAULT, [
			'cost'=>12
		]);

	}


	public static function getError()
	{

		$msg = (isset($_SESSION[User::ERROR]) && $_SESSION[User::ERROR]) ? $_SESSION[User::ERROR] : '';

		User::clearError();

		return $msg;

	}


	public static function clearError()
	{

		$_SESSION[User::ERROR] = NULL;

	}


	public static function setError($msg)
	{

		$_SESSION[User::ERROR] = $msg;

	}


	public static function setErrorRegister($msg)
	{

		$_SESSION[User::ERROR_REGISTER] = $msg;

	}

	
	public static function setSuccess($msg)
	{

		$_SESSION[User::SUCCESS] = $msg;

	}

	public static function getSuccess()
	{

		$msg = (isset($_SESSION[User::SUCCESS]) && $_SESSION[User::SUCCESS]) ? $_SESSION[User::SUCCESS] : '';

		User::clearSuccess();

		return $msg;

	}

	public static function clearSuccess()
	{

		$_SESSION[User::SUCCESS] = NULL;

	}


	public static function getErrorRegister()
	{

		$msg = (isset($_SESSION[User::ERROR_REGISTER]) && $_SESSION[User::ERROR_REGISTER]) ? $_SESSION[User::ERROR_REGISTER] : '';

		User::clearErrorRegister();

		return $msg;

	}


	public static function clearErrorRegister()
	{

		$_SESSION[User::ERROR_REGISTER] = NULL;

	}


	public static function checkLoginExist($login)
	{

		$sql = new Sql();

		$results = $sql->select("SELECT * FROM tb_users WHERE deslogin = :deslogin", [
			':deslogin'=>$login
		]);

		return (count($results) > 0);

	}




}

?>