<title>管理员账户管理</title>

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

// 获取当前账户权限等级，待会儿只能对权限比自己低的账户操作
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

// 处理post请求
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['operate'] == 'create') {
        // 判断设置的权限是不是合法
        if ((int)$_POST['permission'] > $level) {
            // 在数据库添加条目
            $sqluser = $_POST['user'];
            $sqlpwd = crypt($_POST['password'], 'salt');
            $sql = 'insert into admin values ("'.$sqluser.'", "'.$sqlpwd.'", '.$_POST['permission'].')';
            $result = mysqli_query($link, $sql);
            if (!$result) {
                echo '执行失败'.mysqli_error($link);
            }
        }
    }
    else if ($_POST['operate'] == 'edit') {
        // 判断权限是否足够
        if ((int)$_POST['permission'] > $level || $_POST['user'] == $_SESSION['user']) {
            // 修改数据库条目
            $sqlpwd = crypt($_POST['password'], 'salt');
            $sql = 'update `admin` set `pwdhash`="'.$sqlpwd.'" where `user`="'.$_POST['user'].'"';
            $result = mysqli_query($link, $sql);
            if (!$result) {
                echo '执行失败'.mysqli_error($link);
            }
        }
        else {
            echo '权限不足<br>';
        }

    }
    else if ($_POST['operate'] == 'delete') {
        // 判断权限是否足够
        if ((int)$_POST['permission'] > $level) {
            // 删除数据库条目
            $sql = "delete from `admin` where `user`=\"".$_POST['user']."\"";
            $result = mysqli_query($link, $sql);
            if (!$result) {
                echo '执行失败'.mysqli_error($link);
            }
        }
    }
    else {
        echo 'post提交了奇怪的东西<br>';
        var_dump($_POST);
    }
}

?>

<h1>管理员账户管理</h1>
<table border="1">
    <tr>
        <th>当前用户</th>
        <th>当前权限等级</th>
    </tr>
    <tr>
        <td>
            <?php echo $_SESSION['user']; ?>
        </td>
        <td>
            <?php echo $level; ?>
        </td>
    </tr>
</table>

<h3>新建管理员</h3>
<table>
    <form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post">
        <input type="hidden" name="operate" value="create">
        <tr><td><input type="text" placeholder="用户名" name="user"></td></tr>
        <tr><td><input type="password" placeholder="密码" name="password"></td></tr>
        <tr><td><input type="text" placeholder="权限（数字必须高于自身）" name="permission"></td></tr>
        <tr><td><input type="submit" value="新建"></td></tr>
    </form>
</table>

<h3>管理员账户列表</h3>
<table border="1">
    <tr>
        <th>用户名</th>
        <th>权限</th>
        <th>密码修改</th>
        <th>账户删除</th>
    </tr>

    <?php
    $sql = 'select * from `admin`';
    $result = mysqli_query($link, $sql);
    if (!$result) {
        echo '执行失败'.mysqli_error($link);
    }
    $result = $result->fetch_all();
    for ($i = 0; $i < count($result); $i++) {
        echo '<tr>';
        // 显示用户名
        echo '<td>'.$result[$i][0].'</td>';
        echo '<td>'.$result[$i][2].'</td>';

        // 根据权限显示修改密码按钮（可修改自己或者权限比自己低的用户）
        if ($level < $result[$i][2] || $result[$i][0] == $_SESSION['user']){
            $self = $_SERVER['PHP_SELF'];
            $editUser = $result[$i][0];
            $sqlpermission = $result[$i][2];
            echo <<<xxx
            <form action="$self" method="post">
                <td>
                    <input type="hidden" name="operate" value="edit">
                    <input type="hidden" name="user" value="$editUser">
                    <input type="hidden" name="permission" value="$sqlpermission">
                    <input type="text" name="password" placeholder="输入要修改的密码">
                    <input type="submit" value="修改密码">
                    </td>
            </form>
            xxx;
        }
        else {
            echo "<td></td>";
        }

        // 根据权限显示账户删除按钮
        if ($level < $result[$i][2]) {
            $self = $_SERVER['PHP_SELF'];
            $delUser = $result[$i][0];
            $sqlpermission = $result[$i][2];
            echo <<<xxx
            <td>
                <form action="$self" method="post">
                    <input type="hidden" name="operate" value="delete">
                    <input type="hidden" name="user" value="$delUser">
                    <input type="hidden" name="permission" value="$sqlpermission">
                    <input type="submit" value="删除账户">
                </form>
            </td>
            xxx;
        }
        else {
            echo "<td></td>";
        }

        
        echo '</tr>';
    }
    ?>
</table>