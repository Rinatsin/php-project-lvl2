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

use function Differ\boolToString;

/**
 * Function check value is a complex structure
 *
 * @param mixed $value value to check
 *
 * @return string
 */
function isComplex($value)
{
    if (is_array($value)) {
        $result = "'complex value'";
    } else {
        $result = "'{$value}'";
    }
    return $result;
}

/**
 * Function rendering AST (Abstract syntax tree)
 *
 * @param array $ast abstract syntax tree
 *
 * @return string return diff between two files in plain format
 */
function renderTreeToPlain($ast)
{
    $iter = function ($node, $path, $acc) use (&$iter) {
        switch ($node['type']) {
            case 'nested':
                $path = "{$node['name']}.";
                return array_reduce(
                    $node['children'],
                    function ($iAcc, $n) use (&$iter, $path) {
                        return $iter($n, $path, $iAcc);
                    },
                    $acc
                );
                break;
            case 'changed':
                $path .= "{$node['name']}";
                $beforeValue = boolToString($node['beforeValue']);
                $afterValue = boolToString($node['afterValue']);
                $acc .= "Property '{$path}' was changed. From ";
                $acc .= isComplex($beforeValue);
                $acc .= " to ";
                $acc .= isComplex($afterValue);
                $acc .= "\n";
                break;
            case 'deleted':
                $path .= "{$node['name']}";
                $acc .= "Property '{$path}' was removed\n";
                break;
            case 'added':
                $path .= "{$node['name']}";
                $acc .= "Property '{$path}' was added with value: ";
                $value = boolToString($node['value']);
                $acc .= isComplex($value);
                $acc .= "\n";
                break;
            default:
                break;
        }
        return $acc;
    };

    return array_reduce(
        $ast,
        function ($nAcc, $nCurrent) use (&$iter) {
                $nAcc .= $iter($nCurrent, '', '');
            return $nAcc;
        },
        ''
    );
}
