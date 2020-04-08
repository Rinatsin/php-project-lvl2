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

use function Funct\Collection\findWhere;
use function Funct\Strings\contains;

/**
 * Function rendering AST (Abstract syntax tree)
 *
 * @param array $ast abstract syntax tree
 *
 * @return string return diff between two files in plain format
 */
function getPlainFormatOutput($ast)
{
    return renderTreeToPlain($ast);
}

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
 * Function check the value is a bool
 *
 * @param bool $value Bool type value
 *
 * @return string
 */
function boolToString($value)
{
    $newValue = '';

    if (is_bool($value)) {
        switch ($value) {
            case true:
                $newValue = 'true';
                break;
            case false:
                $newValue = 'false';
                break;
        }
        return $newValue;
    } else {
        return $value;
    }
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
        $children = $node['children'] ?? null;

        if ($children) {
            $path = "{$node['name']}.";
            return array_reduce(
                $children,
                function ($iAcc, $n) use (&$iter, $path) {
                    return $iter($n, $path, $iAcc);
                },
                $acc
            );
        }

        $path .= "{$node['name']}";

        switch ($node['state']) {
            case 'changed_from':
                $acc .= "Property '{$path}' was changed. From ";
                $newValue = boolToString($node['value']);
                $acc .= isComplex($newValue);
                break;
            case 'changed_to':
                $acc .= " to ";
                $newValue = boolToString($node['value']);
                $acc .= isComplex($newValue);
                $acc .= "\n";
                break;
            case 'deleted':
                $acc .= "Property '{$path}' was removed\n";
                break;
            case 'added':
                $acc .= "Property '{$path}' was added with value: ";
                $newValue = boolToString($node['value']);
                $acc .= isComplex($newValue);
                $acc .= "\n";
                break;
            default:
                break;
        }
        return $acc;
    };

    return array_reduce(
        $ast,
        function ($nAcc, $nCurrent) use (&$iter, $ast) {
                $nAcc .= $iter($nCurrent, '', '');
            return $nAcc;
        },
        ''
    );
}
