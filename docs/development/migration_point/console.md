# コンソールにおける注意点

```
<!-- CAKEPHP_SHELLが廃止されたため、単純にcliかでコンソールか判定(isConsole()) -->
return defined('CAKEPHP_SHELL') && CAKEPHP_SHELL;
↓
return substr(php_sapi_name(), 0, 3) == 'cli';
```