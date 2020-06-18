<?php
require_once("cookieEncrypt.php");
try
{
    //尝试连接数据库服务器
    $conn = new PDO("mysql:host=localhost:3306;dbname=pj2_travels;charset=utf8mb4;", "pj2", "531poiuy#");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //连接成功
    //获取UID
    $sql = "SELECT UID FROM traveluser WHERE UserName=:UserName";
    $stmt = $conn->prepare($sql);
    $UserName = cookieDecrypt($_COOKIE['UserName']);
    $stmt->bindParam(':UserName', $UserName);
    $stmt->execute();
    $row = $stmt->fetch();
    $UID = $row['UID'];
    //删除
    $sql = 'DELETE FROM travelimagefavor WHERE travelimagefavor.ImageID=:imageID AND travelimagefavor.UID=:UID';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':imageID', $_GET['imageID']);
    $stmt->bindParam(':UID', $UID);
    $stmt->execute();
    $conn = null;
    header('location:../favourite.php?' . $_SERVER['QUERY_STRING']);
}
catch(PDOException $e)
{
    //数据库连接失败
    echo $e->getMessage();
}