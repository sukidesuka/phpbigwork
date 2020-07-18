<title>用户注册</title>
<h1>大作业宾馆用户注册</h1>

<?php 
$errmsg = $user = $password = $confirm_password = '';
$question1 = $question2 = $question3 = '';
$answer1 = $answer2 = $answer3 = '';

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
    insert into `user_info` values (
        "{$_POST['user']}",
        "$pwdhash",
        "{$_POST['people_id']}",
        0
    )
    xxx;
    $result = mysqli_query($link, $sql);
    if (!$result) {
        echo '执行失败'.mysqli_error($link);
    }
    // 将密保信息写入数据库
    $sql = <<<xxx
    insert into `user_question` values (
        "{$_POST['user']}",
        "{$_POST['question1']}",
        "{$_POST['answer1']}",
        "{$_POST['question2']}",
        "{$_POST['answer2']}",
        "{$_POST['question3']}",
        "{$_POST['answer3']}"
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
            <td>用户名：</td>
            <td><input type='text' name='user' value='<?php echo $user; ?>' /></td>
        </tr>
        <tr>
            <td>密码：</td>
            <td><input type='password' name='password' value='<?php echo $password; ?>' /></td>
        </tr>
        <tr>
            <td>确认密码：</td>
            <td><input type='password' name='confirm_password' value='<?php echo $confirm_password; ?>' /></td>
        </tr>
        <tr>
            <td>身份证号码：</td>
            <td><input type='text' name='people_id' value='<?php echo $password; ?>' /></td>
        </tr>
        <tr>
            <td>密保问题1：</td>
            <td><input type="text" name="question1" value="<?php echo $question1; ?>"></td>
        </tr>
        <tr>
            <td>回答1：</td>
            <td><input type="text" name="answer1" value="<?php echo $answer1; ?>"></td>
        </tr>
        <tr>
            <td>密保问题2：</td>
            <td><input type="text" name="question2" value="<?php echo $question2; ?>"></td>
        </tr>
        <tr>
            <td>回答2：</td>
            <td><input type="text" name="answer2" value="<?php echo $answer2; ?>"></td>
        </tr>
        <tr>
            <td>密保问题3：</td>
            <td><input type="text" name="question3" value="<?php echo $question3; ?>"></td>
        </tr>
        <tr>
            <td>回答3：</td>
            <td><input type="text" name="answer3" value="<?php echo $answer3; ?>"></td>
        </tr>
        <tr>
            <td>
                <input type="submit" value='注册'>
            </td>
        </tr>
    </table>
</form>