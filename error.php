<?php
header('content-type:text/html;charset=utf-8');
echo '<h1>';
echo $_GET['message'];
echo '</h1>';

if (isset($_GET['url'])) {
    echo "<a href=\"{$_GET['url']}\">{$_GET['note']}</a>";
}
?>