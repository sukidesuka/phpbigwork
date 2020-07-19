<title>用户账户管理</title>
<h1>用户账户管理</h1>


<?php
// 判断session有没有用户名，没有就是非法访问，要跳回去
session_start();
if (!isset($_SESSION['user'])) {
    header('location: admin.php');
    exit();
}

// 连接数据库
$link = mysqli_connect('localhost:3308', 'root', '', 'bigwork');
if (!$link) {
    echo '数据库连接失败<br>';
    exit();
}

// 获取当前账户权限等级，权限小于3才能操作
$sql = 'select * from `admin`';
$result = mysqli_query($link, $sql);
if (!$result) {
    echo '执行失败'.mysqli_error($link);
}
$result = $result->fetch_all();
for ($i = 0; $i < count($result); $i++) {
    if ($result[$i][0] == $_SESSION['user']) {
        $level = $result[$i][2];
    }
}
// 如果权限>=3，那么跳回管理界面
if ($level >= 3) {
    header("location: manager.php");
}

// 处理post请求
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['operate'] == 'edit_question') {
        $sql = <<<xxx
        update user_question set
        user="{$_POST['user']}",
        question1="{$_POST['question1']}",
        answer1="{$_POST['answer1']}",
        question2="{$_POST['question2']}",
        answer2="{$_POST['answer2']}",
        question3="{$_POST['question3']}",
        answer3="{$_POST['answer3']}"
        where user="{$_POST['user']}"
        xxx;
        $result = mysqli_query($link, $sql);
        if (!$result) {
            echo '执行失败'.mysqli_error($link);
        }
    }
    else if ($_POST['operate'] == 'edit_password') {
        $pwdhash = crypt($_POST['password'], 'salt');
        $sql = <<<xxx
        update user_info set 
        pwdhash="$pwdhash"
        where
        user="{$_POST['user']}"
        xxx;
        $result = mysqli_query($link, $sql);
        if (!$result) {
            echo '执行失败'.mysqli_error($link);
        }
    }
    else if ($_POST['operate'] == 'delete') {
        $sql = <<<xxx
        delete from user_info where
        user="{$_POST['user']}"
        xxx;
        $result = mysqli_query($link, $sql);
        if (!$result) {
            echo '执行失败'.mysqli_error($link);
        }
    }
    else {
        echo 'post提交了奇怪的东西<br>';
        var_dump($_POST);
    }
}

?>


<h3>用户账户列表</h3>
<table border="1">
    <tr>
        <th>用户名</th>
        <th>已消费金额</th>
        <th>密保修改</th>
        <th>密码修改</th>
        <th>账户删除</th>
    </tr>

    <?php
    // 拉取用户数据
    $sql = 'select * from `user_info`';
    $result = mysqli_query($link, $sql);
    if (!$result) {
        echo '执行失败'.mysqli_error($link);
    }
    $result = $result->fetch_all();
    for ($i = 0; $i < count($result); $i++) {
        echo '<tr>';
        // 显示用户名
        echo '<td>'.$result[$i][0].'</td>';
        // 显示已消费金额
        echo '<td>'.$result[$i][3].'</td>';
        // 拉取用户的密保问题
        $sql = 'select * from `user_question` where `user`="'.$result[$i][0].'"';
        $question = mysqli_query($link, $sql);
        if (!$question) {
            echo '执行失败'.mysqli_error($link);
        }
        $question = $question->fetch_all();
        echo <<<xxx
        <form action="{$_SERVER['PHP_SELF']}" method="post">
            <td>
                <input type="hidden" name="operate" value="edit_question">
                <input type="hidden" name="user" value="{$result[0][0]}">
                <input type="text" name="question1" value="{$question[0][1]}" placeholder="问题1">
                <input type="text" name="answer1" value="{$question[0][2]}" placeholder="回答1">
                <input type="text" name="question2" value="{$question[0][3]}" placeholder="问题2">
                <input type="text" name="answer2" value="{$question[0][4]}" placeholder="回答2">
                <input type="text" name="question3" value="{$question[0][5]}" placeholder="问题3">
                <input type="text" name="answer3" value="{$question[0][6]}" placeholder="回答3">
                <input type="submit" value="修改密保">
            </td>
        </form>
        xxx;

        echo <<<xxx
        <form action="{$_SERVER['PHP_SELF']}" method="post">
            <td>
                <input type="hidden" name="operate" value="edit_password">
                <input type="hidden" name="user" value="{$result[0][0]}">
                <input type="text" name="password" placeholder="输入要修改的密码">
                <input type="submit" value="修改密码">
            </td> 
        </form>
        xxx;

        echo <<<xxx
        <form action="{$_SERVER['PHP_SELF']}" method="post">
            <td>
                <input type="hidden" name="operate" value="delete">
                <input type="hidden" name="user" value="{$result[0][0]}">
                <input type="submit" value="删除账户">
            </td>
        </form>
        xxx;

        
        echo '</tr>';
    }
    ?>
</table>