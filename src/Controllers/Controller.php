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
}