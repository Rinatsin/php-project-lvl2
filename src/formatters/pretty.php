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
        case 'not_change':
            $renderedType = '  ';
            break;
        default:
            throw new ErrorException('Unknown state of node');
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
    $resultString = array_reduce(
        $keys,
        function ($acc, $key) use ($data) {
            $acc .= "{$key}: {$data[$key]}";
            return $acc;
        },
        ''
    );
    return $resultString;
}

/**
 * Function rendering tree
 *
 * @param array $ast Abstract syntax Tree
 *
 * @return string
 */
function renderTreeToPretty($ast)
{
    $childrenCount = null;
    $iter = function ($node, $depthToSpace, &$childrenCount, $acc) use (&$iter) {
        
        if (isset($childrenCount)) {
            $childrenCount = $childrenCount - 1;
        }
        switch ($node['type']) {
            case 'nested':
                $childrenCount = count($node['children']);
                $acc .= "{$depthToSpace}  {$node['name']}: {\n";
                return array_reduce(
                    $node['children'],
                    function ($cAcc, $cNode) use (&$iter, $depthToSpace, &$childrenCount) {
                        $depthToSpace .= '    ';
                        return $iter($cNode, $depthToSpace, $childrenCount, $cAcc);
                    },
                    $acc
                );
                break;
            case 'changed':
                $beforeValue = boolToString($node['beforeValue']);
                $acc .= "{$depthToSpace}- {$node['name']}: {$beforeValue}\n";
                $afterValue = boolToString($node['afterValue']);
                $acc .= "{$depthToSpace}+ {$node['name']}: {$afterValue}\n";
                break;
            case 'added' || 'deleted' || 'no_change':
                if (is_array($node['value'])) {
                    $strView = arrayToString($node['value']);
                    $type = renderType($node['type']);
                    $acc .= "{$depthToSpace}{$type}{$node['name']}: {\n";
                    $depthToSpace .= '  ';
                    $acc .= "{$depthToSpace}    {$strView}\n{$depthToSpace}}\n";
                } else {
                    // Если потомков нет и значение узла не массив
                    $newValue = boolToString($node['value']);
                    $type = renderType($node['type']);
                    $acc .= "{$depthToSpace}{$type}{$node['name']}: {$newValue}\n";
                }
                break;
        }
        // если больше потомков нет то ставим закрывающую скобку
        if ($childrenCount === 0) {
            $depthToSpace = '  ';
            $acc .= "  {$depthToSpace}}\n";
        }
        return $acc;
    };
    $renderingData = array_reduce(
        $ast,
        function ($iAcc, $iNode) use (&$iter, $childrenCount) {
            $iAcc .= $iter($iNode, '  ', $childrenCount, '');
            return $iAcc;
        },
        ''
    );
    return "{\n{$renderingData}}\n";
}
