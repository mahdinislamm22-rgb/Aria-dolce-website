<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Metodo non consentito.'
    ]);
    exit;
}

$autoload = __DIR__ . '/vendor/autoload.php';
if (!file_exists($autoload)) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'PHPMailer non installato. Esegui: composer require phpmailer/phpmailer'
    ]);
    exit;
}

require $autoload;

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!is_array($data)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Dati non validi.'
    ]);
    exit;
}

$nome = trim((string) ($data['nome'] ?? ''));
$email = trim((string) ($data['email'] ?? ''));
$telefono = trim((string) ($data['telefono'] ?? 'Non specificato'));
$messaggio = trim((string) ($data['messaggio'] ?? ''));

if ($nome === '' || $email === '' || $messaggio === '') {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Compila nome, email e messaggio prima di inviare.'
    ]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Inserisci un indirizzo email valido.'
    ]);
    exit;
}

$smtpHost = getenv('ARIA_SMTP_HOST') ?: 'smtp.gmail.com';
$smtpUsername = getenv('ARIA_SMTP_USERNAME') ?: 'ariadolce13@gmail.com';
$smtpPassword = getenv('ARIA_SMTP_PASSWORD') ?: '';
$smtpPort = (int) (getenv('ARIA_SMTP_PORT') ?: 587);
$smtpSecure = getenv('ARIA_SMTP_SECURE') ?: 'tls';
$fromEmail = getenv('ARIA_FROM_EMAIL') ?: 'ariadolce13@gmail.com';
$fromName = getenv('ARIA_FROM_NAME') ?: 'Aria Dolce';
$toEmail = getenv('ARIA_TO_EMAIL') ?: 'ariadolce13@gmail.com';
$ccEmail = getenv('ARIA_CC_EMAIL') ?: 'ariadolce@pec.it';

if ($smtpPassword === '') {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'SMTP non configurato. Imposta ARIA_SMTP_PASSWORD nel server.'
    ]);
    exit;
}

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = $smtpHost;
    $mail->SMTPAuth = true;
    $mail->Username = $smtpUsername;
    $mail->Password = $smtpPassword;
    $mail->SMTPSecure = $smtpSecure;
    $mail->Port = $smtpPort;

    $mail->setFrom($fromEmail, $fromName);
    $mail->addAddress($toEmail, 'Aria Dolce');
    if ($ccEmail !== '') {
        $mail->addCC($ccEmail, 'Aria Dolce PEC');
    }
    $mail->addReplyTo($email, $nome);

    $mail->isHTML(false);
    $mail->Subject = 'Nuova richiesta da sito web - ' . $nome;
    $mail->Body = "Nuova richiesta ricevuta dal sito web Aria Dolce\n\n";
    $mail->Body .= "Nome: {$nome}\n";
    $mail->Body .= "Email: {$email}\n";
    $mail->Body .= "Telefono: {$telefono}\n\n";
    $mail->Body .= "Messaggio:\n{$messaggio}\n";

    $mail->send();

    echo json_encode([
        'success' => true,
        'message' => 'Messaggio inviato con successo.'
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Errore SMTP: ' . $mail->ErrorInfo
    ]);
}
