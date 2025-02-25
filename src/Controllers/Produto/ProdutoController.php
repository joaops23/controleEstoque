<?php

namespace Controllers\Produto;

use Controllers\Controller;
use Models\Produto;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use stdClass;

class ProdutoController extends Controller
{
    protected $model;

    public function __construct()
    {
        $this->model = new Produto();
    }

    public static function store(Request $req, Response $res): Response
    {
        $ctr = new static();
        try{
            $body = self::getBody($req);

            self::trataCadastro($body);
            $params = self::setParamsToInsert($body);
    
            $resp = $ctr->model->setInsertUser($params);

            return $ctr::getResponse($res, ["id" => $resp], '201');
        } catch(\Exception $e) {
            return $ctr::getResponse($res, ['message' => $e->getMessage()], "403");
        }
    }

    public static function trataCadastro(stdClass &$params): void
    {
        #prd_descricao
        if(isset($params->prd_descricao) && !empty($params->prd_descricao)) {
            $desc = trim($params->prd_descricao);

            $params->prd_descricao = substr($desc, 0, 255);
        } else {
            throw new \Exception("Descrição do produto Inválida!");
        }
        
        #prd_valor
        if(isset($params->prd_valor) && !empty($params->prd_valor)) {
            if(!is_float($params->prd_valor)) {
                throw new \Exception("Valor do produto inválido!");
            }
            
            $desc = floatval($params->prd_valor);

            $params->prd_valor = $desc;
        } else {
            throw new \Exception("Descrição do produto Inválida!");
        }

        #status
    }
}