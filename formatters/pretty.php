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
 * Function rendering parse tree
 *
 * @param array $tree file to compare one
 *
 * @return string
 */
function renderTreeToPretty($tree)
{
    $childrenCount = null;

    $iter = function ($node, $depthToSpace, &$childrenCount, $acc) use (&$iter) {
        $children = $node['children'] ??  null;
        //если детей нет
        if (!$children) {
            if (isset($childrenCount)) {
                $childrenCount = $childrenCount - 1;
            }
            //если значение массив и нет потомков то добавляем как есть
            if ($node['type'] === 'node') {
                $jsonView = json_encode($node['value']);
                $strView = str_replace(['{"', '":"', '","', '"}'], ['', ': ', "\n{$depthToSpace}"], $jsonView);
                $acc .= "{$depthToSpace}{$node['state']}{$node['name']}: {\n";
                $depthToSpace .= '  ';
                $acc .= "{$depthToSpace}    {$strView}\n{$depthToSpace}}\n";
                //если потомков больше нет то ставим закрывающую скобку
                if ($childrenCount === 0) {
                    $depthToSpace = '  ';
                    $acc .= "  {$depthToSpace}}\n";
                }
                return $acc;
            }
            
            if (is_bool($node['value'])) {
                switch ($node['value']) {
                case true:
                    $node['value'] = 'true';
                    break;
                case false:
                    $node['value'] = 'false';
                    break;
                }
            }

            $acc .= "{$depthToSpace}{$node['state']}{$node['name']}: {$node['value']}\n";

            if ($childrenCount === 0) {
                $depthToSpace = '  ';
                $acc .= "  {$depthToSpace}}\n";
            }
            return $acc;
        }

        if (!isset($childrenCount)) {
            $childrenCount = count($children);
        }

        $acc .= "{$depthToSpace}  {$node['name']}: {\n";

        return array_reduce(
            $children,
            function ($cAcc, $n) use (&$iter, $depthToSpace, &$childrenCount) {
                $depthToSpace .= '    ';
                return $iter($n, $depthToSpace, $childrenCount, $cAcc);
            },
            $acc
        );
    };

    return array_reduce(
        $tree,
        function ($iAcc, $iNode) use (&$iter, $childrenCount) {
            $iAcc .= $iter($iNode, '  ', $childrenCount, '');
            return $iAcc;
        },
        ''
    );
}
