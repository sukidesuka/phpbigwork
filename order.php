<h1>大作业宾馆房间预定</h1>
<?php
$link = mysqli_connect('localhost:3308', 'root', '', 'bigwork');
if (!$link) {
    echo '数据库连接失败<br>';
    exit();
}
// 获取用户的历史消费金额（判断会员等级）
$sql = 'select * from user_info';
$result = mysqli_query($link, $sql);
if (!$result) {
    echo '执行失败'.mysqli_error($link);
}
$result = $result->fetch_all();
$money = 0;
for ($i = 0; $i < count($result); $i++) {
    if ($result[$i][0] == $_SESSION['user']) {
        
    }
}


?>