<title>营收额统计</title>
<h1>营收额统计</h1>

<?php
// 连接数据库
$link = mysqli_connect('localhost:3308', 'root', '', 'bigwork');
if (!$link) {
    echo '数据库连接失败<br>';
    exit();
}

?>

<h3>今年统计图表</h3>
<?php
// 遍历已支付订单，统计每月金额
$total = array(0,0,0,0,0,0,0,0,0,0,0,0);    // 注意1月的下标是0
$sql = "select * from login";
$result = mysqli_query($link, $sql);
if (!$result) {
    echo '执行失败'.mysqli_error($link);
}
$result = $result->fetch_all();
for ($i = 0; $i < count($result); $i++) {
    // 分离出月份
    $temp = explode('-', $result[$i][3]);
    $month = $temp[1];
    // 在对应月份上加上金额
    $total[(int)$month - 1] += (int)$result[$i][6];
    // 绘图
    session_start();
    $_SESSION['graph_data'] = $total;
    echo '<img src="graph.php" alt="折线图">';
}


?>


<h3>账单详细一览</h3>
<table border="1">
    <tr>
        <th>订单号</th>
        <th>结账时间</th>
        <th>应结金额</th>
        <th>实际结算金额</th>
    </tr>
    <?php
        echo "<tr>";
        // 遍历已支付的订单
        $sql = "select * from login";
        $result = mysqli_query($link, $sql);
        if (!$result) {
            echo '执行失败'.mysqli_error($link);
        }
        $result = $result->fetch_all();
        for ($i = 0; $i < count($result); $i++) {
            echo <<<xxx
            <th>{$result[$i][0]}</th>
            <th>{$result[$i][3]}</th>
            <th>{$result[$i][5]}</th>
            <th>{$result[$i][6]}</th>
            xxx;
        }
        

        echo "</tr>";
    
    ?>
</table>