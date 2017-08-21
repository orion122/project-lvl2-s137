<?php

namespace DiffFinder\output;
/*
function output(array $AST, $spaces = '')
{
    $line = '';

    foreach ($AST as $array) {
        if ($array['type'] === 'nested') {
            $children = output($array['children'], '  ');
            $line .= "    \"{$array['key']}\": {\n$children    }\n";
        } elseif ($array['type'] === 'unchanged') {
            $value = boolToText($array['from']);
            $line .= "$spaces$spaces$spaces  \"{$array['key']}\": {$value}\n";
        } elseif ($array['type'] === 'removed') {
            if (is_array($array['from'])) {
                $from = unpackArray($array['from'], $spaces);
                $line .= "$spaces$spaces  - \"{$array['key']}\": {\n{$from}$spaces$spaces    }\n";
            } else {
                $value = boolToText($array['from']);
                $line .= "$spaces$spaces$spaces- \"{$array['key']}\": {$value}\n";
            }
        } elseif ($array['type'] === 'added') {
            if (is_array($array['from'])) {
                $from = unpackArray($array['from'], $spaces);
                $line .= "$spaces$spaces  + \"{$array['key']}\": {\n{$from}$spaces$spaces    }\n";
            } else {
                $value = boolToText($array['from']);
                $line .= "$spaces$spaces$spaces+ \"{$array['key']}\": {$value}\n";
            }
        } elseif ($array['type'] === 'changed') {
//            if (is_array($array['from'])) {
//                $from = unpackArray($array['from']);
//                $line .= "$spaces+ \"{$array['key']}\": {\n{$from}}\n";
//            } else {
            $value1 = boolToText($array['from']);
            $value2 = boolToText($array['to']);
            $line .= "$spaces$spaces$spaces- \"{$array['key']}\": {$value1}\n";
            $line .= "$spaces$spaces$spaces+ \"{$array['key']}\": {$value2}\n";
//            }
        }
    }

    return $line;
}


function unpackArray($array, $spaces) {
    return array_reduce(array_keys($array), function ($acc, $key) use ($array, $spaces) {
        if ($array[$key] !== null) {
            $acc .= "$spaces$spaces        \"{$key}\": \"{$array[$key]}\"\n";
            return $acc;
        }
    }, '');
}


function boolToText($value)
{
    if ($value === true) {
        return 'true';
    } elseif ($value === false) {
        return 'false';
    }
    return "\"$value\"";
}*/

function unpackArray($array, $depth) {
    $spaces = str_repeat(' ', $depth * 4 + 6);

    return array_reduce(array_keys($array), function ($acc, $key) use ($array, $depth, $spaces) {
        if (is_array($array[$key])) {
            $value = unpackArray($array[$key], $depth + 1);
            $acc .= "$spaces  {$key}:\n{$value}";
            return $acc;
        }
        $acc .= "$spaces  {$key}: {$array[$key]}\n";
        return $acc;
    }, '');
}


function output($AST, $depth = 0)
{
    $result = '';

    $spaces = str_repeat(' ', $depth * 4 + 2);

    foreach ($AST as $array) {
        if ($array['isNested'] === true) {
            if ($array['changeType'] === 'unchanged') {
                $value = unpackArray($array['from'], $depth);
                $result .= "$spaces  {$array['key']}:\n{$value}";
            } elseif ($array['changeType'] === 'changed') {
                $value = output($array['from'], 1);
                $result .= "$spaces  {$array['key']}:\n{$value}";
            } elseif ($array['changeType'] === 'removed') {
                $value = unpackArray($array['from'], $depth);
                $result .= "$spaces- {$array['key']}:\n{$value}";
            } elseif ($array['changeType'] === 'added') {
                $value = unpackArray($array['from'], $depth);
                $result .= "$spaces+ {$array['key']}:\n{$value}";
            }
        } else {
            if ($array['changeType'] === 'unchanged') {
                $result .= "$spaces  {$array['key']}: {$array['from']}\n";
            } elseif ($array['changeType'] === 'changed') {
                $result .= "$spaces- {$array['key']}: {$array['from']}\n";
                $result .= "$spaces+ {$array['key']}: {$array['to']}\n";
            } elseif ($array['changeType'] === 'removed') {
                $result .= "$spaces- {$array['key']}: {$array['from']}\n";
            } elseif ($array['changeType'] === 'added') {
                $result .= "$spaces+ {$array['key']}: {$array['from']}\n";
            }
        }
    }

    return $result;
}