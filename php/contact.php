<?php
// php/contact.php
header('Content-Type: application/json; charset=utf-8');

// Load config
$config = require __DIR__ . '/config.php';

// Read input (JSON or form)
$input = file_get_contents('php://input');
$data = [];
if($input) {
    $json = json_decode($input, true);
    if(json_last_error() === JSON_ERROR_NONE) $data = $json;
}
if(empty($data)) {
    // fallback to form-data
    $data = $_POST;
}

// sanitize
$name = substr(trim($data['name'] ?? ''), 0, 200);
$email = substr(trim($data['email'] ?? ''), 0, 200);
$subject = substr(trim($data['subject'] ?? 'Website Contact'), 0, 200);
$message = trim($data['message'] ?? '');

if(!$name || !$email || !$message){
    http_response_code(400);
    echo json_encode(['success'=>false,'message'=>'Missing required fields']);
    exit;
}

// Try to save to database if configured
$dbSaved = false;
try {
    $db = $config['db'];
    if(!empty($db['name'])) {
        $pdo = new PDO("mysql:host={$db['host']};dbname={$db['name']};charset=utf8",$db['user'],$db['pass'],[
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        $stmt = $pdo->prepare("INSERT INTO contacts (name,email,subject,message,created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$name,$email,$subject,$message]);
        $dbSaved = true;
    }
} catch(PDOException $e) {
    // fail silently for DB errors, still try to send email
}

// Send email (simple mail). For production use SMTP via PHPMailer.
$to = $config['email']['to'] ?? 'ritikkumarofficial@gmail.com';
$from = $config['email']['from'] ?? 'no-reply@yourdomain.com';
$fromName = $config['email']['from_name'] ?? 'CreVate Website';

$headers  = "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/plain; charset=utf-8\r\n";
$headers .= "From: {$fromName} <{$from}>\r\n";
$headers .= "Reply-To: {$name} <{$email}>\r\n";

$body = "You have a new message from your website contact form\n\n";
$body .= "Name: $name\n";
$body .= "Email: $email\n";
$body .= "Subject: $subject\n\n";
$body .= "Message:\n$message\n\n";
$body .= "Sent: " . date('Y-m-d H:i:s') . "\n";

$mailSent = false;
try {
    $mailSent = mail($to, $subject, $body, $headers);
} catch(Exception $e) {
    $mailSent = false;
}

// Response
if($mailSent || $dbSaved){
    echo json_encode(['success'=>true,'saved'=>$dbSaved,'emailed'=>$mailSent]);
} else {
    http_response_code(500);
    echo json_encode(['success'=>false,'message'=>'Failed to deliver message.']);
}

/* --- Notes for using SMTP (recommended) ---
If mail() doesn't work on your host, use PHPMailer with SMTP:
1. composer require phpmailer/phpmailer
2. Replace the mail() call above with PHPMailer code:
   $mail = new PHPMailer(true);
   $mail->isSMTP();
   $mail->Host = 'smtp.example.com';
   $mail->SMTPAuth = true;
   $mail->Username = 'smtp-user';
   $mail->Password = 'smtp-pass';
   $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
   $mail->Port = 587;
   $mail->setFrom($from,$fromName);
   $mail->addAddress($to);
   $mail->Subject = $subject;
   $mail->Body = $body;
   $mail->send();
*/
