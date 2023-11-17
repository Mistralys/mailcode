<?php

declare(strict_types=1);

require_once __DIR__.'/prepend.php';

use Mailcode\Mailcode;
use function AppLocalize\pt;

?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?php pt('%1$s browser tools', Mailcode::getName()) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <link href="main.css" rel="stylesheet">
    <style>
        LI{
            padding-bottom:12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><?php pt('%1$s browser tools', Mailcode::getName()) ?></h1><br>
        <ul>
            <li>
                <a href="syntax-highlighter.php"><?php pt('Syntax highlighter'); ?></a><br>
                <?php pt('Syntax highlight a document with Mailcode commands') ?>
            </li>
            <li>
                <a href="translator.php"><?php pt('Syntax translator'); ?></a><br>
                <?php pt('Translate a document with Mailcode commands to a supported syntax'); ?>
            </li>
            <li>
                <a href="extractPhoneCountries.php"><?php pt('Phone countries extractor'); ?></a><br>
                Extracts a country list for the <code>showphone</code> command.
            </li>
        </ul>
    </div>
</body>
