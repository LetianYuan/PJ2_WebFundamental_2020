<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <title>浏览</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/browse.css">
</head>
<body>
<?php
require_once("php/toolbar.php");
require_once("php/SQLprepare.php");
showToolbar(BROWSE);
?>

<?php
try
{
    //尝试连接数据库服务器
    $conn = new PDO("mysql:host=localhost:3306;dbname=pj2_travels;charset=utf8mb4;", "pj2", "531poiuy#");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //连接成功
    ?>
    <aside>
        <ul class="search">
            <li class="title">
                按标题搜索
            </li>
            <li class="inputs">
                <form id="wd_search" action="" method="get">
                    <input id="search_filter" type="text" name="wd"
                           placeholder="搜索标题" value="<?php if(isset($_GET["wd"])) echo $_GET["wd"] ?>">
                    <a id="search_btn" href="javascript:"><img src="img/empty.png" alt="搜索"></a>
                </form>
                <script>
                    document.getElementById("search_btn").onclick = function()
                    {
                        if(document.getElementById("search_filter").value.length > 0)
                            document.getElementById("wd_search").submit();
                    }
                </script>
            </li>
        </ul>
        <ul class="hot_countries">
            <li class="title">
                热门国家快速浏览
            </li>
            <?php
            $sql = "
SELECT travelimage.CountryCodeISO, COUNT(travelimage.CountryCodeISO) AS countryCount, geocountries.CountryName
FROM travelimage
         INNER JOIN geocountries ON travelimage.CountryCodeISO = geocountries.ISO
GROUP BY travelimage.CountryCodeISO
ORDER BY countryCount DESC
LIMIT 0,5";
            $ctryCount = 0;
            $result = $conn->query($sql);
            while($row = $result->fetch())
            {
                $ctryCount++;
                ?>
                <li class="aside_content">
                    <form name="ctry<?php echo $ctryCount; ?>" action="" method="get">
                        <input readonly hidden name="ctry" value="<?php echo $row["CountryName"]; ?>"/>
                        <p onclick="document.ctry<?php echo $ctryCount; ?>.submit();"><?php echo $row["CountryName"]; ?></p>
                    </form>
                </li>
                <?php
            }
            ?>
        </ul>
        <ul class="hot_cities">
            <li class="title">
                热门城市快速浏览
            </li>
            <?php
            $sql = "
SELECT travelimage.CityCode, COUNT(travelimage.CityCode) AS cityCount, geocities.AsciiName AS cityName
FROM travelimage
         INNER JOIN geocities ON travelimage.CityCode = geocities.GeoNameID
GROUP BY travelimage.CityCode
ORDER BY cityCount DESC
LIMIT 0,5";
            $ctCount = 0;
            $result = $conn->query($sql);
            while($row = $result->fetch())
            {
                $ctCount++;
                ?>
                <li class="aside_content">
                    <form name="ct<?php echo $ctCount; ?>" action="" method="get">
                        <input readonly hidden name="ct" value="<?php echo $row["cityName"]; ?>"/>
                        <p onclick="document.ct<?php echo $ctCount; ?>.submit();"><?php echo $row["cityName"]; ?></p>
                    </form>
                </li>
                <?php
            }
            ?>
        </ul>
        <ul class="hot_contents">
            <li class="title">
                热门内容快速浏览
            </li>
            <?php
            $sql = "
SELECT travelimage.Content, COUNT(travelimage.Content) AS contentCount
FROM travelimage
GROUP BY travelimage.Content
ORDER BY contentCount DESC
LIMIT 0,5";
            $ctntCount = 0;
            $result = $conn->query($sql);
            while($row = $result->fetch())
            {
                $ctntCount++;
                ?>
                <li class="aside_content">
                    <form name="ctnt<?php echo $ctntCount; ?>" action="" method="get">
                        <input readonly hidden name="ctnt" value="<?php echo $row["Content"]; ?>"/>
                        <p onclick="document.ctnt<?php echo $ctntCount; ?>.submit();"><?php echo $row["Content"]; ?></p>
                    </form>
                </li>
                <?php
            }
            ?>
        </ul>
    </aside>
    <main>
        <strong id="loading" class="image_title" style="height: 190px">页面加载中……<br/></strong>
        <script>
            window.onload = function loading()
            {
                document.getElementById('loading').style.display = "none";
            };
        </script>
        <ul>
            <li class="title">
                筛选条件
            </li>
            <li class="filter">
                <form action="" method="get">
                    <select id="content" name="content">
                        <option id="content_default" value="none" selected>--选择内容--</option>
                        <?php
                        $sql = "SELECT Content FROM travelimage GROUP BY Content";
                        $result = $conn->query($sql);
                        while($row = $result->fetch())
                        {
                            ?>
                            <option value="<?php echo $row["Content"] ?>"><?php echo $row["Content"] ?></option>
                            <?php
                        }
                        ?>
                    </select>
                    <select id="country" name="country">
                        <option id="country_default" value="none" selected>--选择国家--</option>
                        <option value="Afghanistan">Afghanistan</option>
                        <option value="Aland Islands">Aland Islands</option>
                        <option value="Albania">Albania</option>
                        <option value="Algeria">Algeria</option>
                        <option value="American Samoa">American Samoa</option>
                        <option value="Andorra">Andorra</option>
                        <option value="Angola">Angola</option>
                        <option value="Anguilla">Anguilla</option>
                        <option value="Antarctica">Antarctica</option>
                        <option value="Antigua and Barbuda">Antigua and Barbuda</option>
                        <option value="Argentina">Argentina</option>
                        <option value="Armenia">Armenia</option>
                        <option value="Aruba">Aruba</option>
                        <option value="Australia">Australia</option>
                        <option value="Austria">Austria</option>
                        <option value="Azerbaijan">Azerbaijan</option>
                        <option value="Bahamas">Bahamas</option>
                        <option value="Bahrain">Bahrain</option>
                        <option value="Bangladesh">Bangladesh</option>
                        <option value="Barbados">Barbados</option>
                        <option value="Belarus">Belarus</option>
                        <option value="Belgium">Belgium</option>
                        <option value="Belize">Belize</option>
                        <option value="Benin">Benin</option>
                        <option value="Bermuda">Bermuda</option>
                        <option value="Bhutan">Bhutan</option>
                        <option value="Bolivia">Bolivia</option>
                        <option value="Bonaire, Saint Eustatius and Saba">Bonaire, Saint Eustatius and Saba</option>
                        <option value="Bosnia and Herzegovina">Bosnia and Herzegovina</option>
                        <option value="Botswana">Botswana</option>
                        <option value="Bouvet Island">Bouvet Island</option>
                        <option value="Brazil">Brazil</option>
                        <option value="British Indian Ocean Territory">British Indian Ocean Territory</option>
                        <option value="British Virgin Islands">British Virgin Islands</option>
                        <option value="Brunei">Brunei</option>
                        <option value="Bulgaria">Bulgaria</option>
                        <option value="Burkina Faso">Burkina Faso</option>
                        <option value="Burundi">Burundi</option>
                        <option value="Cambodia">Cambodia</option>
                        <option value="Cameroon">Cameroon</option>
                        <option value="Canada">Canada</option>
                        <option value="Cape Verde">Cape Verde</option>
                        <option value="Cayman Islands">Cayman Islands</option>
                        <option value="Central African Republic">Central African Republic</option>
                        <option value="Chad">Chad</option>
                        <option value="Chile">Chile</option>
                        <option value="China">China</option>
                        <option value="Christmas Island">Christmas Island</option>
                        <option value="Cocos Islands">Cocos Islands</option>
                        <option value="Colombia">Colombia</option>
                        <option value="Comoros">Comoros</option>
                        <option value="Cook Islands">Cook Islands</option>
                        <option value="Costa Rica">Costa Rica</option>
                        <option value="Croatia">Croatia</option>
                        <option value="Cuba">Cuba</option>
                        <option value="Curacao">Curacao</option>
                        <option value="Cyprus">Cyprus</option>
                        <option value="Czech Republic">Czech Republic</option>
                        <option value="Democratic Republic of the Congo">Democratic Republic of the Congo</option>
                        <option value="Denmark">Denmark</option>
                        <option value="Djibouti">Djibouti</option>
                        <option value="Dominica">Dominica</option>
                        <option value="Dominican Republic">Dominican Republic</option>
                        <option value="East Timor">East Timor</option>
                        <option value="Ecuador">Ecuador</option>
                        <option value="Egypt">Egypt</option>
                        <option value="El Salvador">El Salvador</option>
                        <option value="Equatorial Guinea">Equatorial Guinea</option>
                        <option value="Eritrea">Eritrea</option>
                        <option value="Estonia">Estonia</option>
                        <option value="Ethiopia">Ethiopia</option>
                        <option value="Falkland Islands">Falkland Islands</option>
                        <option value="Faroe Islands">Faroe Islands</option>
                        <option value="Fiji">Fiji</option>
                        <option value="Finland">Finland</option>
                        <option value="France">France</option>
                        <option value="French Guiana">French Guiana</option>
                        <option value="French Polynesia">French Polynesia</option>
                        <option value="French Southern Territories">French Southern Territories</option>
                        <option value="Gabon">Gabon</option>
                        <option value="Gambia">Gambia</option>
                        <option value="Georgia">Georgia</option>
                        <option value="Germany">Germany</option>
                        <option value="Ghana">Ghana</option>
                        <option value="Gibraltar">Gibraltar</option>
                        <option value="Greece">Greece</option>
                        <option value="Greenland">Greenland</option>
                        <option value="Grenada">Grenada</option>
                        <option value="Guadeloupe">Guadeloupe</option>
                        <option value="Guam">Guam</option>
                        <option value="Guatemala">Guatemala</option>
                        <option value="Guernsey">Guernsey</option>
                        <option value="Guinea">Guinea</option>
                        <option value="Guinea-Bissau">Guinea-Bissau</option>
                        <option value="Guyana">Guyana</option>
                        <option value="Haiti">Haiti</option>
                        <option value="Heard Island and McDonald Islands">Heard Island and McDonald Islands</option>
                        <option value="Honduras">Honduras</option>
                        <option value="Hong Kong">Hong Kong</option>
                        <option value="Hungary">Hungary</option>
                        <option value="Iceland">Iceland</option>
                        <option value="India">India</option>
                        <option value="Indonesia">Indonesia</option>
                        <option value="Iran">Iran</option>
                        <option value="Iraq">Iraq</option>
                        <option value="Ireland">Ireland</option>
                        <option value="Isle of Man">Isle of Man</option>
                        <option value="Israel">Israel</option>
                        <option value="Italy">Italy</option>
                        <option value="Ivory Coast">Ivory Coast</option>
                        <option value="Jamaica">Jamaica</option>
                        <option value="Japan">Japan</option>
                        <option value="Jersey">Jersey</option>
                        <option value="Jordan">Jordan</option>
                        <option value="Kazakhstan">Kazakhstan</option>
                        <option value="Kenya">Kenya</option>
                        <option value="Kiribati">Kiribati</option>
                        <option value="Kosovo">Kosovo</option>
                        <option value="Kuwait">Kuwait</option>
                        <option value="Kyrgyzstan">Kyrgyzstan</option>
                        <option value="Laos">Laos</option>
                        <option value="Latvia">Latvia</option>
                        <option value="Lebanon">Lebanon</option>
                        <option value="Lesotho">Lesotho</option>
                        <option value="Liberia">Liberia</option>
                        <option value="Libya">Libya</option>
                        <option value="Liechtenstein">Liechtenstein</option>
                        <option value="Lithuania">Lithuania</option>
                        <option value="Luxembourg">Luxembourg</option>
                        <option value="Macao">Macao</option>
                        <option value="Macedonia">Macedonia</option>
                        <option value="Madagascar">Madagascar</option>
                        <option value="Malawi">Malawi</option>
                        <option value="Malaysia">Malaysia</option>
                        <option value="Maldives">Maldives</option>
                        <option value="Mali">Mali</option>
                        <option value="Malta">Malta</option>
                        <option value="Marshall Islands">Marshall Islands</option>
                        <option value="Martinique">Martinique</option>
                        <option value="Mauritania">Mauritania</option>
                        <option value="Mauritius">Mauritius</option>
                        <option value="Mayotte">Mayotte</option>
                        <option value="Mexico">Mexico</option>
                        <option value="Micronesia">Micronesia</option>
                        <option value="Moldova">Moldova</option>
                        <option value="Monaco">Monaco</option>
                        <option value="Mongolia">Mongolia</option>
                        <option value="Montenegro">Montenegro</option>
                        <option value="Montserrat">Montserrat</option>
                        <option value="Morocco">Morocco</option>
                        <option value="Mozambique">Mozambique</option>
                        <option value="Myanmar">Myanmar</option>
                        <option value="Namibia">Namibia</option>
                        <option value="Nauru">Nauru</option>
                        <option value="Nepal">Nepal</option>
                        <option value="Netherlands">Netherlands</option>
                        <option value="Netherlands Antilles">Netherlands Antilles</option>
                        <option value="New Caledonia">New Caledonia</option>
                        <option value="New Zealand">New Zealand</option>
                        <option value="Nicaragua">Nicaragua</option>
                        <option value="Niger">Niger</option>
                        <option value="Nigeria">Nigeria</option>
                        <option value="Niue">Niue</option>
                        <option value="Norfolk Island">Norfolk Island</option>
                        <option value="North Korea">North Korea</option>
                        <option value="Northern Mariana Islands">Northern Mariana Islands</option>
                        <option value="Norway">Norway</option>
                        <option value="Oman">Oman</option>
                        <option value="Pakistan">Pakistan</option>
                        <option value="Palau">Palau</option>
                        <option value="Palestinian Territory">Palestinian Territory</option>
                        <option value="Panama">Panama</option>
                        <option value="Papua New Guinea">Papua New Guinea</option>
                        <option value="Paraguay">Paraguay</option>
                        <option value="Peru">Peru</option>
                        <option value="Philippines">Philippines</option>
                        <option value="Pitcairn">Pitcairn</option>
                        <option value="Poland">Poland</option>
                        <option value="Portugal">Portugal</option>
                        <option value="Puerto Rico">Puerto Rico</option>
                        <option value="Qatar">Qatar</option>
                        <option value="Republic of the Congo">Republic of the Congo</option>
                        <option value="Reunion">Reunion</option>
                        <option value="Romania">Romania</option>
                        <option value="Russia">Russia</option>
                        <option value="Rwanda">Rwanda</option>
                        <option value="Saint Barthelemy">Saint Barthelemy</option>
                        <option value="Saint Helena">Saint Helena</option>
                        <option value="Saint Kitts and Nevis">Saint Kitts and Nevis</option>
                        <option value="Saint Lucia">Saint Lucia</option>
                        <option value="Saint Martin">Saint Martin</option>
                        <option value="Saint Pierre and Miquelon">Saint Pierre and Miquelon</option>
                        <option value="Saint Vincent and the Grenadines">Saint Vincent and the Grenadines</option>
                        <option value="Samoa">Samoa</option>
                        <option value="San Marino">San Marino</option>
                        <option value="Sao Tome and Principe">Sao Tome and Principe</option>
                        <option value="Saudi Arabia">Saudi Arabia</option>
                        <option value="Senegal">Senegal</option>
                        <option value="Serbia">Serbia</option>
                        <option value="Serbia and Montenegro">Serbia and Montenegro</option>
                        <option value="Seychelles">Seychelles</option>
                        <option value="Sierra Leone">Sierra Leone</option>
                        <option value="Singapore">Singapore</option>
                        <option value="Sint Maarten">Sint Maarten</option>
                        <option value="Slovakia">Slovakia</option>
                        <option value="Slovenia">Slovenia</option>
                        <option value="Solomon Islands">Solomon Islands</option>
                        <option value="Somalia">Somalia</option>
                        <option value="South Africa">South Africa</option>
                        <option value="South Georgia and the South Sandwich Islands">South Georgia and the South Sandwich Islands</option>
                        <option value="South Korea">South Korea</option>
                        <option value="South Sudan">South Sudan</option>
                        <option value="Spain">Spain</option>
                        <option value="Sri Lanka">Sri Lanka</option>
                        <option value="Sudan">Sudan</option>
                        <option value="Suriname">Suriname</option>
                        <option value="Svalbard and Jan Mayen">Svalbard and Jan Mayen</option>
                        <option value="Swaziland">Swaziland</option>
                        <option value="Sweden">Sweden</option>
                        <option value="Switzerland">Switzerland</option>
                        <option value="Syria">Syria</option>
                        <option value="Taiwan">Taiwan</option>
                        <option value="Tajikistan">Tajikistan</option>
                        <option value="Tanzania">Tanzania</option>
                        <option value="Thailand">Thailand</option>
                        <option value="Togo">Togo</option>
                        <option value="Tokelau">Tokelau</option>
                        <option value="Tonga">Tonga</option>
                        <option value="Trinidad and Tobago">Trinidad and Tobago</option>
                        <option value="Tunisia">Tunisia</option>
                        <option value="Turkey">Turkey</option>
                        <option value="Turkmenistan">Turkmenistan</option>
                        <option value="Turks and Caicos Islands">Turks and Caicos Islands</option>
                        <option value="Tuvalu">Tuvalu</option>
                        <option value="U.S. Virgin Islands">U.S. Virgin Islands</option>
                        <option value="Uganda">Uganda</option>
                        <option value="Ukraine">Ukraine</option>
                        <option value="United Arab Emirates">United Arab Emirates</option>
                        <option value="United Kingdom">United Kingdom</option>
                        <option value="United States">United States</option>
                        <option value="United States Minor Outlying Islands">United States Minor Outlying Islands</option>
                        <option value="Uruguay">Uruguay</option>
                        <option value="Uzbekistan">Uzbekistan</option>
                        <option value="Vanuatu">Vanuatu</option>
                        <option value="Vatican">Vatican</option>
                        <option value="Venezuela">Venezuela</option>
                        <option value="Vietnam">Vietnam</option>
                        <option value="Wallis and Futuna">Wallis and Futuna</option>
                        <option value="Western Sahara">Western Sahara</option>
                        <option value="Yemen">Yemen</option>
                        <option value="Zambia">Zambia</option>
                        <option value="Zimbabwe">Zimbabwe</option>
                    </select>
                    <select id="city" name="city">
                        <option id="city_default" value="none" selected>--选择地区--</option>
                    </select>
                    <input type="submit" value="筛选">
                </form>
            </li>
            <script src="js/filter_onchange.js"></script>
            <script src="js/getQueryVariable.js"></script>
            <script src="js/browse_filter_echo.js"></script>
            <li class="content">
                <ul>
                    <?php
                    $sql = "SELECT travelimage.ImageID, travelimage.PATH FROM travelimage ";
                    if(isset($_GET["wd"]))//按标题查询，wd是word的意思
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
                    else if(isset($_GET["ctry"]))//country
                    {
                        $ctry = $_GET["ctry"];
                        SQLprepare($ctry);
                        $sql .= "INNER JOIN geocountries ON travelimage.CountryCodeISO = geocountries.ISO WHERE geocountries.CountryName = ";
                        $sql .= "'" . $ctry . "' ";
                    }
                    else if(isset($_GET["ct"]))//city
                    {
                        $ct = $_GET["ct"];
                        SQLprepare($ct);
                        $sql .= "INNER JOIN geocities ON travelimage.Citycode = geocities.GeoNameID WHERE geocities.AsciiName = ";
                        $sql .= "'" . $ct . "' ";
                    }
                    else if(isset($_GET["ctnt"]))//content
                    {
                        $ctnt = $_GET["ctnt"];
                        SQLprepare($ctnt);
                        $sql .= "WHERE travelimage.Content = ";
                        $sql .= "'" . $ctnt . "' ";
                    }
                    else if(isset($_GET["content"]) && isset($_GET["country"]) && isset($_GET["city"]))
                    {
                        $content = $_GET["content"];
                        $country = $_GET["country"];
                        $city = $_GET["city"];
                        SQLprepare($content);
                        SQLprepare($country);
                        SQLprepare($city);
                        $sql .= "LEFT JOIN geocities ON geocities.GeoNameID = travelimage.CityCode LEFT JOIN geocountries ON geocountries.ISO = travelimage.CountryCodeISO ";
                        if($content != 'NONE')
                            $sql .= "WHERE travelimage.Content = '" . $content . "' ";
                        else
                            $sql .= "WHERE 1=1 ";
                        if($country != 'NONE')
                            $sql .= "AND geocountries.CountryName = '" . $country . "' ";
                        if($city != 'NONE')
                            $sql .= "AND geocities.AsciiName = '" . $city . "' ";
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
                        $pageSize = 18;//一页上容纳的图片数量
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
                            <li class="content_image">
                                <a href="picture_details.php<?php echo "?imageID=" . $row["ImageID"] ?>">
                                    <img src="img/travel_images/small/<?php echo $row["PATH"] ?>"
                                         alt="<?php echo $row["PATH"] ?>">
                                </a>
                            </li>
                            <?php
                        }
                    }
                    else
                    {
                        ?>
                        <strong class="image_title" style="height: 190px">很抱歉，没有查找到相关的图片。</strong>
                        <?php
                    }
                    ?>
                </ul>
                <script src="js/browse_picture_appear.js"></script>
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
                    <a href="browse.php?<?php echo changePageTo($_SERVER["QUERY_STRING"], max(1, $currentPage - 1)); ?>"
                       title="向前一页"><<</a>
                    <?php
                    for($i = 1; $i <= $totalPage; $i++)
                    {
                        ?>
                        <a <?php if($i == $currentPage) echo "class=\"active\"" ?>
                                href="browse.php?<?php echo changePageTo($_SERVER["QUERY_STRING"], $i); ?>"
                                title="<?php echo $i ?>"><?php echo $i ?></a>
                        <?php
                    }
                    ?>
                    <a href="browse.php?<?php echo changePageTo($_SERVER["QUERY_STRING"], min($totalPage, $currentPage + 1)); ?>"
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
    <span class="copyright">Copyright &copy;2020 Letian Yuan.
                            (旦)-学习性-2020-0001&nbsp;&nbsp;&nbsp;旦公网安备19302010019号&nbsp;&nbsp;&nbsp;旦ICP证000019号</span>
</footer>
</body>
</html>