<?php require_once './database/connection.php' ?>
<?php require_once './partials/session.php' ?>

<?php
$_POST = json_decode(file_get_contents('php://input'), true);

if (isset($_POST['submit'])) {
    $body = htmlspecialchars($_POST['body']);

    if (empty($body)) {
        echo json_encode(['errorBody' => 'Enter the task from PHP!']);
    } else {
        $id = $_SESSION['user']['id'];
        $sql = "INSERT INTO `tasks`(`body`, `user_id`) VALUES ('$body','$id')";
        $result = $conn->query($sql);
        if ($result) {
            echo json_encode(['success' => 'Magic has been spelled!']);
        } else {
            echo json_encode(['failure' => 'Magic has become shopper!']);
        }
    }
}
