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
 * Function create new node
 *
 * @param string $name   node name
 * @param string $state  node state (added, deleted or no changed)
 * @param string $type   node type (node or leaf)
 * @param string $value  node value
 * @param array  $childs childs current node
 *
 * @return array return node of ast
 */
function createNode($name, $state, $type, $value, $children)
{
    return [
      'name' => $name,
      'state' => $state,
      'type' => $type,
      'value' => $value,
      'children' => $children
    ];
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
    $iter = function ($before, $after, $acc) use (&$iter) {
        $keys = union(array_keys($before), array_keys($after));
        $ast = array_reduce(
            $keys,
            function ($iAcc, $key) use ($before, $after, &$iter) {
                if (isset($before[$key]) && isset($after[$key])) {
                    if (is_array($before[$key]) && is_array($after[$key])) {
                        $children = $iter($before[$key], $after[$key], []);
                        $iAcc[] = createNode($key, 'changed', 'node', '', $children);
                    } else {
                        if ($before[$key] === $after[$key]) {
                            $iAcc[] = createNode($key, 'not_change', 'leaf', $before[$key], []);
                        } else {
                            $iAcc[] = createNode($key, 'added', 'leaf', $after[$key], []);
                            $iAcc[] = createNode($key, 'deleted', 'leaf', $before[$key], []);
                        }
                    }
                } elseif (isset($before[$key])) {
                    $iAcc[] = createNode($key, 'deleted', 'leaf', $before[$key], []);
                } elseif (isset($after[$key])) {
                    $iAcc[] = createNode($key, 'added', 'leaf', $after[$key], []);
                }
                return $iAcc;
            },
            $acc
        );
        return $ast;
    };

    return $iter($before, $after, []);
}
