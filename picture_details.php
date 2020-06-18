<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <title>图片详情</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/picture_details.css">
</head>
<body>
<?php
require_once("php/toolbar.php");
require_once("php/cookieEncrypt.php");
showToolbar(NONE);
?>

<?php
//首先查看是否有GET参数，没有则显示404
if(!isset($_GET['imageID']))
{
    echo "<h1 style='font-size: 80px'>404 Page Not Found</h1>";
}
else
{
    try
    {
        //尝试连接数据库服务器
        $conn = new PDO("mysql:host=localhost:3306;dbname=pj2_travels;charset=utf8mb4;", "pj2", "531poiuy#");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        //连接成功
        $sql = "
SELECT travelimage.Title,travelimage.Description,travelimage.PATH,travelimage.Content,travelimage.UID,
traveluser.UserName,geocountries.CountryName,geocities.AsciiName AS CityName,COUNT(travelimagefavor.ImageID) AS Favors
FROM travelimage
LEFT JOIN traveluser ON travelimage.UID=traveluser.UID
LEFT JOIN geocountries ON travelimage.CountryCodeISO=geocountries.ISO
LEFT JOIN geocities ON travelimage.CityCode=geocities.GeoNameID
LEFT JOIN travelimagefavor ON travelimage.ImageID=travelimagefavor.ImageID
WHERE travelimage.ImageID= :id";
        $stmt = $conn->prepare($sql);
        $imageID = htmlspecialchars($_GET["imageID"]);
        $stmt->bindParam(":id", $imageID);
        $stmt->execute();
        $row = $stmt->fetch();
        if($row['PATH'] != null)
        {
            ?>
            <div class="vague_border">
                <img src="img/travel_images/large/<?php echo $row['PATH'] ?>"
                     title="<?php echo $row['PATH'] ?>"
                     alt="<?php echo $row['PATH'] ?>"
                     class="picture">
            </div>
            <table class="picture_description">
                <caption>图片详情</caption>
                <tr>
                    <th>标题</th>
                    <td><?php echo $row['Title'] ?></td>
                </tr>
                <tr>
                    <th>作者</th>
                    <td><?php echo $row['UserName'] ?></td>
                </tr>
                <tr>
                    <th>主题</th>
                    <td><?php echo $row['Content'] ?></td>
                </tr>
                <tr>
                    <th>国家</th>
                    <td><?php echo $row['CountryName'] ?></td>
                </tr>
                <tr>
                    <th>城市</th>
                    <td><?php echo $row['CityName'] ?></td>
                </tr>
                <tr>
                    <th>描述</th>
                    <td><?php echo $row['Description'] ?></td>
                </tr>
            </table>
            <?php
            $favors = $row['Favors'];
            //查找我是否收藏了这张图片
            if(isset($_COOKIE['UserName']))
            {
                $sql = "
SELECT travelimagefavor.UID, travelimagefavor.ImageID
FROM travelimagefavor
         INNER JOIN traveluser ON traveluser.UID = travelimagefavor.UID
WHERE traveluser.UserName = :UserName AND travelimagefavor.ImageID = :ImageID";
                $stmt = $conn->prepare($sql);
                $UserName = cookieDecrypt($_COOKIE['UserName']);
                $stmt->bindParam(':UserName', $UserName);
                $stmt->bindParam(':ImageID', $_GET['imageID']);
                $stmt->execute();
                while($row = $stmt->fetch())
                {
                    $isThisPictureCollected = true;
                }
            }
            ?>
            <div class="interact">
                <a href="php/collect.php?<?php echo $_SERVER['QUERY_STRING'] ?>" class="collect" id="collect"><img src="img/empty.png" alt="收藏"
                                                                                                                   title="收藏">
                    <span id="collect_text"><?php if($isThisPictureCollected) echo "已" ?>收藏 <?php echo $favors ?></span></a>
            </div>
            <?php
        }
        else//图片不存在
        {
            echo "<h1 style='font-size: 80px'>Image Not Found</h1>";
        }
        $conn = null;
    }
    catch(PDOException $e)
    {
        //数据库连接失败
        echo $e->getMessage();
    }
}
?>
<footer class="copyright">
    <span class="copyright">Copyright &copy;2020 Letian Yuan.
                            (旦)-学习性-2020-0001&nbsp;&nbsp;&nbsp;旦公网安备19302010019号&nbsp;&nbsp;&nbsp;旦ICP证000019号</span>
</footer>
</body>
</html>