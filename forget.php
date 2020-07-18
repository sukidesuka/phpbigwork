<title>密码找回</title>
<h1>密码找回</h1>

<?php 

// 连接数据库
$link = mysqli_connect('localhost:3308', 'root', '', 'bigwork');
if (!$link) {
    echo '数据库连接失败<br>';
    exit();
}

// 处理post请求
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 从数据库获取密保问题
    $sql = 'select * from `user_question` where user="'.$_POST['user'].'"';
    $result = mysqli_query($link, $sql);
    if (!$result) {
    echo '执行失败'.mysqli_error($link);
    }
    $result = $result->fetch_all();
    $question1 = $result[0][1];
    $answer1 = $result[0][2];
    $question2 = $result[0][3];
    $answer2 = $result[0][4];
    $question3 = $result[0][5];
    $answer3 = $result[0][6];
}

?>
<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
    <table>
        <?php
        // 需要先让用户输入用户名，才能从数据库拿密保，所以需要做判断
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            echo <<<xxx
            <input type="hidden" name="operation" value="edit">
            <tr>
                <td>用户名：</td>
                <td>{$_POST['user']}</td>
            </tr>
            <tr>
                <td>密保问题1：</td>
                <td>$question1</td>
            </tr>
            <tr>
                <td>回答1：</td>
                <td><input type="text" name="post_answer1" value=""></td>
            </tr>
            <tr>
                <td>密保问题2：</td>
                <td>$question2</td>
            </tr>
            <tr>
                <td>回答2：</td>
                <td><input type="text" name="post_answer2" value=""></td>
            </tr>
            <tr>
                <td>密保问题3：</td>
                <td>$question3</td>
            </tr>
            <tr>
                <td>回答3：</td>
                <td><input type="text" name="post_answer3" value=""></td>
            </tr>
            <tr>
                <td>新密码</td>
                <td><input type="text" ></td>
            </tr>
            <tr>
                <td>
                    <input type="submit" value='修改密码'>
                </td>
            </tr>
            xxx;
        }
        else {
            echo <<<xxx
            <input type="hidden" name="operation" value="post_user">
            <tr>
                <td>用户名：</td>
                <td><input type="text" name="user"></td>
            </tr>
            <tr>
                <td>
                    <input type="submit" value='确认'>
                </td>
            </tr>
            xxx;
        }
        
        ?>

        
    </table>
</form>