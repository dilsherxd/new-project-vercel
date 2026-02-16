<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

/**
 * Encrypt Facebook password using standard ChaCha20-Poly1305
 */
function facebook_web_encrypt_password($key_id, $public_key_hex, $password, $version = 5) {
    $aes_key = random_bytes(32);
    $iv = random_bytes(SODIUM_CRYPTO_AEAD_CHACHA20POLY1305_IETF_NPUBBYTES); // 12 bytes
    $timestamp = time();

    // Encrypt using standard ChaCha20-Poly1305
    try {
        $ciphertext = sodium_crypto_aead_chacha20poly1305_ietf_encrypt(
            $password,
            (string)$timestamp,
            $iv,
            $aes_key
        );
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Encryption failed: ' . $e->getMessage()]);
        exit;
    }

    $public_key_bin = hex2bin($public_key_hex);
    if ($public_key_bin === false || strlen($public_key_bin) !== SODIUM_CRYPTO_BOX_PUBLICKEYBYTES) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid public_key_hex']);
        exit;
    }

    try {
        $encrypted_key = sodium_crypto_box_seal($aes_key, $public_key_bin);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Key encryption failed: ' . $e->getMessage()]);
        exit;
    }

    $len = pack("v", strlen($encrypted_key));
    $final_bytes = chr(1) . chr($key_id) . $len . $encrypted_key . substr($ciphertext, -16) . substr($ciphertext, 0, -16);

    return [
        'encrypted_password' => "#PWD_BROWSER:$version:$timestamp:" . base64_encode($final_bytes),
        'encrypted_key_hex' => bin2hex($encrypted_key),
        'timestamp' => $timestamp
    ];
}

// Support GET and POST
$key_id = $_REQUEST['key_id'] ?? null;
$public_key_hex = $_REQUEST['public_key_hex'] ?? null;
$password = $_REQUEST['password'] ?? null;

if (!$key_id || !$public_key_hex || !$password) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing key_id, public_key_hex, or password']);
    exit;
}

// Encrypt password and return JSON
echo json_encode(facebook_web_encrypt_password($key_id, $public_key_hex, $password));
