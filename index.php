<?php

require_once './db/Database.php';
require_once './app/posts-api.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Methods: *');
header('Access-Control-Allow-Credentials: true');
header('Content-type: application/json');


function init(): void
{
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            PostsAPI::get();
            break;
        case 'POST':
            PostsAPI::post();
            break;

        case 'DELETE':
            PostsAPI::delete();
            break;

        case 'PATCH':
            PostsAPI::patch();
                break;
        default:

            break;
    }
}

init();