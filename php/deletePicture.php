<?php
require_once("cookieEncrypt.php");
try
{
    //尝试连接数据库服务器
    $conn = new PDO("mysql:host=localhost:3306;dbname=pj2_travels;charset=utf8mb4;", "pj2", "531poiuy#");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //连接成功
    //检查这张图片是否是自己的
    //先判断GET中的imageID是不是自己的图片，否则不能有删除的权限
    if(isset($_GET['imageID']))
    {
        $sql = "
SELECT travelimage.ImageID, travelimage.UID
FROM travelimage
         INNER JOIN traveluser ON traveluser.UID = travelimage.UID
WHERE traveluser.UserName = :UserName
  AND travelimage.ImageID = :ImageID";
        $stmt = $conn->prepare($sql);
        $UserName = cookieDecrypt($_COOKIE['UserName']);
        $stmt->bindParam(':UserName', $UserName);
        $stmt->bindParam(':ImageID', $_GET['imageID']);
        $stmt->execute();
        while($row = $stmt->fetch())
        {
            $isThisPictureMine = true;
        }
    }
    if($isThisPictureMine)
    {
        $sql = 'DELETE FROM travelimage WHERE travelimage.ImageID=:imageID';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':imageID', $_GET['imageID']);
        $stmt->execute();
        $sql = 'DELETE FROM travelimagefavor WHERE travelimagefavor.ImageID=:imageID';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':imageID', $_GET['imageID']);
        $stmt->execute();
    }
    $conn = null;
    header('location:../myPhoto.php?' . $_SERVER['QUERY_STRING']);
}
catch(PDOException $e)
{
    //数据库连接失败
    echo $e->getMessage();
}