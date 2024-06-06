<?php
function isAdmin($user_id, $mysqli) {
    $stmt = $mysqli->prepare("SELECT is_admin FROM users WHERE id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $stmt->bind_result($is_admin);
    $stmt->fetch();
    $stmt->close();

    return $is_admin;
}
?>