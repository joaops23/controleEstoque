<?php

namespace Controllers\Usuario;

use Controllers\Controller;
use Models\Usuario;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Services\Mail\Mailer;
use Services\Mail\OptionsSendMail;

class UsuarioController extends Controller {

    protected $model;

    public function __construct()
    {
        $this->model = new Usuario();
    }
    public static function getUsers(Request $req, Response $res): Response
    {
        $ctr = new static();

        $body = self::getBodyNotRequired($req);
        $params = self::setParamsToFetch($body->params);
        $users = $ctr->model->getAllUsers($params, $body->order);

        return $ctr::getResponse($res, $users, "200");
    }

    public static function getUsersById(Request $req, Response $res, $args): Response
    {
        $ctr = new static();

        $id = $args['id'];

        $objectParams = (object) array(
            "usu_id" => ["=", $id]
        );
        
        $params = self::setParamsToFetch($objectParams);
        $users = ($ctr->model->getAllUsers($params))[0];

        return $ctr::getResponse($res, $users, "200");
    }

    public static function storeUser(Request $req, Response $res, $args): Response
    {
        $ctr = new static();
        try{
    
            $body = self::getBody($req);

            self::trataCadastro($body);
            $body->usu_data_inclusao = date("Y-m-d");
            $body->usu_senha = '000000'; # adiciona senha provisória
            $body->usu_stt = 4;
            $params = self::setParamsToInsert($body);
    
            $resp = $ctr->model->setInsertUser($params);

            return $ctr::getResponse($res, ["id" => $resp], '201');
            //return $ctr::getResponse($res, ['message' => "Teste"], '200');
        } catch(\Exception $e) {
            return $ctr::getResponse($res, ['message' => $e->getMessage()], "403");
        }
    }

    public static function updateUser(Request $req, Response $res, $args): Response
    {
        $ctr = new static();

        try{
            $body = self::getBody($req);
            $id = $args['id'];

            self::trataCadastro($body);
            $params = self::setParamsToInsert($body);
    
            $resp = $ctr->model->setUpdateUser($id, $params);

            return $ctr::getResponse($res, ["id" => $resp], '201');
        } catch(\Exception $e) {
            return $ctr::getResponse($res, ['message' => $e->getMessage()], "403");
        }
    }

    private static function trataCadastro(&$params)
    {
        if(isset($params->usu_cpf) && !empty($params->usu_cpf)) {
            $cpf = str_replace(['.', ',', '-'], "", $params->usu_cpf);
            if(strlen($cpf) > 11) {
                throw new \Exception("CPF Inválido!");
            }

            $params->usu_cpf = $cpf;
        } else {
            throw new \Exception("Informar CPF corretamente!");
        }

        if(isset($params->usu_nome) && !empty($params->usu_nome)) {
            $nome = $params->usu_nome;
            $params->usu_nome = substr($nome, 0, 100);
        } else {
            throw new \Exception("Informar nome corretamente!");
        }


        if(isset($params->usu_email) && !empty($params->usu_email)) {
            $nome = $params->usu_email;
            $params->usu_email = substr($nome, 0, 100);
        } else {
            throw new \Exception("Informar email corretamente!");
        }

    }

    public static function sendMailTemporaryPass(Request $req, Response $res, $args): Response
    {
        $ctr = new Static();
        try{
            $usuId = $args['id'];
            
            $usuario = $ctr->model->getUserById($usuId);

            $mail = new Mailer();

            $mail->send(new OptionsSendMail([
                "to" => $usuario->usu_email,
                "from" => getenv('MAIL_FROM'),
                "subject" => "Envio de senha provisória",
                "body" => "<p>Olá! Aqui está sua senha provisória, realize o login com estas credenciais e solicite a alteração de senha</p>
                    </br><b>Usuario:</b>{$usuario->usu_email}
                    </br><b>Senha:</b>{$usuario->usu_senha}
                ",
            ]));
            

            return $ctr::getResponse($res, ['message' => "Senha provisória enviada com sucesso!"], 200);

        } catch(\Exception $e) {
            return $ctr::getResponse($res, ['message' => $e->getMessage()], '422');
        }
    }
}