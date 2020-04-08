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
        if (is_bool($node['value'])) {
            $newValue = $node['value'] ? 'true' : 'false';
        } else {
            $newValue = $node['value'];
        }

        switch ($node['state']) {
            case 'changed_from':
                $acc .= "Property '{$path}' was changed. From ";
                $acc .= isComplex($newValue);
                break;
            case 'changed_to':
                $acc .= " to ";
                $acc .= isComplex($newValue);
                $acc .= "\n";
                break;
            case 'deleted':
                $acc .= "Property '{$path}' was removed\n";
                break;
            case 'added':
                $acc .= "Property '{$path}' was added with value: ";
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
