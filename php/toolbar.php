<?php
define("HOME", 1);
define("BROWSE", 2);
define("SEARCH", 3);
define("NONE", 4);
require_once("php/cookieEncrypt.php");
function showToolbar($status)
{
    echo "
<nav>
    <ul class='toolbar'>
        <li class='home'>
            <a href =" . ($status == HOME ? "javascript:" : "index.php") . "> 首页</a>
        </li>
        <li class='browse'>
            <a href = " . ($status == BROWSE ? "javascript:" : "browse.php") . "> 浏览</a>
        </li>
        <li class='search'>
            <a href = " . ($status == SEARCH ? "javascript:" : "search.php") . "> 搜索</a>
        </li>
        <li class='user'>" . (isset($_COOKIE["UserName"]) ? ("
            <a href = 'javascript:'>个人中心 <span class='dropdown_caret'></span></a>
            <ul class='menu'>
                <li>
                    <a href = 'upload.php'><img src = 'img/upload.png' alt = '1.'> 上传</a>
                </li>
                <li>
                    <a href = 'myPhoto.php'><img src = 'img/photo.png' alt = '2.'> 我的照片</a>
                </li>
                <li>
                    <a href = 'favourite.php'><img src = 'img/favorite.png' alt = '3.'> 我的收藏</a>
                </li>
                <li>
                    <a href = 'php/logout.php'><img src = 'img/exit.png' alt = '4.'> 登出</a>
                </li>
            </ul>") :
            ("<a href = 'login.php'>请登录</a>")) . "
        </li>
    </ul>
</nav>";
}

