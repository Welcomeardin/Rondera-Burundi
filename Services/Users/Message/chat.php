<?php
// chat.php
$title = 'Chat';
ob_start();
session_start();
?>

<?php
$content = ob_get_clean();
include __DIR__ . '/../template/global.php';
?>