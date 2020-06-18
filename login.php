<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <title>登陆</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
<?php
define("FAILED", true);
define("NOT_FAILED", false);
function getLoginForm($Failed)
{
    return "<form action='' method='post' role='form'>
    <fieldset" . ($Failed ? " style='height:400px'" : "") . ">
<legend>登陆</legend>
<section>
    <label for='user_name'>用户名:</label>
    <br/>
    <input type='text' id='user_name' name='UserName' required>
    <br/>
    <label for='password'>密码:</label>
    <br/>
    <input type='password' id='password' name='Password' required>
    <br/>
    " . ($Failed ? "<p class='warn'>用户名或密码错误，请重试</p>" : "") . "
    <input type='submit' value='登陆'>
    <br/>    
    <p>没有账号? <a href='register.php'>注册一个!</a></p>
</section>
</fieldset>
</form>";
}
function isValidLogin()
{
    try
    {
        //尝试连接数据库服务器
        $conn = new PDO("mysql:host=localhost:3306;dbname=pj2_travels;charset=utf8mb4;", "pj2", "531poiuy#");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "SELECT Salt FROM traveluser WHERE UserName=:UserName";
        $statement = $conn->prepare($sql);
        $userName = $_POST['UserName'];
        $statement->bindValue(':UserName', $userName);
        $statement->execute();
        if($statement->rowCount() > 0)
        {
            $row = $statement->fetch();
            $salt = $row['Salt'];
        }
        else
        {
            return false;
        }
        $sql = "SELECT * FROM traveluser WHERE UserName=:UserName and Pass=:Password";
        $statement = $conn->prepare($sql);
        $password = hash("sha256", $_POST['Password'] . $userName . $salt);//MD5加密是不安全的，因此用SHA256
        $statement->bindValue(':UserName', $userName);
        $statement->bindValue(':Password', $password);
        $statement->execute();
        if($statement->rowCount() > 0)
        {
            $conn = null;
            return true;
        }
        $conn = null;
        return false;
    }
    catch(PDOException $e)
    {
        //数据库连接失败
        echo $e->getMessage();
    }
    return false;
}

require_once("php/cookieEncrypt.php");
if(isset($_COOKIE['UserName']))//如果已经登录
{
    header('location:index.php');
}
if(isset($_POST['UserName']))//如果表单里有信息，说明正在尝试登陆
{
    if($_SERVER["REQUEST_METHOD"] == "POST")
    {
        if(isValidLogin())//登陆成功
        {
            $expiryTime = time() + 60 * 60 * 24;
            setcookie("UserName", cookieEncrypt($_POST['UserName']), $expiryTime, "/");//稍微做一些base64加密，防止被轻易破解cookie
            if(isset($_COOKIE['PreviousURL']))
            {
                setcookie("PreviousURL", "", -1, "/");
                header("location:" . $_COOKIE['PreviousURL']);
            }
            else
            {
                header("location:index.php");
            }
        }
        else
        {
            echo getLoginForm(FAILED);//登陆失败，显示错误信息
        }
    }
}
else
{
    echo getLoginForm(NOT_FAILED);
}
?>

<footer class="copyright">
    <span class="copyright">Copyright &copy;2020 Letian Yuan.
                            (旦)-学习性-2020-0001&nbsp;&nbsp;&nbsp;旦公网安备19302010019号&nbsp;&nbsp;&nbsp;旦ICP证000019号</span>
</footer>

</body>
</html>