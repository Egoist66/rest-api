<?php

function parse_url_string(): array {
    
    $path = explode('/', $_GET['q']);
    $params = [
        "url" => $path[0],
        "id" => $path[1] ?? ''
    ];

    return $params;


}