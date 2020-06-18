<?php
try
{
    //尝试连接数据库服务器
    $conn = new PDO("mysql:host=localhost:3306;dbname=pj2_travels;charset=utf8mb4;", "pj2", "531poiuy#");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "
SELECT ImageID, Title, Description, PATH
FROM travelimage
ORDER BY rand()
LIMIT 8;";
    $result = $conn->query($sql);
    if($result->rowCount() > 0)
    {
        $row = $result->fetch();
        $i = 1;
        for(; $i <= 8; $row = $result->fetch(), $i++)
        {
            ?>
            <div class="hot_image f-up">
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
    return false;
}
catch(PDOException $e)
{
    //数据库连接失败
    echo $e->getMessage();
}