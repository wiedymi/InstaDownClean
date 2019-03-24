<?php
if(!isset($_POST)){ 
    echo "error";
}

$to      = "wiedymi0@gmail.com";
$subject = $_POST["name"];
$message = $_POST["text"];
$headers = 'From: '.$_POST["email"]. "\r\n" .
    'Reply-To: wiedymi0@gmail.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

mail($to, $subject, $message, $headers);

echo "ok";