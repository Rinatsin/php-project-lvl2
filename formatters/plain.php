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
    $renderingData = '';

    foreach ($ast as $node) {
        $renderingData .= renderTreeToPlain($node);
    }
    return $renderingData;
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
    $iter = function ($node, $path, $acc, $parent) use (&$iter) {
        $children = $node['children'] ?? null;

        if ($children) {
            $path = "{$node['name']}.";
            return array_reduce(
                $children,
                function ($iAcc, $n) use (&$iter, $path, $children) {
                    return $iter($n, $path, $iAcc, $children);
                },
                $acc
            );
        }

        $path .= "{$node['name']}";
        if (contains($acc, $path)) {
            return $acc;
        } else {
            $delValue = findWhere($parent, ['name' => $node['name'], 'state' => '- ']);
            $addValue = findWhere($parent, ['name' => $node['name'], 'state' => '+ ']);
            if (isset($delValue) && isset($addValue)) {
                $acc .= "Property '{$path}' was changed. ";
                $acc .= "From ";
                $acc .= isComplex($delValue['value']);
                $acc .= " to ";
                $acc .= isComplex($addValue['value']);
                $acc .= "\n";
                return $acc;
            }
        }

        switch ($node['state'])
        {
        case '- ':
            $acc .= "Property '{$path}' was removed\n";
            return $acc;
            break;
        case '+ ':
            $acc .= "Property '{$path}' was added with value: ";
            $acc .= isComplex($node['value']);
            $acc .= "\n";
            return $acc;
            break;
        case '  ':
            return $acc;
            break;
        }
    };

    return $iter($ast, '', '', []);
}

/**
 * Function check value is a complex structure
 * 
 * @param mixed $value 
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