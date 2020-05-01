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

use function Funct\Collection\compact;

/**
 * Check value for complex and bool
 *
 * @param mixed $value value for check
 *
 * @return string value
 */
function stringifyValueForPlain($value)
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }
    if (is_array($value)) {
        return "'complex value'";
    }
    return "'{$value}'";
}

/**
 * Function rendering AST (Abstract syntax tree)
 *
 * @param array  $ast      abstract syntax tree
 * @param string $pathRoot path root
 *
 * @return string return diff between two files in plain format
 */
function renderTreeToPlain($ast, $pathRoot = null)
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
                    return renderTreeToPlain($node['children'], $node['name']);
                    break;
                case 'changed':
                    $beforeValue = stringifyValueForPlain($node['beforeValue']);
                    $afterValue = stringifyValueForPlain($node['afterValue']);
                    return "Property '{$path}' was changed. From {$beforeValue} to {$afterValue}";
                    break;
                case 'deleted':
                    return "Property '{$path}' was removed";
                    break;
                case 'added':
                    $value = stringifyValueForPlain($node['value']);
                    return "Property '{$path}' was added with value: {$value}";
                    break;
                case 'not_change':
                    return;
                default:
                    throw new ErrorException("Unknown type of node {$node['type']}");
            }
        },
        $ast
    );
    $filteredEmptyNodes = compact($mapped);

    return implode("\n", $filteredEmptyNodes);
}
