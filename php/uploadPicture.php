<style>
    body
    {
        text-align: center;
    }
</style>

<?php
require_once('cookieEncrypt.php');
function generateImageName()
{
    $time = date('YmdHis');
    $user = cookieDecrypt($_COOKIE['UserName']);
    return $user . '-' . $time . '.' . explode('/', $_FILES["picture"]["type"])[1];
}
function getCompressedSize()
{
    if($_FILES["picture"]["size"] <= 81902)
    {
        return 1;
    }
    else if($_FILES["picture"]["size"] <= 204800)
    {
        return 0.8;
    }
    else if($_FILES["picture"]["size"] <= 512000)
    {
        return 0.5;
    }
    else if($_FILES["picture"]["size"] <= 1048576)
    {
        return 0.4;
    }
    else if($_FILES["picture"]["size"] <= 2097152)
    {
        return 0.3;
    }
    else if($_FILES["picture"]["size"] <= 4194304)
    {
        return 0.2;
    }
    else
    {
        return 0.15;
    }
}
function updateDatabase($PATH)
{
    try
    {
        //尝试连接数据库服务器
        $conn = new PDO("mysql:host=localhost:3306;dbname=pj2_travels;charset=utf8mb4;", "pj2", "531poiuy#");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        //连接成功
        $sql = "SELECT ISO From geocountries WHERE CountryName=:country";//获取ISO
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':country', $_POST['country']);
        $stmt->execute();
        $row = $stmt->fetch();
        $CountryCodeISO = $row['ISO'];
        //获取cityCode
        $sql = "SELECT GeoNameID, Latitude, Longitude From geocities WHERE AsciiName=:city AND CountryCodeISO=:ISO";//获取CityCode，Latitude，Longitude
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':city', $_POST['city']);
        $stmt->bindParam(':ISO', $CountryCodeISO);
        $stmt->execute();
        $row = $stmt->fetch();
        $cityCode = $row['GeoNameID'];
        $latitude = $row['Latitude'];
        $longitude = $row['Longitude'];
        //获取UID
        $sql = "SELECT UID FROM traveluser WHERE UserName=:UserName";
        $stmt = $conn->prepare($sql);
        $UserName = cookieDecrypt($_COOKIE['UserName']);
        $stmt->bindParam(':UserName', $UserName);
        $stmt->execute();
        $row = $stmt->fetch();
        $UID = $row['UID'];
        $sql = "INSERT INTO travelimage(Title, Description, Latitude, Longitude, CityCode, CountryCodeISO, UID, PATH, Content)
VALUES (:Title, :Description, :Latitude, :Longitude, :CityCode, :CountryCodeISO, :UID, :PATH, :Content)";//插入
        $stmt = $conn->prepare($sql);
        $title = htmlspecialchars($_POST['title']);//防XSS攻击
        $description = htmlspecialchars($_POST['description']);
        $content = htmlspecialchars($_POST['content']);
        $stmt->bindParam(':Title', $title);
        $stmt->bindParam(':Description', $description);
        $stmt->bindParam(':Latitude', $latitude);
        $stmt->bindParam(':Longitude', $longitude);
        $stmt->bindParam(':CityCode', $cityCode);
        $stmt->bindParam(':CountryCodeISO', $CountryCodeISO);
        $stmt->bindParam(':UID', $UID);
        $stmt->bindParam(':PATH', $PATH);
        $stmt->bindParam(':Content', $content);
        $stmt->execute();
        $conn = null;
    }
    catch(PDOException $e)
    {
        //数据库连接失败
        echo $e->getMessage() . '<br/>';
        return false;
    }
    return true;
}

if($_POST['title'] != '' && $_POST['content'] != 'none' && $_POST['country'] != 'none' && $_POST['city'] != 'none' && $_POST['description'] != '')
{
    // 允许上传的图片后缀
    $allowedExts = array("gif", "jpeg", "jpg", "png");
    $temp = explode(".", $_FILES["picture"]["name"]);
    $extension = end($temp);        // 获取文件后缀名
    if((($_FILES["picture"]["type"] == "image/gif") || ($_FILES["picture"]["type"] == "image/jpeg") || ($_FILES["picture"]["type"] == "image/jpg") || ($_FILES["picture"]["type"] == "image/png"))
        && ($_FILES["picture"]["size"] <= 10485760)    // 小于 10 mb
        && in_array($extension, $allowedExts))
    {
        if($_FILES["picture"]["error"] > 0)
        {
            echo "<h1>错误: " . $_FILES["picture"]["error"] . "</h1>";
        }
        else
        {
            // 判断当前目录下的 img 目录是否存在该文件
            $imageName = generateImageName();
            if(file_exists("img/travel_images/large/" . $imageName))//这里不能写成 "../img" ，非常神奇
            {
                echo '<h1>上传错误，请重试。</h1>';
            }
            else//上传文件
            {
                move_uploaded_file($_FILES["picture"]["tmp_name"], "img/travel_images/large/" . $imageName);
                require_once('imageCompress.php');
                //压缩图片并保存到small中
                $imgCompress = new ImgCompress("img/travel_images/large/" . $imageName, getCompressedSize());
                $imgCompress->compressImg("img/travel_images/small/" . $imageName);
                if(updateDataBase($imageName))
                {
                    echo "<h1>上传成功！</h1>";
                    $success = true;
                }
                else
                {
                    echo "<h1>未知的数据库错误。</h1>";
                }

            }
        }
    }
    else
    {
        echo "<h1>非法的文件格式(.jpg, .gif, .jpeg, .png)或大小(<=10MB)</h1>";
    }
}
else
{
    echo "<h1>请填写完整图片信息。</h1>";
}
if($success)//跳转到我的照片
{
    ?>
    <p>提示：<strong id="number" style="color:red">5</strong> 秒后跳转至“我的照片”。</p>
    <script>
        let time = 5;
        let interval = null;

        function changeNumber()
        {
            time--;
            document.getElementById('number').textContent = time;
            if(time === 0)
            {
                clearInterval(interval);
                location.href = '../myPhoto.php';
            }
        }

        interval = setInterval(changeNumber, 1000);
    </script>
    <?php
}
else//跳转到上传界面
{
    ?>
    <p>提示：<strong id="number" style="color:red">5</strong> 秒后回到“上传界面”。</p>
    <script>
        let time = 5;
        let interval = null;

        function changeNumber()
        {
            time--;
            document.getElementById('number').textContent = time;
            if(time === 0)
            {
                clearInterval(interval);
                location.href = '../upload.php';
            }
        }

        interval = setInterval(changeNumber, 1000);

    </script>
    <?php
}
?>