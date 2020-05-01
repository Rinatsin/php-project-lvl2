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
                    $depth += 1;
                    $indentationStep = '    ';
                    $addIndent = str_repeat($indentationStep, $depth);
                    $newIndent = $curIndent . $addIndent;
                    $children = buildPrettyFormatOutput($node['children'], $newIndent, $depth);
                    return "{$curIndent}  {$node['name']}: {\n$children\n{$curIndent}  }";
                    break;
                case 'changed':
                    $beforValue = stringifyPretty($node['beforeValue'], $curIndent);
                    $afterValue = stringifyPretty($node['afterValue'], $curIndent);
                    return "{$curIndent}- {$node['name']}: {$beforValue}\n{$curIndent}+ {$node['name']}: {$afterValue}";
                    break;
                case 'added' || 'deleted' || 'not_change':
                    $value = stringifyPretty($node['value'], $curIndent);
                    $type = renderType($node['type']);
                    return "{$curIndent}{$type}{$node['name']}: {$value}";
                    break;
            }
        },
        $ast
    );
    $joined = implode("\n", $rendered);
    return $joined;
}
