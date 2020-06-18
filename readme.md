# PJ2说明文档

> 袁乐天 19302010019

## 项目完成情况

> 除开Bonus的“前端框架、工具”部分，其他内容均已完成，不再赘述。

### 服务器部署

网址：[http://letianyuan.xyz:63337/](http://letianyuan.xyz:63337/)

> 说明：考虑到国内部署网站会需要ICP备案和网安备案，ICP备案较容易通过，而“旅游图片分享平台”属于“交互式网站”，欲通过网安备案则需要设立安全员、审核员等，基本不可能通过。所以没有部署在80端口，而是选择了一个不知名端口以防查水表。
>
> 因此，网站会在助教批改完PJ后及时关闭。

### 密码加盐

![screenshot1](img\screenshot1.jpg)

如图，对每一个用户随机生成一个十六进制字符串作为随机盐，并且以用户名本身作为固定盐，将上述字符串与用户原先的密码进行连接，之后对得到的一长串进行SHA256加密。即`加盐后的密码=SHA256(用户原先的密码+用户名+随机盐)`

### 首页

* **“收藏最多”** 利用sql语句的`ORDER BY COUNT(travelimagefavor.ImageID) DESC`即可
* **“随机”** 利用sql语句的`ORDER BY rand()`即可

### 浏览页

* 筛选部分

  * 考虑到服务器性能的问题，我直接把所有的国家信息放到了html代码中，把所有的城市信息放到了js文件中，从而不进行数据库查询来实现多级联动
  * 模糊查询利用了sql语句中的`LIKE`。这一块是非常容易被SQL注入的，因而我直接将查询语句中的sql关键字直接删除以防SQL注入。

* 图片展示部分

  * 页码的实现方式主要是借鉴了老师讲课时的代码，利用sql语句的`LIMIT`即可

* 热门的定义

  * 同一类别下图片数量最多的类别定义为最热门

    例如以下是热门国家的实现

    ```sql
    SELECT travelimage.CountryCodeISO, COUNT(travelimage.CountryCodeISO) AS countryCount, geocountries.CountryName
    FROM travelimage
             INNER JOIN geocountries ON travelimage.CountryCodeISO = geocountries.ISO
    GROUP BY travelimage.CountryCodeISO
    ORDER BY countryCount DESC
    LIMIT 0,5
    ```

### 搜索页

> 模糊查询同浏览页

### 上传页

* 考虑到修改数据库是相当敏感的操作，且用户可以在客户端删除js代码，因而我的合法性校验并没有用js，而是直接在服务器端进行的
* 如果用户没有登录而直接进入【上传页】的话，用户会被强制跳转到登录页，登录成功之后会返回【上传页】
* 上传图片之后会在`/large`文件夹下存储源文件，然后被压缩到80KB左右后被复制一份存储到`/small`文件夹下，因而，可以接受用户上传比较大的图片，而不至于首页或浏览页卡顿。

### 我的照片

* 如果用户没有登录而直接进入【我的照片】的话，用户会被强制跳转到登录页，登录成功之后会返回【我的照片】
* 点击删除后，会重定向到deletePicture.php页面，并传入imageID参数来实现删除。由于删除图片是一个相当敏感的操作，deletePicture.php页面**必须**对用户身份进行验证，验证这张图片是不是用户自己的。否则，任何人都可以随意删除图片，并且百度爬虫如果一爬deletePicture.php页面的话，数据库里的所有图片就都没了。

### 我的收藏

> 基本同【我的照片】

### 登陆页

* 登陆采用POST方式

### 注册页

> 基本同登陆页

### 详细图片页面

* 图片显示的是`/large`文件夹中的原图

### RESTful风格

RESTFUL特点包括：

* 每一个URI代表1种资源；
* 客户端使用GET、POST、PUT、DELETE4个表示操作方式的动词对服务端资源进行操作：GET用来获取资源，POST用来新建资源（也可以用于更新资源），PUT用来更新资源，DELETE用来删除资源；
* 通过操作资源的表现形式来操作资源；
* 资源的表现形式是XML或者HTML；
* 客户端与服务端之间的交互在请求之间是无状态的，从客户端到服务端的每个请求都必须包含理解请求所必需的信息。

我尽量地去实现了RESTful风格，但由于时间有限，可能一些细节还是理解不够到位的。

例如：

* 页码是利用GET方式实现的，例如http://letianyuan.xyz:63337/browse.php?page=3
* 图片详情页也是利用GET方式实现的，例如http://letianyuan.xyz:63337/picture_details.php?imageID=70

这样每个URI都代表了1种资源，实现了 **幂等**。