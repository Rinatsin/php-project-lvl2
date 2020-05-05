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
 * Function rendering tree
 *
 * @param array $ast Abstract syntax Tree
 *
 * @return string
 */
function renderTreeToPretty($ast)
{
    $pretty = buildPrettyFormatOutput($ast);
    return "{\n{$pretty}\n}";
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
        case 'added':
            return '+ ';
        default:
            return '  ';
    }
}

/**
 * Function rendering array ti string
 *
 * @param array  $value  array for rendering
 * @param string $indent indentation
 *
 * @return string
 */
function stringifyPretty($value, $indent)
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }
    if (is_array($value)) {
        $keys = array_keys($value);
        $mapped = array_map(
            function ($key) use ($value, $indent) {
                return "      {$indent}{$key}: {$value[$key]}";
            },
            $keys
        );
        $joined = implode(",\n", $mapped);
        return "{\n{$joined}\n{$indent}  }";
    }
    return $value;
}

/**
 * Function rendering tree
 *
 * @param array   $ast   Abstract syntax Tree
 * @param integer $depth Count spaces
 *
 * @return string
 */
function buildPrettyFormatOutput($ast, $depth = 0)
{
    $baseIndent = '  ';
    $indentationStep = '    ';
    $addIndent = str_repeat($indentationStep, $depth);
    $indent = $baseIndent . $addIndent;
    $rendered = array_map(
        function ($node) use ($depth, $indent) {
            switch ($node['type']) {
                case 'nested':
                    $depth += 1;
                    $children = buildPrettyFormatOutput($node['children'], $depth);
                    return "{$indent}  {$node['name']}: {\n$children\n{$indent}  }";
                case 'changed':
                    $beforValue = stringifyPretty($node['beforeValue'], $indent);
                    $afterValue = stringifyPretty($node['afterValue'], $indent);
                    return "{$indent}- {$node['name']}: {$beforValue}\n{$indent}+ {$node['name']}: {$afterValue}";
                case 'added' || 'deleted' || 'not_change':
                    $value = stringifyPretty($node['value'], $indent);
                    $type = renderType($node['type']);
                    return "{$indent}{$type}{$node['name']}: {$value}";
            }
        },
        $ast
    );
    $joined = implode("\n", $rendered);
    return $joined;
}
