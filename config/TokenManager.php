<?php
namespace config;

class TokenManager {
    private static $secret_key = 'your_very_secret_key_here_change_in_production'; // Replace with a strong, environment-specific key
    private static $token_expiration = 3600; // 1 hour

    /**
     * Generate a JWT token for a user
     * @param array $user User data to encode in the token
     * @return string Generated token
     */
    public static function generateToken($user) {
        // Header
        $header = json_encode([
            'typ' => 'JWT',
            'alg' => 'HS256'
        ]);

        // Payload
        $payload = json_encode([
            'user_id' => $user['id'] ?? $user['user_id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'exp' => time() + self::$token_expiration
        ]);

        // Encode Header
        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));

        // Encode Payload
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

        // Create Signature
        $signature = hash_hmac('sha256', "$base64UrlHeader.$base64UrlPayload", self::$secret_key, true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        // Combine all parts
        $jwt = "$base64UrlHeader.$base64UrlPayload.$base64UrlSignature";

        return $jwt;
    }

    /**
     * Validate and decode a JWT token
     * @param string $token JWT token to validate
     * @return array|false Decoded token data or false if invalid
     */
    public static function validateToken($token) {
        // Split the token
        $tokenParts = explode('.', $token);
        
        // Check if token has 3 parts
        if (count($tokenParts) !== 3) {
            return false;
        }

        list($header, $payload, $signature) = $tokenParts;

        // Verify signature
        $validSignature = hash_hmac('sha256', "$header.$payload", self::$secret_key, true);
        $base64UrlValidSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($validSignature));

        // Check if signatures match
        if (!hash_equals($base64UrlValidSignature, $signature)) {
            return false;
        }

        // Decode payload
        $payloadJson = base64_decode(str_replace(['-', '_'], ['+', '/'], $payload));
        $payloadData = json_decode($payloadJson, true);

        // Check expiration
        if (isset($payloadData['exp']) && $payloadData['exp'] < time()) {
            return false;
        }

        return $payloadData;
    }

    /**
     * Get token from multiple sources
     * @return string|null Token or null if not found
     */
    public static function getTokenFromHeader() {
        // Check session first
        if (isset($_SESSION['token'])) {
            return $_SESSION['token'];
        }

        // Check Authorization header
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? null;

        if ($authHeader) {
            // Remove 'Bearer ' prefix if present
            return str_replace('Bearer ', '', $authHeader);
        }

        // Check query parameters (for GET requests like kanban)
        $token = $_GET['token'] ?? $_POST['token'] ?? null;

        return $token;
    }
}
