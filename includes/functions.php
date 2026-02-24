<?php
// includes/functions.php

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function redirect($path) {
    header('Location: ' . SITE_URL . $path);
    exit();
}

function flash($name = '', $message = '', $class = 'alert alert-info') {
    if (!empty($name)) {
        if (!empty($message) && empty($_SESSION[$name])) {
            $_SESSION[$name] = $message;
            $_SESSION[$name . '_class'] = $class;
        } elseif (empty($message) && !empty($_SESSION[$name])) {
            $class = !empty($_SESSION[$name . '_class']) ? $_SESSION[$name . '_class'] : '';
            echo '<div class="' . $class . '" id="msg-flash">' . $_SESSION[$name] . '</div>';
            unset($_SESSION[$name]);
            unset($_SESSION[$name . '_class']);
        }
    }
}

function timeAgo($timestamp) {
    if (!$timestamp) return "Unknown";
    $time = strtotime($timestamp);
    $diff = time() - $time;
    
    // If diff is negative (due to minor clock sync issues), treat as just now
    if ($diff < 60) return "Just now";
    
    $units = [
        31536000 => 'year',
        2592000 => 'month',
        604800 => 'week',
        86400 => 'day',
        3600 => 'hour',
        60 => 'minute'
    ];
    
    foreach ($units as $unit => $text) {
        if ($diff < $unit) continue;
        $numberOfUnits = floor($diff / $unit);
        return $numberOfUnits . ' ' . $text . (($numberOfUnits > 1) ? 's' : '') . ' ago';
    }
    return "Just now";
}

function getPetPrimaryImage($db, $pet_id) {
    $stmt = $db->prepare("SELECT image_path FROM pet_images WHERE pet_id = ? AND is_primary = 1 LIMIT 1");
    $stmt->execute([$pet_id]);
    $img = $stmt->fetch();
    if (!$img) {
        $stmt = $db->prepare("SELECT image_path FROM pet_images WHERE pet_id = ? LIMIT 1");
        $stmt->execute([$pet_id]);
        $img = $stmt->fetch();
    }
    return $img ? PET_IMAGE_PATH . $img['image_path'] : SITE_URL . '/assets/images/default-pet.png';
}
?>
