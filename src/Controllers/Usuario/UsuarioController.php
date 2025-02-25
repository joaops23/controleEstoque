<?php

namespace Controllers\Usuario;

use Controllers\Controller;
use FastRoute\RouteParser\Std;
use Models\Usuario;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UsuarioController extends Controller {

    protected $model;

    public function __construct()
    {
        $this->model = new Usuario();
    }
    public static function getUsers(Request $req, Response $res)
    {
        $ctr = new static();

        $body = self::getBodyNotRequired($req);
        $params = self::setParamsToFetch($body->params);
        $users = $ctr->model->getAllUsers($params, $body->order);

        return $ctr::getResponse($res, $users, "200");
    }

    public static function getUsersById(Request $req, Response $res, $args)
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

    public static function storeUser(Request $req, Response $res, $args)
    {
        $ctr = new static();
        try{
    
            $body = self::getBody($req);

            self::trataCadastro($body);
            $params = self::setParamsToInsert($body);
    
            $resp = $ctr->model->setInsertUser($params);

            return $ctr::getResponse($res, ["id" => $resp], '201');
            //return $ctr::getResponse($res, ['message' => "Teste"], '200');
        } catch(\Exception $e) {
            return $ctr::getResponse($res, ['message' => $e->getMessage()], "403");
        }
    }


    # Métodos de apoio
    private static function setParamsToFetch($paramsReq)
    {
        $params = array();
        $arrayParams = get_object_vars($paramsReq);
        if(count($arrayParams)> 0) {
            foreach($arrayParams as $i => $val) {
                if(!empty($i) && is_array($val) && count($val)){
                    $val = self::formatVal($val);
                    $params[$i] = $val;
                }
            }
        }

        return $params;
    }

    private static function formatVal($arrVal = array())
    {
        $retVal = array();
        foreach($arrVal as $val) {
            if(!in_array($val, static::getOperations())) {
                $retVal[] = "|sl" . $val . "|sl";
            } else {
                $retVal[] = $val;
            }
        }

        return $retVal;
    }

    private static function setParamsToInsert($paramsReq)
    {
        $params = array();
        $arrayParams = get_object_vars($paramsReq);
        if(count($arrayParams)> 0) {
            foreach($arrayParams as $i => $val) {
                $val = self::formatValToInsert($val);
                $params[$i] = $val;
            }
        }

        return $params;
    }

    private static function formatValToInsert($val)
    {
        $retVal = '';
        if(!in_array($val, static::getOperations())) {
            $retVal = "|sl" . $val . "|sl";
        } else {
            $retVal = $val;
        }

        return $retVal;
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


        $params->usu_data_inclusao = date("Y-m-d");
        $params->usu_senha = '000000'; # adiciona senha provisória
    }
}