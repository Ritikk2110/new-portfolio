<?php
// contact.php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name    = trim($_POST["name"]);
    $email   = trim($_POST["email"]);
    $subject = trim($_POST["subject"]);
    $message = trim($_POST["message"]);

    // Replace with your actual email
    $to = "kr2060398@gmail.com";  

    $headers = "From: " . $name . " <" . $email . ">\r\n";
    $headers .= "Reply-To: " . $email . "\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    $body  = "New message from your portfolio site:\n\n";
    $body .= "Name: " . $name . "\n";
    $body .= "Email: " . $email . "\n";
    $body .= "Subject: " . $subject . "\n\n";
    $body .= "Message:\n" . $message . "\n";

    if (mail($to, $subject, $body, $headers)) {
        echo json_encode(["status" => "success", "message" => "Your message was sent successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Sorry, something went wrong."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
}
?>