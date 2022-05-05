<?php

// 引入处理程序
require_once './index.php';

// 将源代码转成tokens词法
$code = file_get_contents('./code1.php');
$tokens = sourceToTokens($code);

// 根据实际业务解析并生成新tokens
$tokens = fixIFBlock($tokens);
$tokens = fixSwitchDefault($tokens);

// 将新tokens反编码为源代码输出
$code = tokensToSource($tokens);
echo $code;
