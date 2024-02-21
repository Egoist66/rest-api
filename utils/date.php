<?php

function current_time(array $options = array("time_zone" => "UTC", "format" => "H:i:s")): string
{
    date_default_timezone_set($options["time_zone"] ?? "UTC");
    $formatDate = $options["format"] ?? "H:i:s";

    return date($formatDate, time());
}
