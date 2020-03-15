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

use function Differ\Parsers\parse;
use function Funct\Collection\union;
use function Funct\Invoke\ifIsset;

/**
 * Function compare two files and return their difference
 *
 * @param string $before file to compare one
 * @param string $after  file to compare two
 *
 * @return array
 */
function getAst($before, $after)
{
    $result = buildAst($before, $after);
    return $result;
}

/**
 * Function compare two files with tree structure and return their difference
 *
 * @param array $before file to compare one
 * @param array $after  file to compare two
 *
 * @return array
 */
function buildAst($before, $after)
{
    $keys = union(array_keys($before), array_keys($after));

    $iter = function ($before, $after, $keys, $acc) use (&$iter) {
        $ast = array_reduce(
            $keys,
            function ($iAcc, $key) use ($before, $after, &$iter) {
                if (isset($before[$key]) && isset($after[$key])) {
                    if (is_array($before[$key]) && is_array($after[$key])) {
                        $beforeChildKeys = array_keys($before[$key]);
                        $afterChildKeys = array_keys($after[$key]);
                        $childKeys = union($beforeChildKeys, $afterChildKeys);
                        $beforeChilds = $before[$key];
                        $afterChilds = $after[$key];
                        $childs = $iter($beforeChilds, $afterChilds, $childKeys, []);
                        $iAcc[] = [
                          'name' => $key,
                          'state' => 'changed',
                          'type' => 'node',
                          'children' => $childs
                        ];
                    } else {
                        if ($before[$key] === $after[$key]) {
                            $iAcc[] = [
                              'name' => $key,
                              'state' => '  ',
                              'type' => 'leaf',
                              'value' => $before[$key]
                            ];
                        } else {
                            $iAcc[] = [
                              'name' => $key,
                              'state' => '+ ',
                              'type' => 'leaf',
                              'value' => $after[$key]
                            ];
                            $iAcc[] = [
                              'name' => $key,
                              'state' => '- ',
                              'type' => 'leaf',
                              'value' => $before[$key]
                            ];
                        }
                    }
                } elseif (isset($before[$key]) && !isset($after[$key])) {
                    if (is_array($before[$key])) {
                        $iAcc[] = [
                          'name' => $key,
                          'state' => '- ',
                          'type' => 'node',
                          'value' => $before[$key]
                        ];
                    } else {
                        $iAcc[] = [
                          'name' => $key,
                          'state' => '- ',
                          'type' => 'leaf',
                          'value' => $before[$key]
                        ];
                    }
                } elseif (!isset($before[$key]) && isset($after[$key])) {
                    if (is_array($after[$key])) {
                        $iAcc[] = [
                          'name' => $key,
                          'state' => '+ ',
                          'type' => 'node',
                          'value' => $after[$key]
                        ];
                    } else {
                        $iAcc[] = [
                          'name' => $key,
                          'state' => '+ ',
                          'type' => 'leaf',
                          'value' => $after[$key]
                        ];
                    }
                }
                return $iAcc;
            },
            $acc
        );
        return $ast;
    };

    return $iter($before, $after, $keys, []);
}
