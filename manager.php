<title>宾馆管理系统</title>
<h1>宾馆管理系统</h1>

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

// 获取当前账户权限等级
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




?>

<table>
    <tr>
        <td>
            <?php echo "当前用户：".$_SESSION['user'] ?>
        </td>
        <td>
            <?php echo "当前权限等级：$level"; ?>
        </td>
    </tr>
    <tr>
        <td>
            <a href='adminaccountmanager.php'><button type="button">管理员账户管理</button></a>
        </td>
    </tr>
    <?php
    // 账户权限小于3才显示这个
    if ($level < 3) {
        echo <<<xxx
        <tr>
            <td>
                <a href='useraccountmanager.php'><button type="button">用户账户管理</button></a>
            </td>
        </tr>
        xxx;
    }
    
    ?>
    <tr>
        <td>
            <a href='roommanager.php'><button type="button">房间信息管理</button></a>
        </td>
    </tr>
    <tr>
        <td>
            <a href="ordermanager.php"><button type="button">房间预定管理</button></a>
        </td>
    </tr>
    <!-- <tr>
        <td>
            <a href="ordermanager.php"><button type="button">房间入住管理</button></a>
        </td>
    </tr> -->
    <tr>
        <td>
            <a href="ordermanager.php"><button type="button">宾馆财务管理</button></a>
        </td>
    </tr>

</table>