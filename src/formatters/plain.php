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
 * Function rendering AST (Abstract syntax tree)
 *
 * @param array $ast abstract syntax tree
 *
 * @return string return diff between two files in plain format
 */
function renderTreeToPlain($ast)
{
    $plain = buildPlainFormatOutput($ast, null);
    return "$plain\n";
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
 * Function rendering AST (Abstract syntax tree)
 *
 * @param array  $ast      abstract syntax tree
 * @param string $pathRoot path root
 *
 * @return string return diff between two files in plain format
 */
function buildPlainFormatOutput($ast, $pathRoot)
{
    $iter = function ($node, $pathRoot, $acc) {
        if (isset($pathRoot)) {
            $pathParts[] = $pathRoot;
        }
        $pathParts[] = $node['name'];
        $path = implode('.', $pathParts);
        switch ($node['type']) {
            case 'nested':
                $pathRoot = $node['name'];
                $acc = buildPlainFormatOutput($node['children'], $pathRoot);
                break;
            case 'changed':
                $beforeValue = isComplex(boolToString($node['beforeValue']));
                $afterValue = isComplex(boolToString($node['afterValue']));
                $acc = "Property '{$path}' was changed. From {$beforeValue} to {$afterValue}";
                break;
            case 'deleted':
                $acc = "Property '{$path}' was removed";
                break;
            case 'added':
                $value = isComplex(boolToString($node['value']));
                $acc = "Property '{$path}' was added with value: {$value}";
                break;
            default:
                break;
        }
        return $acc;
    };

    $rendered = array_reduce(
        $ast,
        function ($nAcc, $nCurrent) use (&$iter, $pathRoot) {
            $temp = $iter($nCurrent, $pathRoot, '');
            if (!empty($temp)) {
                $nAcc[] = $iter($nCurrent, $pathRoot, '');
            }
            return $nAcc;
        },
        []
    );
    return implode("\n", $rendered);
}
