<?php
//try
//{
//    //尝试连接数据库服务器
//    $conn = new PDO("mysql:host=localhost:3306;dbname=pj2_travels;charset=utf8mb4;", "pj2", "531poiuy#");
//    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//    $sql = "SELECT Salt,UserName FROM traveluser ";
//    $result = $conn->query($sql);
//    while($row = $result->fetch())
//    {
//        $userName = $row['UserName'];
//        $salt = $row['Salt'];
//        $newpass = hash("sha256", 'abcd1234' . $userName . $salt);
//
//        $sql2 = "UPDATE traveluser SET Pass ='" . $newpass . "'WHERE UserName='" . $userName . "'";
//        $conn->query($sql2);
//    }
//
//    $conn = null;
//}
//catch(PDOException $e)
//{
//    //数据库连接失败
//    echo $e->getMessage();
//}


//生成 国家-城市 的 json

//try
//{
//    //尝试连接数据库服务器
//    $conn = new PDO("mysql:host=localhost:3306;dbname=pj2_travels;charset=utf8mb4;", "pj2", "531poiuy#");
//    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//    //连接成功
//    $sql = "
//SELECT distinct geocities.AsciiName AS CityName, geocountries.CountryName
//FROM geocities
//         INNER JOIN geocountries ON geocountries.ISO = geocities.CountryCodeISO
//ORDER BY geocountries.CountryName, CityName
//";
//    $result = $conn->query($sql);
//    echo '{';
//    $previousCountryName = "#";
//    while($row = $result->fetch())
//    {
//        $countryName = $row['CountryName'];
//        $cityName = $row['CityName'];
//        if($countryName != $previousCountryName)
//        {
//            echo '],\'' . str_replace('\'', '\\\'', $countryName) . '\':[';
//            $previousCountryName = $countryName;
//        }
//        echo '\'' . str_replace('\'', '\\\'',$cityName) . '\',';
//    }
//
//
//    $conn = null;
//}
//catch(PDOException $e)
//{
//    //数据库连接失败
//    echo $e->getMessage();
//}
