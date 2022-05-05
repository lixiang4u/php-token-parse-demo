<?php

/**
 * 解析源代码数据code成tokens
 *
 * @param $code
 * @return array
 */
function sourceToTokens($code)
{
    $tokens = token_get_all($code);
    foreach ($tokens as &$token) {
        if (is_array($token)) {
            $token[] = token_name($token[0]);
        }
    }
    unset($token);
    return $tokens;
}

/**
 * 根据tokens反编码成源代码 https://www.php.net/manual/en/tokenizer.examples.php
 *
 * @param $tokens
 * @return string
 */
function tokensToSource($tokens)
{
    $str = '';
    foreach ($tokens as $token) {
        if (is_string($token)) {
            // simple 1-character token
            $str .= $token;
            continue;
        }
        // token array
        //[$id, $text] = $token;
        $id = $token[0];
        $text = $token[1];
        switch ($id) {
            case T_COMMENT:
            case T_DOC_COMMENT:
                // no action on comments
                break;
            default:
                // anything else -> output "as is"
                $str .= $text;
                break;
        }
    }
    return $str;
}

/**
 * 根据tokens，修正switch语句中default块
 *
 * @param $tokens
 * @return mixed
 */
function fixSwitchDefault($tokens)
{
    $findList = [];
    $defaultValue = [false, -1, -1, -1, -1, -1];// find?，switch_start_index，switch_end_index，left_brancg_count, right_brancg_count, break_index
    $current = $defaultValue;
    foreach ($tokens as $index => $token) {
        if (is_array($token) && $token[3] == 'T_SWITCH') {
            $current[0] = true;
            $current[1] = $index;
        }
        if ($current[1] < 0) {
            continue;
        }
        if ($token == '{') {
            $current[3]++;
        }
        if ($token == '}') {
            $current[4]++;
        }
        if ($current[3] > 0 && $current[3] == $current[4]) {
            // 结束
            $current[2] = $index;

            // 当前轮查找结束
            $findList[] = $current;
            $current = $defaultValue;
        }
        if (is_array($token) && $token[3] == 'T_DEFAULT') {
            $current[5] = $index;
        }
    }
    if (empty($findList)) {
        return $tokens;
    }
    foreach (array_reverse($findList) as $item) {
        if ($item[5] > 0) {
            // 存在break语句
            continue;
        }
        array_splice($tokens, $item[2], 0, [T_WHITESPACE, "\n", 19, '']);
        array_splice($tokens, $item[2], 0, [':']);
        array_splice($tokens, $item[2], 0, [T_DEFAULT, 'default', 19, '']);
    }
    return $tokens;
}

/**
 * 根据tokens，修正if语句后圆括号问题
 *
 * @param $tokens
 * @return mixed
 */
function fixIFBlock($tokens)
{
    $findList = [];
    $defaultValue = [false, -1, -1, -1];// find?，if_start_index，if_end_index，if_right_brancg_index
    $current = $defaultValue;
    foreach ($tokens as $index => $token) {
        if (is_array($token) && $token[3] == 'T_IF') {
            $current[1] = $index;
        }
        if ($current[1] < 0 || !is_string($token)) {
            continue;
        }
        if ($token == ')') {
            $current[3] = $index;
        }
        if ($token == '{') {
            $current[0] = true;
        }
        if ($token == ';') {
            $current[2] = $index;

            // 当前轮查找结束
            $findList[] = $current;
            $current = $defaultValue;
        }
    }
    if (empty($findList)) {
        return $tokens;
    }

    foreach (array_reverse($findList) as $item) {
        if ($item[0]) {
            continue;
        }
        array_splice($tokens, $item[2] + 1, 0, ['}']);
        array_splice($tokens, $item[3] + 1, 0, '{');
    }
    return $tokens;
}
