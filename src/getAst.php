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
function buildAstTree2($before, $after)
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
                            'type' => 'changed',
                            'children' => $children
                          ];
                    } else {
                        if ($before[$key] === $after[$key]) {
                            $iAcc[] = [
                                'name' => $key,
                                'type' => 'not_change',
                                'value' => $before[$key]
                              ];
                        } else {
                            $iAcc[] = [
                                'name' => $key,
                                'type' => 'changed_from',
                                'meta' => 'before',
                                'value' => $before[$key]
                              ];
                            $iAcc[] = [
                                'name' => $key,
                                'type' => 'changed_to',
                                'meta' => 'after',
                                'value' => $after[$key]
                              ];
                        }
                    }
                } elseif (isset($before[$key])) {
                    $iAcc[] = [
                        'name' => $key,
                        'type' => 'deleted',
                        'value' => $before[$key],
                      ];
                } elseif (isset($after[$key])) {
                    $iAcc[] = [
                        'name' => $key,
                        'type' => 'added',
                        'value' => $after[$key]
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
    $iter = function ($before, $after) use (&$iter) {
    
        $keys = union(array_keys($before), array_keys($after));

        $ast = array_map(
            function ($key) use ($before, $after, &$iter) {
                if (isset($before[$key]) && isset($after[$key])) {
                    if (is_array($before[$key]) && is_array($after[$key])) {
                        $children = $iter($before[$key], $after[$key]);
                        return [
                            'name' => $key,
                            'type' => 'nested',
                            'children' => $children
                        ];
                    } else {
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
                    }
                } elseif (isset($before[$key])) {
                    return [
                        'name' => $key,
                        'type' => 'deleted',
                        'value' => $before[$key]
                    ];
                } elseif (isset($after[$key])) {
                    return [
                        'name' => $key,
                        'type' => 'added',
                        'value' => $after[$key]
                    ];
                }
            },
            $keys
        );
        return $ast;
    };
    return $iter($before, $after);
}
