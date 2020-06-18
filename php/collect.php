<?php
require_once("cookieEncrypt.php");
require_once("checkLogin.php");
if(check('picture_details.php?imageID=' . $_GET['imageID'], '../login.php') && isset($_GET['imageID']))//如果用户没有登录，则以下的代码不会被执行
{
    try//如果用户登陆了
    {
        //尝试连接数据库服务器
        $conn = new PDO("mysql:host=localhost:3306;dbname=pj2_travels;charset=utf8mb4;", "pj2", "531poiuy#");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        //连接成功
        //获取用户UID
        $sql = "
SELECT UID
FROM traveluser
WHERE UserName = :UserName";
        $stmt = $conn->prepare($sql);
        $UserName = cookieDecrypt($_COOKIE['UserName']);
        $stmt->bindParam(':UserName', $UserName);
        $stmt->execute();
        $row = $stmt->fetch();
        $UID = $row['UID'];//如果黑客篡改cookie，这里会出错
        //检查用户是否已经收藏这个图片
        $sql = "
SELECT travelimagefavor.UID, travelimagefavor.ImageID
FROM travelimagefavor
WHERE travelimagefavor.UID = :UID AND travelimagefavor.ImageID = :ImageID";
        $stmt = $conn->prepare($sql);
        $UserName = cookieDecrypt($_COOKIE['UserName']);
        $stmt->bindParam(':UID', $UID);
        $stmt->bindParam(':ImageID', $_GET['imageID']);
        $stmt->execute();
        while($row = $stmt->fetch())
        {
            $isThisPictureCollected = true;
        }

        if($isThisPictureCollected)//如果已收藏，那么取消收藏
        {
            $sql = "
DELETE
FROM travelimagefavor
WHERE UID = :UID
  AND ImageID = :ImageID";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':UID', $UID);
            $stmt->bindParam(':ImageID', $_GET['imageID']);
            $stmt->execute();
        }
        else//否则收藏
        {
            $sql = "INSERT INTO travelimagefavor (UID, ImageID) VALUE (:UID, :ImageID)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':UID', $UID);
            $stmt->bindParam(':ImageID', $_GET['imageID']);
            $stmt->execute();
        }
        $conn = null;
        header('location:../picture_details.php?' . $_SERVER['QUERY_STRING']);
    }
    catch(PDOException $e)
    {
        //数据库连接失败
        echo $e->getMessage();
    }
}
?>