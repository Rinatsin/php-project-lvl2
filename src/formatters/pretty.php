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

/**
 * Function rendering parse tree
 *
 * @param string $tree Ast tree
 *
 * @return string
 */
function getPrettyFormatOutput($tree)
{
    $renderingData = renderTreeToPretty($tree);
    return "{\n{$renderingData}}\n";
}

/**
 * Function rendering key state
 *
 * @param string $state state of node AST
 *
 * @return string rendered state
 */
function renderState($state)
{
    //$renderedState = '';

    switch ($state) {
        case 'changed_from':
            $renderedState = '- ';
            break;
        case 'changed_to':
            $renderedState = '+ ';
            break;
        case 'deleted':
            $renderedState = '- ';
            break;
        case 'added':
            $renderedState = '+ ';
            break;
        case 'not_change':
            $renderedState = '  ';
            break;
        default:
            throw new ErrorException('Unknown state of node');
    }

    return $renderedState;
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
        $children = $node['children'] ?? null;
        //Если у узла есть потомки то рекурсивно обрабатываем их
        if ($children) {
            //если количество потомков не установлено то устанавливаем
            if (!isset($childrenCount)) {
                $childrenCount = count($children);
            }
            $acc .= "{$depthToSpace}  {$node['name']}: {\n";
            return array_reduce(
                $children,
                function ($cAcc, $cNode) use (&$iter, $depthToSpace, &$childrenCount) {
                    $depthToSpace .= '    ';
                    return $iter($cNode, $depthToSpace, $childrenCount, $cAcc);
                },
                $acc
            );
        }
        //Если есть потомки, то уменьшаем их количество на одного
        if (isset($childrenCount)) {
            $childrenCount = $childrenCount - 1;
        }
        //Если потомков нет и значение ноды массив
        if (is_array($node['value'])) {
            $jsonView = json_encode($node['value']);
            $strView = str_replace(['{"', '":"', '","', '"}'], ['', ': ', "\n{$depthToSpace}"], $jsonView);
            $state = renderState($node['state']);
            $acc .= "{$depthToSpace}{$state}{$node['name']}: {\n";
            $depthToSpace .= '  ';
            $acc .= "{$depthToSpace}    {$strView}\n{$depthToSpace}}\n";
            //если потомков больше нет то ставим закрывающую скобку
            if ($childrenCount === 0) {
                $depthToSpace = '  ';
                $acc .= "  {$depthToSpace}}\n";
            }
            return $acc;
        }
        // Если потомков нет и значение узла не массив
        $newBoolValue = boolToString($node['value']);
        $state = renderState($node['state']);
        $acc .= "{$depthToSpace}{$state}{$node['name']}: {$newBoolValue}\n";
        // если больше потомков нет то уменьшаем отступ и ставим закрывающую скобку
        if ($childrenCount === 0) {
            $depthToSpace = '  ';
            $acc .= "  {$depthToSpace}}\n";
        }
        return $acc;
    };

    return array_reduce(
        $ast,
        function ($iAcc, $iNode) use (&$iter, $childrenCount) {
            $iAcc .= $iter($iNode, '  ', $childrenCount, '');
            return $iAcc;
        },
        ''
    );
}
