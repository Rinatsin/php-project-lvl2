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
 * Function get rendering ast and pass it to output
 *
 * @param array $ast rendered tree
 *
 * @return json
 */
function getJsonFormatOutput($ast)
{
    $renderedAst = renderTreeToJson($ast);
    $json = json_encode($renderedAst);
    $result = str_replace(["[", "},", "}]"], ["[\n", "},\n", "}\n]"], $json);
    return $result;
}

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
        $children = $node['children'] ?? null;

        if ($children) {
            $mapped = array_map(
                function ($nNode) use (&$iter) {
                    return $iter($nNode);
                },
                $children,
            );
            return [$node['name'] => $mapped];
        }
        return [$node['name'] => $node['value'], "state" => $node['state']];
    };

    return array_reduce(
        $ast,
        function ($iAcc, $iNode) use (&$iter) {
            $iAcc[] = $iter($iNode);
            return $iAcc;
        }
    );
}
