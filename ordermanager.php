<title>房间预定管理</title>
<h1>房间预定管理</h1>

<h3>房间预定列表</h3>
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

// 



?>

<table border="1">
    <tr>
        <th>预定订单号</th>
        <th>客户身份证</th>
        <th>房间号</th>
        <th>预定时间</th>
        <th>预定入住时间</th>
        <th>预定入住时长</th>
    </tr>
    <?php 
    // 从数据库获取所有预定列表
    $sql = 'select * from `pre_order`';
    $result = mysqli_query($link, $sql);
    if (!$result) {
        echo '执行失败'.mysqli_error($link);
    }
    $result = $result->fetch_all();
    for ($i = 0; $i < count($result); $i++) {
        echo '<tr>';
        echo <<<xxx
        <form action="{$_SERVER['PHP_SELF']}" method="post">
            <input type="hidden" name="operate" value="room_edit">
            <input type="hidden" name="ori_room_id" value="{$result[$i][0]}">
            <td><input type="text" value="{$result[$i][0]}" name="room_id"></td>
            <td><input type="text" value="{$result[$i][1]}" name="type"></td>
            <td><input type="text" value="{$result[$i][2]}" name="phone"></td>
            <td><input type="text" value="{$result[$i][3]}" name="stair"></td>
            <td><input type="text" value="{$result[$i][4]}" name="status"></td>
            <td>
                <input type="submit" value="修改">
                <form action="{$_SERVER['PHP_SELF']}" method="post">
                    <input type="hidden" name="operate" value="room_delete">
                    <input type="hidden" name="room_id" value="{$result[$i][0]}">
                    <input type="submit" value="删除">
                </form>
            </td>
        </form>
        
        xxx;
        echo '</tr>';
    }

    ?>
</table>