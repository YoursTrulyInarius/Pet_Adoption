<?php
require_once 'config/config.php';
require_once 'config/database.php';
$db = Database::getConnection();

echo "PHP Current Time (Configured TZ): " . date('Y-m-d H:i:s') . "\n";
echo "PHP Timezone: " . date_default_timezone_get() . "\n";

$res = $db->query("SELECT NOW() as db_now")->fetch();
echo "Database NOW(): " . $res['db_now'] . "\n";

$res = $db->query("SELECT @@session.time_zone as tz, @@global.time_zone as gtz")->fetch();
echo "DB Session TZ: " . $res['tz'] . "\n";
echo "DB Global TZ: " . $res['gtz'] . "\n";
?>
