<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <title>注册</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/register.css">
</head>
<body>
<?php
define("SUCCESS", 1);
define("INVALID_USERNAME", 2);
define("DUPLICATED_USERNAME", 3);
define("INCONSISTENT_PASS", 4);
define("INVALID_MAIL", 5);
define("WEAK_PASS", 6);
function getRegisterForm($status)
{
    switch($status)
    {
        case INVALID_USERNAME:
            $warning = "不合法的用户名(<=20且>=4个字符)";
            break;
        case DUPLICATED_USERNAME:
            $warning = "用户名已存在";
            break;
        case INCONSISTENT_PASS:
            $warning = "两次输入的密码不一致";
            break;
        case INVALID_MAIL:
            $warning = "无效的邮箱";
            break;
        case WEAK_PASS:
            $warning = "密码太弱(>=6位且必须有大写、小写字母及数字)";
            break;
        default:
            $warning = "未知的错误";
    }
    return "<form action='' method='post' role='form'>
    <fieldset" . ($status != SUCCESS ? " style='height:550px'" : "") . ">
<legend>注册</legend>
<section>
    <label for='user_name'>用户名:</label>
    <br/>
    <input type='text' id='user_name' name='UserName' required>
    <br/>
    <label for=\"email\">邮箱:</label>
    <br/>
    <input type=\"email\" id=\"email\" name='Mail' required>
    <br/>
    <label for='password'>密码:</label>
    <br/>
    <input type='password' id='password' name='Password' required>
    <br/>
    <label for='password_confirm'>确认密码:</label>
    <br/>
    <input type='password' id='password_confirm' name='PasswordConfirm' required>
    <br/>
    " . ($status != SUCCESS ? "<p class='warn'>" . $warning . "</p>" : "") . "
    <input type='submit' value='注册'>
    <br/>    
    <p>已有账号? <a href=\"login.php\">去登陆吧!</a></p>
</section>
</fieldset>
</form>";
}
function checkRegister()
{
    try
    {
        //尝试连接数据库服务器
        $conn = new PDO("mysql:host=localhost:3306;dbname=pj2_travels;charset=utf8mb4;", "pj2", "531poiuy#");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        //不合法的用户名
        $userName = $_POST['UserName'];
        if(strlen($userName) > 20 || strlen($userName) < 4)
        {
            return INVALID_USERNAME;
        }
        //用户名已存在
        $sql = "SELECT UserName FROM traveluser WHERE UserName=:UserName";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':UserName', $_POST['UserName']);
        $stmt->execute();
        if($stmt->rowCount() > 0)
        {
            return DUPLICATED_USERNAME;
        }
        //两次输入的密码不一致
        if($_POST['Password'] != $_POST['PasswordConfirm'])
        {
            return INCONSISTENT_PASS;
        }
        //无效的邮箱
        if(!preg_match("/^([^\x01-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c\x3e\x40\x5b-\x5d\x7f]+|)(\x2e([^\x01-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c\x3e\x40\x5b-\x5d\x7f]+|))*\x40([^\x01-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c\x3e\x40\x5b-\x5d\x7f]+|)(\x2e([^\x01-\x20\x22\x28\x29\x2c\x2e\x3a-\x3c\x3e\x40\x5b-\x5d\x7f]+|))*(\.\w{2,})+$/", $_POST['Mail']))
        {
            return INVALID_MAIL;
        }
        //弱密码
        if(strlen($_POST['Password']) < 6 || !preg_match("/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9]).*$/", $_POST['Password']))
        {
            return WEAK_PASS;
        }
    }
    catch(PDOException $e)
    {
        //数据库连接失败
        echo $e->getMessage();
        return 999;
    }
    finally//防止return之后不关闭链接
    {
        $conn = null;
    }
    return SUCCESS;
}

if(isset($_POST['UserName']))//如果表单里有信息，说明正在尝试注册
{
    if($_SERVER["REQUEST_METHOD"] == "POST")
    {
        $returnCode = checkRegister();
        if($returnCode === SUCCESS)//注册成功
        {
            try
            {
                //尝试连接数据库服务器
                $conn = new PDO("mysql:host=localhost:3306;dbname=pj2_travels;charset=utf8mb4;", "pj2", "531poiuy#");
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                //连接成功
                $randSalt = hash("sha256", rand(1, 1000000));
                $pass = hash("sha256", $_POST['Password'] . $_POST['UserName'] . $randSalt);
                $sql = "INSERT INTO traveluser(Email,UserName,Pass,State,DateJoined,Salt,DateLastModified) 
VALUES (:Email,:UserName,:Pass,1,current_timestamp,:Salt,current_timestamp)";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':Email', $_POST['Mail']);
                $stmt->bindParam(':UserName', $_POST['UserName']);
                $stmt->bindParam(':Pass', $pass);
                $stmt->bindParam(':Salt', $randSalt);
                $stmt->execute();
                header("location:login.php");
            }
            catch(PDOException $e)
            {
                //数据库连接失败
                echo $e->getMessage();
                return 999;
            }
            finally
            {
                $conn = null;
            }
        }
        else
        {
            echo getRegisterForm($returnCode);//登陆失败，显示错误信息
        }
    }
}
else
{
    echo getRegisterForm(SUCCESS);
}
?>

<footer class="copyright">
    <span class="copyright">Copyright &copy;2020 Letian Yuan.
                            (旦)-学习性-2020-0001&nbsp;&nbsp;&nbsp;旦公网安备19302010019号&nbsp;&nbsp;&nbsp;旦ICP证000019号</span>
</footer>

</body>
</html>