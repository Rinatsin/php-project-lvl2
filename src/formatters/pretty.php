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
    $pretty = buildPrettyFormatOutput($ast);
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
    switch ($type) {
        case 'deleted':
            return '- ';
            break;
        case 'added':
            return '+ ';
            break;
        default:
            return '  ';
            break;
    }
}

/**
 * Function rendering array ti string
 *
 * @param array $data array for rendering
 *
 * @return string
 */
function joinCollection($data)
{
    $keys = array_keys($data);
    $result = array_map(
        function ($key) use ($data) {
            return "{$key}: {$data[$key]}";
        },
        $keys
    );
    return implode(",\n", $result);
}

/**
 * Function translate depth to spaces
 *
 * @param integer $depth depth of recursion
 *
 * @return string spaces
 */
function depthToIndentation($depth, $baseIndentation)
{
    $indentation = $baseIndentation;
    $indentationStep = '    ';
    while ($depth > 0) {
        $indentation .= $indentationStep;
        $depth--;
    }
    return $indentation;
}

/**
 * Function rendering tree
 *
 * @param array   $ast       Abstract syntax Tree
 * @param string  $curIndent Indentation
 * @param integer $depth     Count spaces
 *
 * @return string
 */
function buildPrettyFormatOutput($ast, $curIndent = '  ', $depth = 0)
{
    $rendered = array_map(
        function ($node) use ($depth, $curIndent) {
            switch ($node['type']) {
                case 'nested':
                    $depth++;
                    $indent = depthToIndentation($depth, $curIndent);
                    $children = buildPrettyFormatOutput($node['children'], $indent, $depth);
                    return "{$curIndent}  {$node['name']}: {\n$children\n{$curIndent}  }";
                    break;
                case 'changed':
                    $beforValue = boolToString($node['beforeValue']);
                    $afterValue = boolToString($node['afterValue']);
                    return "{$curIndent}- {$node['name']}: {$beforValue}\n{$curIndent}+ {$node['name']}: {$afterValue}";
                    break;
                case 'added' || 'deleted' || 'not_change':
                    if (is_array($node['value'])) {
                        $value = joinCollection($node['value']);
                        $type = renderType($node['type']);
                        return "{$curIndent}{$type}{$node['name']}: {\n{$curIndent}      {$value}\n{$curIndent}  }";
                    } else {
                        $newValue = boolToString($node['value']);
                        $type = renderType($node['type']);
                        return "{$curIndent}{$type}{$node['name']}: {$newValue}";
                    }
                    break;
            }
        },
        $ast
    );//sdfsdf fdsfsdf    sdfsd   d   d   d  d  d

    $joined = implode("\n", $rendered);
    return $joined;
}
