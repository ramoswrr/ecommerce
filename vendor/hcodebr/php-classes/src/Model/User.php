<?php 

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;

//Essa classe User é um Model. E todo Model vai ter gets e sets. 
//É mais inteligente criar uma classe Model que saiba fazer seus gets e sets. 
//E cada classe, cada DAO e qualquer outra coisa, ela já vai extender de um Model que já vai saber fazer esses gets e sets.
class User extends Model 
{

    public static function login($login, $password)
    {
        $sql = new Sql();

        $results = $sql->select("SELECT * FROM tb_users WHERE deslogin = :LOGIN", array(
            ":LOGIN"=>$login
            ));

        if (count($results) === 0)
        {
            throw new \Exception("Usuário inexistente ou senha inválida.", 1);     //Como é uma classe ntiva do php, insirir \Exception
        }

        $data = $results[0];
        
        if( password_verify($password, $data["despassword"]) === true)  //Se a senha digitada corresponde a senha criptografada do bd.
        {
            $user = new User();       //Criando uma intância dentro da própria classe. Vai trazer os métodos de Model, pois User extende de Model.

            $user->setiduser($data["iduser"]); //o médtodo __call($name, $args) da classe Model vai identificar que se tratar de um set (que "seta" o atributo iduser)

        }
        else
        {
            throw new \Exception("Usuário inexistente ou senha inválida.", 1);
        }


    }
}

?>