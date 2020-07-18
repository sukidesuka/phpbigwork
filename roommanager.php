<title>房间信息管理</title>
<h1>宾馆房间信息管理</h1>
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

// 处理post请求
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['operate'] == 'room_create') {
        $sql = <<<xxx
        insert into room values (
            "{$_POST['room_id']}",
            "{$_POST['type']}",
            "{$_POST['phone']}",
            {$_POST['stair']},
            "{$_POST['status']}"
        )
        xxx;
        $result = mysqli_query($link, $sql);
        if (!$result) {
            echo '执行失败'.mysqli_error($link);
        }
    }
    else if ($_POST['operate'] == 'info_create') {
        $sql = <<<xxx
        insert into room_info values(
            "{$_POST['type']}",
            {$_POST['price']},
            {$_POST['size']},
            {$_POST['bed']},
            "{$_POST['computer']}"
        )
        xxx;
        $result = mysqli_query($link, $sql);
        if (!$result) {
            echo '执行失败'.mysqli_error($link);
        }
    }
    else if ($_POST['operate'] == 'info_delete') {
        $sql = "delete from `room_info` where `type`=\"{$_POST['type']}\"";
        echo $sql;
        $result = mysqli_query($link, $sql);
        if (!$result) {
            echo '执行失败'.mysqli_error($link);
        }
    }
    else if ($_POST['operate'] == 'room_delete') {
        $sql = "delete from `room` where `room_id`=\"{$_POST['room_id']}\"";
        $result = mysqli_query($link, $sql);
        if (!$result) {
            echo '执行失败'.mysqli_error($link);
        }
    }
    else if ($_POST['operate'] == 'room_edit') {
        $sql = <<<xxx
        update `room`
        set room_id="{$_POST['room_id']}", type="{$_POST['type']}", phone="{$_POST['phone']}",
            stair={$_POST['stair']}, status="{$_POST['status']}"
        where room_id="{$_POST['ori_room_id']}"
        xxx;
        $result = mysqli_query($link, $sql);
        if (!$result) {
            echo '执行失败'.mysqli_error($link);
        }
    }
    else if ($_POST['operate'] == 'info_edit') {
        $sql = <<<xxx
        update `room_info`
        set type="{$_POST['type']}", price={$_POST['price']}, size={$_POST['size']},
            bed={$_POST['bed']}, computer="{$_POST['computer']}"
        where type="{$_POST['ori_type']}"
        xxx;
        $result = mysqli_query($link, $sql);
        if (!$result) {
            echo '执行失败'.mysqli_error($link);
        }
    }
    else {
        echo 'post提交了奇怪的东西<br>';
        echo var_dump($_POST);
    }
}

?>

<h3>新建房间信息</h3>
<table>
    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
        <input type="hidden" name="operate" value="room_create">
        <tr><td><input type="text" placeholder="客房号" name="room_id"></td></tr>
        <tr><td><input type="text" placeholder="房间类型" name="type"></td></tr>
        <tr><td><input type="text" placeholder="房间电话" name="phone"></td></tr>
        <tr><td><input type="text" placeholder="楼层" name="stair"></td></tr>
        <tr><td><input type="text" placeholder="客房状态" name="status"></td></tr>
        <tr><td><input type="submit" value="新建"></td></tr>
    </form>
</table>

<h3>新建房间类型信息</h3>
<table>
    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
        <input type="hidden" name="operate" value="info_create">
        <tr><td><input type="text" placeholder="房间类型" name="type"></td></tr>
        <tr><td><input type="text" placeholder="价格" name="price"></td></tr>
        <tr><td><input type="text" placeholder="面积" name="size"></td></tr>
        <tr><td><input type="text" placeholder="额定床位" name="bed"></td></tr>
        <tr><td><input type="text" placeholder="是否有电脑" name="computer"></td></tr>
        <tr><td><input type="submit" value="新建"></td></tr>
    </form>
</table>

<h3>已有的房间类型信息</h3>
<table border="1">
    <tr>
        <th>房间类型</th>
        <th>价格</th>
        <th>面积</th>
        <th>额定床位</th>
        <th>是否有电脑</th>
        <th>操作</th>
    </tr>
    <?php
    // 从数据库获取已有房间类型信息
    $sql = 'select * from `room_info`';
    $result = mysqli_query($link, $sql);
    if (!$result) {
        echo '执行失败'.mysqli_error($link);
    }
    $result = $result->fetch_all();
    for ($i = 0; $i < count($result); $i++) {
        
        echo '<tr>';
        echo <<<xxx
        <form action="{$_SERVER['PHP_SELF']}" method="post">
            <input type="hidden" name="operate" value="info_edit">
            <input type="hidden" name="ori_type" value="{$result[$i][0]}">
            <td><input type="text" value="{$result[$i][0]}" name="type"></td>
            <td><input type="text" value="{$result[$i][1]}" name="price"></td>
            <td><input type="text" value="{$result[$i][2]}" name="size"></td>
            <td><input type="text" value="{$result[$i][3]}" name="bed"></td>
            <td><input type="text" value="{$result[$i][4]}" name="computer"></td>
            <td>
                <input type="submit" value="修改">
                <form action="{$_SERVER['PHP_SELF']}" method="post">
                    <input type="hidden" name="operate" value="info_delete">
                    <input type="hidden" name="type" value="{$result[$i][0]}">
                    <input type="submit" value="删除">
                </form>
            </td>
        </form>
        
        xxx;
        echo '</tr>';
    }

    
    ?>
</table>

<h3>总房间列表</h3>
<table border="1">
    <tr>
        <th>房间号</th>
        <th>房间类型</th>
        <th>房间电话</th>
        <th>楼层</th>
        <th>房间状态</th>
        <th>操作</th>
    </tr>
    <?php 
    // 从数据库获取所有房间列表
    $sql = 'select * from `room`';
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