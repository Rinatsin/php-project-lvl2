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
 * Function rendering output between two files to json format
 *
 * @param array $ast Abstract syntax tree to diff between two files
 *
 * @return json
 */
function renderTreeToJson($ast)
{
    $iter = function ($node) use (&$iter) {
        switch ($node['type']) {
            case 'nested':
                $childrens = array_reduce(
                    $node['children'],
                    function ($iAcc, $iNode) use (&$iter) {
                        $iAcc[] = $iter($iNode);
                        return $iAcc;
                    },
                    []
                );
                return [$node['name'] => $childrens];
                break;
            case 'changed':
                return[
                    [$node['name'] => $node['beforeValue'], 'type' => $node['type']],
                    [$node['name'] => $node['afterValue'], 'type' => $node['type']]
                ];
                break;
            case 'added' || 'deleted' || 'not_change':
                return [$node['name'] => $node['value'], "type" => $node['type']];
                break;
        }
    };
    return json_encode(
        array_reduce(
            $ast,
            function ($nAcc, $nNode) use (&$iter) {
                $nAcc[] = $iter($nNode);
                return $nAcc;
            },
            []
        )
    );
}
