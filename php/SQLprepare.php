<?php
function SQLprepare(&$str)//SQL防注入
{
    $str = preg_replace("/(DELETE)|(INSERT)|(UPDATE)|(SELECT)|(DROP)|(DATABASE)|(#)/", " ", strtoupper($str));
    $str = str_replace("'", "''", $str);//单引号转义
}

