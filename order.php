<h1>大作业宾馆房间预定</h1>
<?php
$link = mysqli_connect('localhost:3308', 'root', '', 'bigwork');
if (!$link) {
    echo '数据库连接失败<br>';
    exit();
}
// 判断session有没有user，没有就是非法访问要跳回去
session_start();
if (!isset($_SESSION['user'])) {
    header('location: index.php');
    exit();
}

// 获取用户的历史消费金额（判断会员等级）
$sql = "select * from user_info where user=\"{$_SESSION['user']}\"";
$result = mysqli_query($link, $sql);
if (!$result) {
    echo '执行失败'.mysqli_error($link);
}
$result = $result->fetch_all();
$money = $result[0][3];
echo "<p>尊敬的{$_SESSION['user']}您好</p>";
if ($money > 10000) {
    $rate = 0.7;
    echo "<p>您是尊贵的钻石会员，可享受30%折扣</p>";
}
else if ($money > 5000) {
    $rate = 0.8;
    echo "<p>您是尊贵的铂金会员，可享受20%折扣</p>";
}
else if ($money > 3000) {
    $rate = 0.9;
    echo "<p>您是尊贵的黄金会员，可享受10%折扣</p>";
}
else if ($money > 1000) {
    $rate = 0.95;
    echo "<p>您是尊贵的高级会员，可享受5%折扣</p>";
}
else {
    $rate = 1.0;
    echo "<p>您是大众会员</p>";
}

// 处理post操作请求
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['operation'] == 'order') {
        // 将对应房间的状态改为被预定
        $sql = "update room set status=\"被预定\" where room_id=\"{$_POST['room_id']}\"";
        $result = mysqli_query($link, $sql);
        if (!$result) {
            echo '执行失败'.mysqli_error($link);
        }
        // 从数据库读取身份证号
        $sql = "select * from user_info where user=\"{$_SESSION['user']}\"";
        $result = mysqli_query($link, $sql);
        if (!$result) {
            echo '执行失败'.mysqli_error($link);
        }
        $result = $result->fetch_all();
        $people_id = $result[0][2];
        // 将预定操作写入pre_order
        $sql = <<<xxx
        insert into pre_order values (
            null,
            "{$people_id}",
            "{$_POST['room_id']}",
            "{$_POST['order_time']}",
            "{$_POST['use_time']}",
            "{$_POST['use_long_time']}",
            "no"
        )
        xxx;
        $result = mysqli_query($link, $sql);
        if (!$result) {
            echo '执行失败'.mysqli_error($link);
        }
    }
    else if ($_POST['operation'] == 'login') {
        // 将对应的房间状态改为空闲
        $sql = "update room set status=\"空闲\" where room_id=\"{$_POST['room_id']}\"";
        $result = mysqli_query($link, $sql);
        if (!$result) {
            echo '执行失败'.mysqli_error($link);
        }
        // 将对应的pre_order的done改为yes
        $sql = "update pre_order set done=\"yes\" where order_id=\"{$_POST['order_id']}\"";
        $result = mysqli_query($link, $sql);
        if (!$result) {
            echo '执行失败'.mysqli_error($link);
        }
        // 计入入住数据库
        $credit_money = 100;        // 押金默认全部100
        $money = (int)$_POST['price'] * (int)$_POST['use_long_time'];
        $real_money = $money * $rate;
        $sql = <<<xxx
        insert into login values (
            "{$_POST['order_id']}",
            "{$_POST['room_id']}",
            "{$_POST['in_time']}",
            "{$_POST['out_time']}",
            $credit_money,
            $money,
            $real_money
        )
        xxx;
        $result = mysqli_query($link, $sql);
        if (!$result) {
            echo '执行失败'.mysqli_error($link);
        }
        // 更新用户的已消费金额
        $now_money = (int)$_POST['now_money'] + $real_money;
        $sql = <<<xxx
        update user_info set money=$now_money where user="{$_SESSION['user']}"
        xxx;
        $result = mysqli_query($link, $sql);
        if (!$result) {
            echo '执行失败'.mysqli_error($link);
        }
    }
}

?>

<h3>您已预定的房间</h3>
<table border="1">
    <tr>
        <th>房间号</th>
        <th>房间类型</th>
        <th>房间楼层</th>
        <th>价格(元/天)</th>
        <th>大小(平方米)</th>
        <th>床位数量</th>
        <th>是否有电脑</th>
        <th>预定日期</th>
        <th>预定入住日期</th>
        <th>预定天数</th>
        <th>操作</th>
    </tr>
    <?php
    // 从数据库读取身份证号
    $sql = "select * from user_info where user=\"{$_SESSION['user']}\"";
    $result = mysqli_query($link, $sql);
    if (!$result) {
        echo '执行失败'.mysqli_error($link);
    }
    $result = $result->fetch_all();
    $people_id = $result[0][2];
    // 顺手记录一下当前消费了多少钱
    $now_money = $result[0][3];
    // 依据身份证号查询所有未完成的预订单
    $sql = <<<xxx
    select * from pre_order where people_id="$people_id" and done="no"
    xxx;
    $result = mysqli_query($link, $sql);
    if (!$result) {
        echo '执行失败'.mysqli_error($link);
    }
    $result = $result->fetch_all();
    for ($i = 0; $i < count($result); $i++) {
        // 根据查出来的房号，输出房子的类型，再根据类型，输出详细信息
        $sql = <<<xxx
        select * from room where room_id="{$result[$i][2]}"
        xxx;
        $room = mysqli_query($link, $sql);
        if (!$room) {
            echo '执行失败'.mysqli_error($link);
        }
        $room = $room->fetch_all();

        $sql = "select * from room_info where type=\"{$room[0][1]}\"";
        $info = mysqli_query($link, $sql);
        if (!$result) {
            echo '执行失败'.mysqli_error($link);
        }
        $info = $info->fetch_all();
        echo "<tr>";
        echo "<td>{$result[$i][2]}</td>";
        echo "<td>{$room[0][1]}</td>";
        echo "<td>{$room[0][3]}</td>";
        echo "<td>{$info[0][1]}</td>";
        echo "<td>{$info[0][2]}</td>";
        echo "<td>{$info[0][3]}</td>";
        echo "<td>{$info[0][4]}</td>";
        echo "<td>{$result[$i][3]}</td>";
        echo "<td>{$result[$i][4]}</td>";
        echo "<td>{$result[$i][5]}</td>";
        echo <<<xxx
        <form action="{$_SERVER['PHP_SELF']}" method="post">
            <td>
                <input type="hidden" name="operation" value="login">
                <input type="hidden" name="room_id" value="{$result[$i][2]}">
                <input type="hidden" name="order_id" value="{$result[$i][0]}">
                <input type="hidden" name="in_time" value="{$result[$i][4]}">
                <input type="hidden" name="price" value="{$info[0][1]}">
                <input type="hidden" name="use_long_time" value="{$result[$i][5]}">
                <input type="hidden" name="now_money" value="{$now_money}">
                <input type="text" name="out_time" value="" placeholder="退房日期">
                <button type="submit">入住</button>
            </td>
        </form>
        xxx;
        echo "</tr>";
    }
    ?>
</table>


<h3>可用房间列表</h3>
<table border="1">
    <tr>
        <th>房间号</th>
        <th>房间类型</th>
        <th>房间楼层</th>
        <th>价格(元/天)</th>
        <th>大小(平方米)</th>
        <th>床位数量</th>
        <th>是否有电脑</th>
        <th>预定日期</th>
        <th>预定入住日期</th>
        <th>预定天数</th>
        <th>操作</th>
    </tr>
    <?php
    // 查询所有空闲的房间
    $sql = "select * from room where status=\"空闲\"";
    $result = mysqli_query($link, $sql);
    if (!$result) {
        echo '执行失败'.mysqli_error($link);
    }
    $result = $result->fetch_all();
    for ($i = 0; $i < count($result); $i++) {
        // 根据房间类型查询该类型的详细信息
        $sql = "select * from room_info where type=\"{$result[$i][1]}\"";
        $info = mysqli_query($link, $sql);
        if (!$result) {
            echo '执行失败'.mysqli_error($link);
        }
        $info = $info->fetch_all();
        echo "<tr>";
        echo "<td>{$result[$i][0]}</td>";
        echo "<td>{$result[$i][1]}</td>";
        echo "<td>{$result[$i][3]}</td>";
        echo "<td>{$info[0][1]}</td>";
        echo "<td>{$info[0][2]}</td>";
        echo "<td>{$info[0][3]}</td>";
        echo "<td>{$info[0][4]}</td>";
        echo <<<xxx
        <form action="{$_SERVER['PHP_SELF']}" method="post">
            <td>
                <input type="text" name="order_time" placeholder="预定日期">
            </td>
            <td>
                <input type="text" name="use_time" placeholder="预定入住日期">
            </td>
            <td>
                <input type="text" name="use_long_time" placeholder="预定入住天数">
            </td>
            <td>
                <input type="hidden" name="operation" value="order">
                <input type="hidden" name="room_id" value="{$result[$i][0]}">
                <button type="submit">预定</button>
            </td>
        </form>
        xxx;
        echo "</tr>";
    }
    ?>
</table>