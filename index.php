<?php

/**
 * Created by PhpStorm.
 * User: vphucpham
 * Date: 1/7/17
 * Time: 11:13 PM
 */
    require_once(dirname(__FILE__) . '/v1/API.php');


    $method = $_SERVER['REQUEST_METHOD'];

    switch ($method) {
        case 'DELETE':
        case 'POST':
            $api = new API($method, $_POST);
            $api->getResponse();
            break;
        case 'GET':
            $api = new API($method, $_GET);
            $api->getResponse();

            break;
        case 'PUT':
            break;
        default:
            $api->getResponse(405);
            break;

    }

?>