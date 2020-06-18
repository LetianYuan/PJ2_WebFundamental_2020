<!DOCTYPE html>
<html lang="zh-cn">

<head>
    <title>我的收藏</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/myPhoto.css">
</head>
<body>
<?php
require_once("php/checkLogin.php");
check('favourite.php', 'login.php');
require_once("php/toolbar.php");
require_once("php/cookieEncrypt.php");
showToolbar(NONE);
?>
<main>
    <ul class="container">
        <li class="title">
            我的收藏
        </li>
        <li class="content">
            <ul class="pictures">
                <?php
                try
                {
                //尝试连接数据库服务器
                $conn = new PDO("mysql:host=localhost:3306;dbname=pj2_travels;charset=utf8mb4;", "pj2", "531poiuy#");
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                //连接成功
                $UserName = cookieDecrypt($_COOKIE['UserName']);
                $sql = "
SELECT traveluser.UserName, travelimage.ImageID, travelimage.Title, travelimage.Description, travelimage.PATH
FROM traveluser
         RIGHT JOIN travelimagefavor ON traveluser.UID = travelimagefavor.UID
         RIGHT JOIN travelimage ON travelimage.ImageID = travelimagefavor.ImageID
WHERE traveluser.UserName = :UserName ";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':UserName', $UserName);
                $stmt->execute();
                $imageCount = 0;
                while($row = $stmt->fetch())
                {
                    $imageCount++;
                }
                if($imageCount > 0)
                {
                    $pageSize = 5;//一页上容纳的图片数量
                    //页数
                    $totalPage = (int)(($imageCount % $pageSize == 0) ? ($imageCount / $pageSize) : ($imageCount / $pageSize + 1));
                    if(!isset($_GET["page"]))
                    {
                        $currentPage = 1;
                    }
                    else
                    {
                        $currentPage = htmlspecialchars($_GET["page"]);
                        if($currentPage > $totalPage)//防止用户手动输入page
                            $currentPage = $totalPage;
                    }
                    $mark = ($currentPage - 1) * $pageSize;//当前页面第一张图片的位置
                    $sql .= "LIMIT " . $mark . "," . $pageSize;
                    $stmt = $conn->prepare($sql);
                    $stmt->bindParam(':UserName', $UserName);
                    $stmt->execute();
                    while($row = $stmt->fetch())
                    {
                        ?>
                        <li>
                            <a href="picture_details.php?imageID=<?php echo $row['ImageID'] ?>">
                                <img src="img/travel_images/small/<?php echo $row['PATH'] ?>"
                                     title="<?php echo $row['Title'] ?>"
                                     alt="<?php echo $row['PATH'] ?>">
                            </a>
                            <span class="image_right_area">
                                <span class="image_description">
                                    <strong class="image_title"><?php echo $row['Title'] ?></strong><br/>
                                    <?php echo $row['Description'] ?>
                                </span>
                                <br/>
                                <button id="pic_delete<?php echo $row['ImageID'] ?>" class="delete" value="删除">删除</button>
                                <script>
                                    document.getElementById('pic_delete<?php echo $row['ImageID'] ?>').onclick = function()
                                    {
                                        if(confirm('你确定要取消收藏这张图片吗？'))
                                        {
                                            window.location.href = 'php/cancelFavourite.php?imageID=<?php echo $row['ImageID'] ?>';
                                        }
                                    }
                                </script>
                            </span>
                        </li>
                        <?php
                    }
                }
                else
                {
                    ?>
                    <strong class="image_title" style="height: 190px">您还没有收藏任何图片。</strong>
                    <?php
                }
                ?>
            </ul>
            <?php
            function changePageTo($query, $page)
            {
                if(strpos($query, "page=") !== false)//如果有page参数
                    return preg_replace("/((?<=&page=)|(?<=^page=)).*?((?=&)|$)/i", $page, $query);
                else if(strlen($query) > 0)//如果有任何除page以外的参数
                    return $query . "&page=" . $page;
                else //如果没有任何参数
                    return "page=" . $page;
            }
            ?>
            <div class="page_number">
                <a href="favourite.php?<?php echo changePageTo($_SERVER["QUERY_STRING"], max(1, $currentPage - 1)); ?>"
                   title="向前一页"><<</a>
                <?php
                for($i = 1; $i <= $totalPage; $i++)
                {
                    ?>
                    <a <?php if($i == $currentPage) echo "class=\"active\"" ?>
                            href="favourite.php?<?php echo changePageTo($_SERVER["QUERY_STRING"], $i); ?>"
                            title="<?php echo $i ?>"><?php echo $i ?></a>
                    <?php
                }
                ?>
                <a href="favourite.php?<?php echo changePageTo($_SERVER["QUERY_STRING"], min($totalPage, $currentPage + 1)); ?>"
                   title="向后一页">>></a>
            </div>
            <?php
            $conn = null;
            }
            catch(PDOException $e)
            {
                //数据库连接失败
                echo $e->getMessage();
            }
            ?>
        </li>
    </ul>
</main>
<footer class="copyright">
    <span class="copyright">Copyright &copy;2020 Letian Yuan.
                            (旦)-学习性-2020-0001&nbsp;&nbsp;&nbsp;旦公网安备19302010019号&nbsp;&nbsp;&nbsp;旦ICP证000019号</span>
</footer>
</body>
</html>