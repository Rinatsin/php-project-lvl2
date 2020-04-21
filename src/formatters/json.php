<?php

/**
 * Program compare two files and return their difference
 *
 * PHP version 7.3
 *
 * @category PHP
 * @package  Php-project-lvl2
 * @author   Rinat Salimyanov <rinatsin@gmail.com>
 * @license  https://github.com/Rinatsin/php-project-lvl2 MIT
 * @link     https://github.com/Rinatsin/php-project-lvl2
 */

 namespace Differ\Formatters;

/**
 * Function rendering output between two files to json format
 *
 * @param array $ast Abstract syntax tree to diff between two files
 *
 * @return json
 */
function renderTreeToJson($ast)
{
    return json_encode($ast);
}
