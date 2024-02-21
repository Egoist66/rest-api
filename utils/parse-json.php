<?php

function parse_json(string $path, string $errorInfo): array{
    if(!file_exists($path)){
        die($errorInfo);
    }
    return json_decode(file_get_contents($path), true);
}