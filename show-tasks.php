<?php require_once './database/connection.php' ?>
<?php require_once './partials/session.php' ?>

<?php
$id = $_SESSION['user']['id'];
$sql = "SELECT * FROM `tasks` WHERE `user_id` = $id";
$result = $conn->query($sql);
$tasks = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode($tasks);