<?php

function safe_db_config_init(string $path): void
{

    if (!file_exists($path)) {


        file_put_contents($path, json_encode([
            "host" => "localhost",
            "user" => "root",
            "password" => "",
            "db_name" => "api"
        ]));
    }
}