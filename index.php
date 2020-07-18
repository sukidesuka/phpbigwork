<title>大作业宾馆</title>
<h1>欢迎登录大作业宾馆预定系统</h1>

<?php
session_start();

// 先连接数据库
$link = mysqli_connect('localhost:3308', 'root', '', 'bigwork');
if (!$link) {
    echo '数据库连接失败<br>';
    exit();
}

// 处理post请求
$user = $password = $errmsg = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_POST['user'];
    $password = $_POST['password'];
    if (array_key_exists('checkbox', $_POST)) {
        $remember = true;
    }
    else {
        $remember = false;
    }
    //// 验证用户名和密码
    // 验证用户名是否存在
    $sql = 'select * from user_info';
    $result = mysqli_query($link, $sql);
    var_dump($result);
    $result = $result->fetch_all();

    $userExist = false;
    $userPwd = '';
    for ($i = 0; $i < count($result); $i++) {
        if ($result[$i][0] == $user) {
            $userExist = true;
            $userPwd = $result[$i][1];
        }
    }
    if (!$userExist) {
        $errmsg = '用户名不存在';
        return;
    }
    // 验证密码是否正确
    $pwdCorrect = false;
    if ($userPwd == $password) {
        $pwdCorrect = true;
    }
    if (!$pwdCorrect) {
        $errmsg = '密码错误';
        return;
    }
    // 验证验证码
    if (empty($_POST['vcode'])) {
        $errmsg = "请填写验证码！";
    } else {
        if (0 == strcasecmp($_POST['vcode'], $_SESSION['vcode'])) {  //不区分大小写比较
            // 如果有勾选'记住我'，则将user写入cookie
            if (isset($_POST['checkbox'])) {
                setcookie('user', $user, time() + 3600);        // cookies有效期1小时
            }
            // 跳转到欢迎页面
            $_SESSION['user'] = $user;      // 将用户名写入session（就不用token了）
            header("location: welcome.php");
        } else {
            $errmsg = '验证码错误';
        }
    }
}
else {
    // 非POST请求则判断cookie是否存在，存在则直接读取cookie中的user且跳转登录
    if (isset($_COOKIE['user'])) {
        $_SESSION['user'] = $_COOKIE['user'];
        header('location: welcome.php');
    }
}

?>

<p><?php echo $errmsg; ?></p>
<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
    <table>
        <tr>
            <td><label for='user'>用户名：</label></td>
            <td><input type='text' name='user' id='user' value='<?php echo $user; ?>' /></td>
        </tr>
        <tr>
            <td><label for='password'>密码：</label></td>
            <td><input type='password' name='password' id='password' value='<?php echo $password; ?>' /></td>
        </tr>
        <tr>
            <td><label for='vcode'>验证码：</label></td>
            <td><input type='password' name='vcode' id='vcode' /></td>
            <td><img src="vcode.php?width=100&height=35" alt="验证码"></td>
        </tr>
        <tr>
            <td>
                <input type="submit" value='登录'></input>
                <a href="register.php"><button type="button">注册</button></a>
            </td>
        </tr>
        <tr>
            <td>
                <input type="checkbox" value='remember' name='checkbox[]'>记住我</input>
            </td>
        </tr>
    </table>
</form>