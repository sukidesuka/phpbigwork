<title>宾馆管理系统后台</title>
<h1>欢迎登录宾馆管理系统后台</h1>
<?php
// 连接数据库
$link = mysqli_connect('localhost:3308', 'root', '');
if (!$link) {
    echo '数据库连接失败<br>';
    exit();
}

// 如果大作业数据库没有则创建
$sql = 'create database if not exists bigwork';
$result = mysqli_query($link, $sql);
if (!$result) {
    echo '执行失败'.mysqli_error($link);
}

// 选择大作业数据库
$sql = 'use bigwork';
$result = mysqli_query($link, $sql);
if (!$result) {
    echo '执行失败'.mysqli_error($link);
}

// 创建用户的数据表 用户名 密码
$sql = <<<xxx
create table if not exists user_info(
    user varchar(20) not null primary key,
    pwdhash varchar(255) not null
)
xxx;
$result = mysqli_query($link, $sql);
if (!$result) {
    echo '执行失败'.mysqli_error($link);
}

// 创建客房外部信息 房间号 房间类型 房间电话 楼层 客房状态
$sql = <<<xxx
create table if not exists room(
    room_id varchar(20) not null primary key, 
    type varchar(20) not null, 
    phone varchar(20) not null, 
    stair int not null, 
    status varchar(20) not null
)
xxx;
$result = mysqli_query($link, $sql);
if (!$result) {
    echo '执行失败'.mysqli_error($link);
}

// 创建客房的内部信息 房间号 房间类型 价格 面积 额定床位 是否有电脑(yes/no)
$sql = <<<xxx
create table if not exists room_info(
    room_id varchar(20) not null primary key, 
    type varchar(20) not null, 
    price int not null, 
    size double not null, 
    bed int not null,
    computer varchar(20) not null
)
xxx;
$result = mysqli_query($link, $sql);
if (!$result) {
    echo '执行失败'.mysqli_error($link);
}

// 创建入住信息表 订单流水号（先预定再入住） 客房号 入住日期 退房日期 押金 结账金额 消费金额
// 也许结账金额是正常金额，消费金额是优惠过后的？
$sql = <<<xxx
create table if not exists login(
    order_id int not null primary key,
    room_id varchar(20) not null,
    in_time varchar(20) not null,
    out_time varchar(20) not null,
    credit_money int not null,
    money int not null,
    real_money int not null
)
xxx;
$result = mysqli_query($link, $sql);
if (!$result) {
    echo '执行失败'.mysqli_error($link);
}

// 创建预定信息表 订单流水号 证件号 客房号 预定日期(yy-mm-dd) 预定入住日期(yy-mm-dd) 预定天数 此预定是否已完成
$sql = <<<xxx
create table if not exists pre_order(
    order_id int not null primary key auto_increment, 
    people_id varchar(20) not null,
    room_id varchar(20) not null,
    order_time varchar(20) not null,
    use_time varchar(20) not null,
    use_long_time int not null,
    done varchar(20) not null
)
xxx;
$result = mysqli_query($link, $sql);
if (!$result) {
    echo '执行失败'.mysqli_error($link);
}

// 创建用户找回密码的密保问题数据表
$sql = <<<xxx
create table if not exists `user_question` (
    `user` varchar(20) not null primary key,
    `question1` varchar(100) not null,
    `answer1` varchar(100) not null,
    `question2` varchar(100) not null,
    `answer2` varchar(100) not null,
    `question3` varchar(100) not null,
    `answer3` varchar(100) not null
)
xxx;
$result = mysqli_query($link, $sql);
if (!$result) {
    echo '执行失败'.mysqli_error($link);
}

// 创建管理员账户数据表
$sql = <<<xxx
create table if not exists `admin`(
    `user` varchar(20) not null primary key, 
    `pwdhash` varchar(255) not null,
    `level` int not null
)
xxx;
$result = mysqli_query($link, $sql);
if (!$result) {
    echo '执行失败'.mysqli_error($link);
}

// 查找管理员数据表中有没有root账户，没有则创建
$sql = 'select * from `admin`';
$result = mysqli_query($link, $sql);
if (!$result) {
    echo '执行失败'.mysqli_error($link);
}
$result = $result->fetch_all();
$flag = false;
for ($i = 0; $i < count($result); $i++) {
    if ($result[$i][0] == 'root') {
        $flag = true;
    }
}
if (!$flag) {
    $pwdhash = crypt('123456', 'salt');
    $sql = 'insert into `admin` values ("root", "'.$pwdhash.'", 1)';
    $result = mysqli_query($link, $sql);
    if (!$result) {
        echo '执行失败'.mysqli_error($link);
    }
}

// 查询数据库的root账户密码是不是123456，是则发出修改提醒
$sql = 'select * from `admin`';
$result = mysqli_query($link, $sql);
if (!$result) {
    echo '执行失败'.mysqli_error($link);
}
$result = $result->fetch_all();
$flag = false;
for ($i = 0; $i < count($result); $i++) {
    if ($result[$i][0] == 'root' && hash_equals($result[$i][1], crypt('123456', 'salt'))) {
        $flag = true;
    }
}
if ($flag) {
    echo '<p>初始系统会自动创建root账户，密码123456，请及时修改账号密码！</p>';
}

// 有提交则判断账号密码错误或者正确
$errmsg = $user = $password = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_POST['user'];
    $password = $_POST['password'];

    $sql = 'select * from `admin`';
    $result = mysqli_query($link, $sql);
    if (!$result) {
        echo '执行失败'.mysqli_error($link);
    }
    $result = $result->fetch_all();
    $correct = false;
    for ($i = 0; $i < count($result); $i++) {
        if ($result[$i][0] == $user && hash_equals($result[$i][1], crypt($password, 'salt'))) {
            $correct = true;
        }
    }
    if (!$correct) {
        $errmsg = '账号或密码错误，请检查后重新输入';
    }
    else {
        // 跳转到后台页面
        session_start();
        $_SESSION['user'] = $user;
        header('location: manager.php');
    }

}

?>

<p><?php echo $errmsg; ?></p>
<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
    <table>
        <tr>
            <td><label for='user'>用户名：</label></td>
            <td><input type='text' name='user' id='user' value='<?php echo $user?>' /></td>
        </tr>
        <tr>
            <td><label for='password'>密码：</label></td>
            <td><input type='password' name='password' id='password' value='<?php echo $password?>' /></td>
        </tr>
        <tr>
            <td>
                <input type="submit" value='登录'>
            </td>
        </tr>
    </table>
</form>
<p>如果忘记root密码，请到数据库删除root账户条目</p>