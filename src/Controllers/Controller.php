<?php

namespace Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class Controller
{

    public static function getBody(Request $req)
    {
        $body = (json_decode($req->getBody()));

        if(empty($body)) {
            throw new \Exception("Payload Inválido!");
        }

        $body = $body->data;

        return $body;
    }
    
    public static function getBodyNotRequired(Request $req)
    {
        $body = (json_decode($req->getBody()));

        $body = $body->data ?? null;

        return $body;
    }

    public static function getResponse(Response $res, $data, $status)
    {
        $response = $res->withStatus(intval($status))
            ->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($data));

        return $response;
    }

    public static function getOperations()
    {
        return  ["AND", 'OR', 'BETWEEN', 'LIKE', '=', 'like', "<>"];
    }

    # Métodos de apoio
    protected static function setParamsToFetch($paramsReq)
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

    protected static function formatVal($arrVal = array())
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

    protected static function setParamsToInsert($paramsReq)
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

    protected static function formatValToInsert($val)
    {
        $retVal = '';
        if(!in_array($val, static::getOperations())) {
            $retVal = "|sl" . $val . "|sl";
        } else {
            $retVal = $val;
        }

        return $retVal;
    }
}