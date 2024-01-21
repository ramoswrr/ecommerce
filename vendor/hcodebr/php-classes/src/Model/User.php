<?php 

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;

//Essa classe User é um Model. E todo Model vai ter gets e sets. 
//É mais inteligente criar uma classe Model que saiba fazer seus gets e sets. 
//E cada classe, cada DAO e qualquer outra coisa, ela já vai extender de um Model que já vai saber fazer esses gets e sets.
class User extends Model 
{

    const SESSION = "User";

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
            $user = new User();       //Criando uma intância dentro da própria classe. Vai trazer os métodos de Model, pois User extende de Model.

            //$user->setiduser($data["iduser"]); //o médtodo __call($name, $args) da classe Model vai identificar que se tratar de um set (que "seta" o atributo iduser)

            $user->setDate($data);          //ao invés de passar cada campo (iduser, despassaword...), vamos criar o método que faça isso para buscar todos os campos.

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

		if (   !isset($_SESSION[User::SESSION])                 //Se a sessão não for definida
            || !$_SESSION[User::SESSION]                        //ou se a sessão for falsa
            || !(int)$_SESSION[User::SESSION]["iduser"] > 0   //ou se o iduser que está dentro da sessão não for maior que 0. obs: (int) é para casting. 
            || (bool)$_SESSION[User::SESSION]["inadmin"] !== $inadmin   //verificar se está logado na administração, porque se for um usuário da loja, não pode entrar na área de adm.
            )
            //(casting é o processo de converter um valor de um tipo de dados para outro. Por exemplo, se você tem uma variável $x que contém um valor inteiro e deseja convertê-la em uma string, você pode fazer um cast da variável $x para string usando a sintaxe (string) $x ).
        {

            header("Location: /admin/login");       //Redirecionar para a tela de login
            exit;

		}

	}


    public static function logout()
	{

		$_SESSION[User::SESSION] = NULL;

	}




}

?>