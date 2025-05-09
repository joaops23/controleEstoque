<?php

namespace Controllers\Produto;

use Controllers\Controller;
use Models\Produto;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Services\Xls\Reader;
use Slim\Psr7\UploadedFile;
use stdClass;
use PhpOffice\PhpSpreadsheet\IOFactory;

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
    
            $resp = $ctr->model->setInsertProduct($params);

            return $ctr::getResponse($res, ["id" => $resp], '201');
        } catch(\Exception $e) {
            return $ctr::getResponse($res, ['message' => $e->getMessage()], "403");
        }
    }

    public static function getProducts(Request $req, Response $res, $args): response
    {
        $ctr = new static();

        $body = self::getBodyNotRequired($req);
        $params = self::setParamsToFetch($body->params);
        $id = isset($args['id']) && !empty($args['id']) ? $args['id']  : '';

        (isset($id) && !empty($id)) ? $params['prd_id'] = ["=", $id] : '';

        $order = isset($body->order) ? $body->order : new stdClass();

        $users = $ctr->model->getAllProducts($params, $order);

        return $ctr::getResponse($res, $users, "200");
    }


    public static function update(Request $req, Response $res, $args): Response
    {
        $ctr = new static();

        try{
            $body = self::getBody($req);
            $id = $args['id'];

            self::trataCadastro($body);
            $params = self::setParamsToInsert($body);
    
            $resp = $ctr->model->setUpdateProduct($id, $params);

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
            throw new \Exception("Valor do produto Inválido!");
        }

        #status
        if(isset($params->prd_status) && trim(!empty($params->prd_status))) {
            $stt = trim($params->prd_status) ?? 'ativo';
    
            if(in_array($stt, ['ativo', 'inativo']));
            $params->prd_status = $stt;
        } else {
            throw new \Exception("Status do produto Inválido!");
        }

        if(!isset($params->prd_id)) {
            $params->prd_data_inclusao = date("Y-m-d H:i:s");
        }
    }

    public static function storeMass(Request $req, Response $res, $args): Response
    {
        $ctr = new static();
        $file = UploadedFile::class;
        if(count($req->getUploadedFiles()) == 0){
            throw new \Exception('Arquivo [file] não enviado!');
        }   
        $file = $req->getUploadedFiles()['file'];
        
        $xlsService = new Reader();

        $xlsService->loadXls($file->getFilePath());

        $data = $xlsService->getData();

        $ctr->storeProds($data, $res);
        return $res;
    }


    public function storeProds(array $data, Response $res)
    {
        try{
            $params = $this->prepareDataImport($data);

            var_dump($params);

        } catch(\Exception $e) {
            return $this::getResponse($res, ['message' => $e->getMessage()], "403");
        }
    }

    /**
     * Retorna um array do tipo
     * 0 => [
     *  "prd_descricao"
"prd_valor"
"prd_status"
     * ]
     */
    private function prepareDataImport(array $data): array
    {
        $dataFormatted = [];
        foreach($data as $item) {
            $itemFormatted = [
                "prd_descricao" => $item[0],
                "prd_valor" => $item[1],
                "prd_status" => $item[2]
            ];

            array_push($dataFormatted, $itemFormatted);
        }
        return $dataFormatted;
    }
}