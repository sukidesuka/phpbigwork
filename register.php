<title>用户注册</title>
<h1>大作业宾馆用户注册</h1>

<?php 
$errmsg = $user = $password = $confirm_password = '';

// 连接数据库
$link = mysqli_connect('localhost:3308', 'root', '', 'bigwork');
if (!$link) {
    echo '数据库连接失败<br>';
    exit();
}

// 处理post请求
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 将注册数据写入数据库
    $pwdhash = crypt($_POST['password'], 'salt');
    $sql = <<<xxx
    insert into user_info values (
        "{$_POST['user']}",
        "$pwdhash",
        "{$_POST['people_id']}"
    )
    xxx;
    $result = mysqli_query($link, $sql);
    if (!$result) {
        echo '执行失败'.mysqli_error($link);
    }
    // 跳转到信息提示页面提示注册成功
    header("location: error.php?message=注册成功&url=index.php&note=返回首页");
}
?>


<p><?php echo $errmsg; ?></p>
<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
    <table>
        <tr>
            <td><label>用户名：</label></td>
            <td><input type='text' name='user' value='<?php echo $user; ?>' /></td>
        </tr>
        <tr>
            <td><label>密码：</label></td>
            <td><input type='password' name='password' value='<?php echo $password; ?>' /></td>
        </tr>
        <tr>
            <td><label>确认密码：</label></td>
            <td><input type='password' name='confirm_password' value='<?php echo $confirm_password; ?>' /></td>
        </tr>
        <tr>
            <td><label>身份证号码：</label></td>
            <td><input type='text' name='people_id' id='password' value='<?php echo $password; ?>' /></td>
        </tr>
        <tr>
            <td>
                <input type="submit" value='登录'>
            </td>
        </tr>
    </table>
</form>