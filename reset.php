<?php
$conn = mysqli_connect('localhost', 'root', '');
if ($conn) {
    mysqli_query($conn, 'DROP DATABASE IF EXISTS shopping_db');
}
header('Location: index.php');
exit;
?>
