<?php

function sanitize_input(string $input): string
{
    $input = htmlspecialchars($input);
    $input = trim($input);
    return strip_tags($input);
}