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
    $result = buildAst2($before, $after);
    return $result;
}

/**
 * Function build ast
 *
 * @param array $before first file for diff
 * @param array $after  second file for diff
 *
 * @return array return ast
 */
function buildAst2($before, $after)
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
                        $iAcc[] = createNode($key, 'changed', 'node', '', $childs);
                    } else {
                        if ($before[$key] === $after[$key]) {
                            $iAcc[] = createNode($key, '  ', 'leaf', $before[$key], []);
                        } else {
                            $iAcc[] = createNode($key, '+ ', 'leaf', $after[$key], []);
                            $iAcc[] = createNode($key, '- ', 'leaf', $before[$key], []);
                        }
                    }
                } elseif (isset($before[$key])) {
                    if (is_array($before[$key])) {
                        $iAcc[] = createNode($key, '- ', 'node', $before[$key], []);
                    } else {
                        $iAcc[] = createNode($key, '- ', 'leaf', $before[$key], []);
                    }
                } elseif (isset($after[$key])) {
                    if (is_array($after[$key])) {
                        $iAcc[] = createNode($key, '+ ', 'node', $after[$key], []);
                    } else {
                        $iAcc[] = createNode($key, '+ ', 'leaf', $after[$key], []);
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
function createNode($name, $state, $type, $value, $childs)
{
    return [
      'name' => $name,
      'state' => $state,
      'type' => $type,
      'value' => $value,
      'children' => $childs
    ];
}
