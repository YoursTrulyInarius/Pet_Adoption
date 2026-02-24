<?php
require_once 'c:/xampp/htdocs/Pet_Adoption/config/mailer.php';
$result = sendEmail('test@example.com', 'Test Subject', 'Test Body');
var_dump($result);
?>
