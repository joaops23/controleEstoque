<?php
namespace Routes;

use Psr\Http\Message\ServerRequestInterface as Request;

class RoutesDefault
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
}

?>