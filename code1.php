<?php

// 该文件测试用，未格式化/混乱的格式
$p1 = rand(1, 9);
if ($p1 == 9) { echo 'err';
}

echo
'ok'
;

switch ($p1) { case "x":
        $p1++;break;
    case "y":
    {$p1 += 2;
    }
    case "z":
        $p1 += 3;break;
}

if ($p1) echo 'xxxx'; else {
    echo 'SSS';
}



switch ($p1) {
    case "x":
        $p1++;break; case "z":
        $p1 += 3;
        break; default:
}

$i = 0;
if ($i++ < 9)

    $i = $i * 4 + 8;

