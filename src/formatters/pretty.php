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
 * Function rendering tree
 *
 * @param array $ast Abstract syntax Tree
 *
 * @return string
 */
function renderTreeToPretty($ast)
{
    $pretty = buildPrettyFormatOutput($ast, '  ');
    return "{\n{$pretty}\n}\n";
}

/**
 * Function rendering key state
 *
 * @param string $type state of node AST
 *
 * @return string rendered state
 */
function renderType($type)
{
    $renderedType = '';

    switch ($type) {
        case 'deleted':
            $renderedType = '- ';
            break;
        case 'added':
            $renderedType = '+ ';
            break;
        default:
            $renderedType = '  ';
            break;
    }

    return $renderedType;
}

/**
 * Function rendering array ti string
 *
 * @param array $data array for rendering
 *
 * @return string
 */
function arrayToString($data)
{
    $keys = array_keys($data);
    $countElements = count($keys);
    $resultString = array_reduce(
        $keys,
        function ($acc, $key) use ($data, &$countElements) {
            if ($countElements > 1) {
                $acc .= "{$key}: {$data[$key]},\n";
            } else {
                $acc .= "{$key}: {$data[$key]}";
            }
            $countElements--;
            return $acc;
        },
        ''
    );
    return $resultString;
}

/**
 * Function rendering tree
 *
 * @param array  $ast          Abstract syntax Tree
 * @param string $depthToSpace Count spaces
 *
 * @return string
 */
function buildPrettyFormatOutput($ast, $depthToSpace)
{
    $iter = function ($node, $depthToSpace, $acc) {
        switch ($node['type']) {
            case 'nested':
                $children = buildPrettyFormatOutput($node['children'], '      ');
                $acc = "{$depthToSpace}  {$node['name']}: {\n$children\n{$depthToSpace}  }";
                break;
            case 'changed':
                $beforeValue = boolToString($node['beforeValue']);
                $afterValue = boolToString($node['afterValue']);
                $acc = "{$depthToSpace}- {$node['name']}: {$beforeValue}\n";
                $acc .= "{$depthToSpace}+ {$node['name']}: {$afterValue}";
                break;
            case 'added' || 'deleted' || 'not_change':
                if (is_array($node['value'])) {
                    $strView = arrayToString($node['value']);
                    $type = renderType($node['type']);
                    $acc = "{$depthToSpace}{$type}{$node['name']}: {\n";
                    $acc .= "{$depthToSpace}      {$strView}\n{$depthToSpace}  }";
                } else {
                    // Если потомков нет и значение узла не массив
                    $newValue = boolToString($node['value']);
                    $type = renderType($node['type']);
                    $acc = "{$depthToSpace}{$type}{$node['name']}: {$newValue}";
                }
                break;
        }
        return $acc;
    };
    $renderingData = array_reduce(
        $ast,
        function ($iAcc, $iNode) use (&$iter, $depthToSpace) {
            $iAcc[] = $iter($iNode, $depthToSpace, '');
            return $iAcc;
        },
        []
    );
    $joined = implode("\n", $renderingData);
    return $joined;
}
