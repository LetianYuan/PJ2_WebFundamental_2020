<!DOCTYPE html>
<html lang="zh-cn">

<head>
    <title>首页</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/index.css">
</head>

<body>
<?php
require_once("php/toolbar.php");
showToolbar(HOME);
?>
<div class="float_button">
    <img src="img/refresh.png" title="刷新" id="refresh_button" class="refresh_button" alt="refresh">
    <script src="js/XHR.js"></script>
    <script>
        document.getElementById("refresh_button").onclick = function()
        {
            let xhr = createXHR();
            xhr.onreadystatechange = function()
            {
                if(xhr.readyState === 4)
                {
                    if((xhr.status >= 200 && xhr.status < 300) || xhr.status === 304)
                    {
                        document.getElementById("hot_image_area").innerHTML = xhr.responseText;
                    }
                    else
                    {
                        alert("Request was unsuccessful: " + xhr.status);
                    }
                }
            }
            let url = "php/home_randomPictures.php";
            xhr.open("get", url, true);
            xhr.send(null);
        }
    </script>
    <img src="img/back_to_top.png" title="回到页面顶部" id="back_to_top" class="back_to_top" alt="back_to_top">
    <script>
        var timer = null;
        back_to_top.onclick = function()
        {
            cancelAnimationFrame(timer);
            timer = requestAnimationFrame(function fn()
            {
                var oTop = document.body.scrollTop || document.documentElement.scrollTop;
                if(oTop > 0)
                {
                    document.body.scrollTop = document.documentElement.scrollTop = oTop - 50;
                    timer = requestAnimationFrame(fn);
                }
                else
                {
                    cancelAnimationFrame(timer);
                }
            });
        }
    </script>
</div>
<div class="head_image">
    <img class="head_image" src="img/home.jpg" alt="head_img">
    <p class="head_title">Welcome!</p>
</div>
<script async src="js/home_picture_box_appear.js"></script>

<div class="hot_image_area" id="hot_image_area">
    <?php
    try
    {
        //尝试连接数据库服务器
        $conn = new PDO("mysql:host=localhost:3306;dbname=pj2_travels;charset=utf8mb4;", "pj2", "531poiuy#");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        //连接成功
        $sql = "
SELECT travelimagefavor.ImageID,
       COUNT(travelimagefavor.ImageID) AS Favors,
       travelimage.Description,
       travelimage.Title,
       travelimage.PATH
FROM travelimagefavor
         LEFT JOIN travelimage
                   ON travelimage.ImageID = travelimagefavor.ImageID
GROUP BY travelimagefavor.ImageID
ORDER BY COUNT(travelimagefavor.ImageID) DESC";
        $result = $conn->query($sql);
        if($result->rowCount() > 0)
        {
            $row = $result->fetch();
            $i = 1;
            for(; $i <= 8; $row = $result->fetch(), $i++)
            {
                ?>
                <div class="hot_image">
                    <a href="picture_details.php?imageID=<?php echo $row["ImageID"] ?>">
                        <img src="img/travel_images/small/<?php echo $row["PATH"] ?>"
                             title="<?php echo $row["Title"] ?>"
                             alt="<?php echo $row["PATH"] ?>">
                    </a>
                    <p class="image_title"><?php echo $row["Title"] ?></p>
                    <p class="image_description"><?php if($row["Description"] != null) echo $row["Description"]; else echo "(作者很懒，什么都没写)"; ?></p>
                </div>

                <?php
            }
        }
        else
        {
            echo "数据库错误：未知";
        }
        $conn = null;
    }
    catch(PDOException $e)
    {
        //数据库连接失败
        echo $e->getMessage();
    }
    ?>
</div>
<footer class="copyright">
    <img src="img/weixin.jpg" alt="微信" title="联系我们">
    <span class="information">联系我们:19302010019@fudan.edu.cn<br/>
                              Copyright &copy;2020 Letian Yuan. All rights reserved.<br/>
                              (旦)-学习性-2020-0001&nbsp;&nbsp;&nbsp;旦公网安备19302010019号&nbsp;&nbsp;&nbsp;旦ICP证000019号</span>
</footer>
</body>
</html>