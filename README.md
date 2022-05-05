## php token解析
- 使用php函数```token_get_all```解析源代码，在根据业务规则对源码进行修改，生成新对规范性代码
- 该代码来源于修正 [https://www.sonarqube.org/](https://www.sonarqube.org/) 的检测结果
- 该代码仅作为想法，仅实现基础测试结构

## 用途
- 格式化代码：文件前后加空格，
- ```if```语句前后加花括号```{}```
- ```switch```语句加```default```块
- ```function```参数删除未使用变量
- 批量修改文件，增加/删除指定代码
- ...


## 测试
```php
php ./example.php
```


## 参考

[https://www.php.net/manual/en/tokenizer.examples.php](https://www.php.net/manual/en/tokenizer.examples.php)