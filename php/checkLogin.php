<?php
function check($url, $login)
{
    if(!isset($_COOKIE['UserName']))//检查是否登陆，否则直接转到登录界面
    {
        $expiryTime = time() + 60 * 60 * 24;
        setcookie("PreviousURL", $url, $expiryTime, "/");
        header("location:" . $login);
        return false;
    }
    return true;
}

