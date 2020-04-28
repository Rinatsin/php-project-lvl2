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

use ErrorException;

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
    $plain = buildPlainFormatOutput($ast);
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
        return "'complex value'";
    } else {
        return "'{$value}'";
    }
}

/**
 * Check value for complex and bool
 *
 * @param mixed $value value for check
 *
 * @return string value
 */
function stringify($value)
{
    $newValue = boolToString($value);
    return isComplex($newValue);
}

/**
 * Function rendering AST (Abstract syntax tree)
 *
 * @param array  $ast      abstract syntax tree
 * @param string $pathRoot path root
 *
 * @return string return diff between two files in plain format
 */
function buildPlainFormatOutput($ast, $pathRoot = null)
{
    $mapped = array_map(
        function ($node) use ($pathRoot) {
            if (isset($pathRoot)) {
                $pathParts[] = $pathRoot;
            }
            $pathParts[] = $node['name'];
            $path = implode('.', $pathParts);
            switch ($node['type']) {
                case 'nested':
                    return buildPlainFormatOutput($node['children'], $node['name']);
                    break;
                case 'changed':
                    $beforeValue = stringify($node['beforeValue']);
                    $afterValue = stringify($node['afterValue']);
                    return "Property '{$path}' was changed. From {$beforeValue} to {$afterValue}";
                    break;
                case 'deleted':
                    return "Property '{$path}' was removed";
                    break;
                case 'added':
                    $value = stringify($node['value']);
                    return "Property '{$path}' was added with value: {$value}";
                    break;
                case 'not_change':
                    return;
                    break;
                default:
                    throw new ErrorException("Unknown type of node {$node['type']}");
            }
        },
        $ast
    );
    $filteredEmptyNodes = array_filter(
        $mapped,
        function ($node) {
            if (!empty($node)) {
                return $node;
            }
        }
    );
    return implode("\n", $filteredEmptyNodes);
}
