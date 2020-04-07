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
    $iter = function ($before, $after, $acc) use (&$iter) {
        $keys = union(array_keys($before), array_keys($after));
        $ast = array_reduce(
            $keys,
            function ($iAcc, $key) use ($before, $after, &$iter) {
                if (isset($before[$key]) && isset($after[$key])) {
                    if (is_array($before[$key]) && is_array($after[$key])) {
                        $children = $iter($before[$key], $after[$key], []);
                        $iAcc[] = [
                            'name' => $key,
                            'state' => 'changed',
                            'type' => 'node',
                            'value' => '',
                            'children' => $children
                          ];
                    } else {
                        if ($before[$key] === $after[$key]) {
                            $iAcc[] = [
                                'name' => $key,
                                'state' => 'not_change',
                                'type' => 'leaf',
                                'value' => $before[$key],
                                'children' => []
                              ];
                        } else {
                            $iAcc[] = [
                                'name' => $key,
                                'state' => 'added',
                                'type' => 'leaf',
                                'value' => $after[$key],
                                'children' => []
                              ];
                            $iAcc[] = [
                                'name' => $key,
                                'state' => 'deleted',
                                'type' => 'leaf',
                                'value' => $before[$key],
                                'children' => []
                              ];
                        }
                    }
                } elseif (isset($before[$key])) {
                    $iAcc[] = [
                        'name' => $key,
                        'state' => 'deleted',
                        'type' => 'leaf',
                        'value' => $before[$key],
                        'children' => []
                      ];
                } elseif (isset($after[$key])) {
                    $iAcc[] = [
                        'name' => $key,
                        'state' => 'added',
                        'type' => 'leaf',
                        'value' => $after[$key],
                        'children' => []
                      ];
                }
                return $iAcc;
            },
            $acc
        );
        return $ast;
    };

    return $iter($before, $after, []);
}
