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

 namespace Differ;

use function Funct\Collection\union;

/**
 * Function build ast
 *
 * @param array $before first file to diff
 * @param array $after  second file to diff
 *
 * @return array ast tree
 */
function buildAstTree($before, $after)
{
    $keys = union(array_keys($before), array_keys($after));
    $ast = array_map(
        function ($key) use ($before, $after) {
            if (!isset($before[$key])) {
                return [
                    'name' => $key,
                    'type' => 'added',
                    'value' => $after[$key]
                ];
            }
            if (!isset($after[$key])) {
                return [
                    'name' => $key,
                    'type' => 'deleted',
                    'value' => $before[$key]
                ];
            }
            if (is_array($before[$key]) && is_array($after[$key])) {
                $children = buildAstTree($before[$key], $after[$key]);
                return [
                    'name' => $key,
                    'type' => 'nested',
                    'children' => $children
                ];
            }
            if ($before[$key] === $after[$key]) {
                return [
                    'name' => $key,
                    'type' => 'not_change',
                    'value' => $before[$key]
                ];
            } else {
                return [
                    'name' => $key,
                    'type' => 'changed',
                    'beforeValue' => $before[$key],
                    'afterValue' => $after[$key]
                ];
            }
        },
        $keys
    );
    return $ast;
}
