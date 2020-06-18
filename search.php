<!DOCTYPE html>
<html lang="zh-cn">

<head>
    <title>搜索</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/search.css">
</head>
<body>
<?php
require_once("php/toolbar.php");
require_once("php/SQLprepare.php");
showToolbar(SEARCH);
try
{
//尝试连接数据库服务器
    $conn = new PDO("mysql:host=localhost:3306;dbname=pj2_travels;charset=utf8mb4;", "pj2", "531poiuy#");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//连接成功
    ?>
    <main>
        <form id="wd_search" action="" method="get">
            <fieldset>
                <ul>
                    <li class="title">
                        搜索
                    </li>
                    <li class="content">
                        <input id="search_btn" type="submit" value="搜索">
                        <label><input id="search_by_title" type="radio" name="m" value="1" checked>按标题搜索</label>
                        <label><input id="search_by_content" type="radio" name="m" value="2">按内容搜索</label>
                        <br/>
                        <input id="search_filter" name="wd" type="text" placeholder="输入搜索关键词"
                               value="<?php echo $_GET['wd'] ?>">
                        <script src="js/getQueryVariable.js"></script>
                        <script>
                            if(getQueryVariable('m') === '2')
                            {
                                document.getElementById('search_by_content').setAttribute('checked', 'true');
                                document.getElementById('search_by_title').removeAttribute('checked');
                            }
                            else
                            {
                                document.getElementById('search_by_title').setAttribute('checked', 'true');
                                document.getElementById('search_by_content').removeAttribute('checked');
                            }
                        </script>
                    </li>
                </ul>
            </fieldset>
        </form>
        <script>
            document.getElementById("wd_search").onsubmit = function(e)
            {
                if(document.getElementById("search_filter").value.length === 0)
                {
                    e.preventDefault();//当用户什么都没有输入时，阻止表单提交
                }
            }
        </script>
        <ul class="result">
            <li class="title">
                结果
            </li>
            <li class="content">
                <ul class="pictures">
                    <?php
                    $sql = "SELECT ImageID, PATH, Description, Title FROM travelimage ";
                    if(isset($_GET['m']))//如果在查询，否则不做任何处理
                    {
                        if($_GET['m'] == '1')//如果是按标题搜索
                        {
                            $sql .= "WHERE ";
                            //LIKE关键字符替换
                            $wd = $_GET["wd"];
                            $wd = str_replace("[", "[[]", $wd);
                            $wd = str_replace("]", "[]]", $wd);
                            $wd = str_replace("_", "[_]", $wd);
                            $wd = str_replace("%", "[%]", $wd);
                            SQLprepare($wd);//SQL防注入
                            $wds = explode(" ", $wd);//explode等于字符串的split分割
                            $wdsl = count($wds);
                            for($i = 0; $i < $wdsl; $i++)
                            {
                                if($i == $wdsl - 1)
                                    $sql .= "Title LIKE '%" . $wds[$i] . "%' ";
                                else
                                    $sql .= "Title LIKE '%" . $wds[$i] . "%' AND ";
                            }
                        }
                        else if($_GET['m'] == '2')//如果是按内容搜索
                        {
                            $sql .= "WHERE ";
                            //LIKE关键字符替换
                            $wd = $_GET["wd"];
                            $wd = str_replace("[", "[[]", $wd);
                            $wd = str_replace("]", "[]]", $wd);
                            $wd = str_replace("_", "[_]", $wd);
                            $wd = str_replace("%", "[%]", $wd);
                            SQLprepare($wd);//SQL防注入
                            $wds = explode(" ", $wd);//explode等于字符串的split分割
                            $wdsl = count($wds);
                            for($i = 0; $i < $wdsl; $i++)
                            {
                                if($i == $wdsl - 1)
                                    $sql .= "Description LIKE '%" . $wds[$i] . "%' ";
                                else
                                    $sql .= "Description LIKE '%" . $wds[$i] . "%' AND ";
                            }
                        }
                    }
                    $result = $conn->query($sql);
                    if($result)
                    {
                        $imageCount = $result->rowCount();//图片总数
                    }
                    else
                    {
                        $imageCount = 0;
                        echo "数据库错误：未知";
                    }
                    if($imageCount > 0)
                    {
                        $pageSize = 5;//一页上容纳的图片数量
                        //页数
                        $totalPage = min((int)(($imageCount % $pageSize == 0) ? ($imageCount / $pageSize) : ($imageCount / $pageSize + 1)), 5);
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
                        $result = $conn->query($sql);
                        while($row = $result->fetch())
                        {
                            ?>
                            <li>
                                <a href="picture_details.php?imageID=<?php echo $row['ImageID'] ?>">
                                    <img src="img/travel_images/small/<?php echo $row['PATH'] ?>"
                                         title="<?php echo $row['Title'] ?>"
                                         alt="<?php echo $row['PATH'] ?>">
                                </a>
                                <span class="image_description"><strong
                                            class="image_title"><?php echo $row['Title'] ?></strong><br/>
                                    <?php echo $row['Description'] ?></span>
                            </li>
                            <?php
                        }
                    }
                    else
                    {
                        ?>
                        <li style="height: 190px">
                            <span class="image_description"><strong class="image_title">很抱歉，没有查找到相关的图片。</strong>
                            </span>
                        </li>
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
                    <a href="search.php?<?php echo changePageTo($_SERVER["QUERY_STRING"], max(1, $currentPage - 1)); ?>"
                       title="向前一页"><<</a>
                    <?php
                    for($i = 1; $i <= $totalPage; $i++)
                    {
                        ?>
                        <a <?php if($i == $currentPage) echo "class=\"active\"" ?>
                                href="search.php?<?php echo changePageTo($_SERVER["QUERY_STRING"], $i); ?>"
                                title="<?php echo $i ?>"><?php echo $i ?></a>
                        <?php
                    }
                    ?>
                    <a href="search.php?<?php echo changePageTo($_SERVER["QUERY_STRING"], min($totalPage, $currentPage + 1)); ?>"
                       title="向后一页">>></a>
                </div>
            </li>
        </ul>
    </main>
    <?php
    $conn = null;
}
catch(PDOException $e)
{
    //数据库连接失败
    echo $e->getMessage();
}
?>
<footer class="copyright">
    <span class="copyright">Copyright &copy;2020 Letian Yuan. (旦)-学习性-2020-0001&nbsp;&nbsp;&nbsp;旦公网安备19302010019号&nbsp;&nbsp;&nbsp;旦ICP证000019号</span>
</footer>

</body>
</html>