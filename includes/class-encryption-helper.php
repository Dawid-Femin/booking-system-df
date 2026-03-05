<?php
/**
 * Encryption helper for sensitive data.
 *
 * @since      1.0.0
 * @package    Booking_System_DF
 * @author     Dawid Femin
 */
class Encryption_Helper {

    private static $key = null;

    private static function get_key() {
        if (self::$key !== null) {
            return self::$key;
        }

        $key = get_option('booking_system_df_encryption_key');
        
        if (!$key) {
            if (function_exists('sodium_crypto_secretbox_keygen')) {
                $key = base64_encode(sodium_crypto_secretbox_keygen());
            } else {
                $key = base64_encode(openssl_random_pseudo_bytes(32));
            }
            update_option('booking_system_df_encryption_key', $key);
        }
        
        self::$key = base64_decode($key);
        return self::$key;
    }

    public static function encrypt($plaintext) {
        if (empty($plaintext)) {
            return '';
        }

        try {
            $key = self::get_key();
            
            if (function_exists('sodium_crypto_secretbox')) {
                $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
                $ciphertext = sodium_crypto_secretbox($plaintext, $nonce, $key);
                return base64_encode($nonce . $ciphertext);
            } else {
                $iv = openssl_random_pseudo_bytes(16);
                $ciphertext = openssl_encrypt($plaintext, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
                return base64_encode($iv . $ciphertext);
            }
        } catch (Exception $e) {
            Booking_System_Logger::log_error('Encryption failed: ' . $e->getMessage());
            return '';
        }
    }

    public static function decrypt($encrypted) {
        if (empty($encrypted)) {
            return '';
        }

        try {
            $key = self::get_key();
            $data = base64_decode($encrypted);
            
            if (function_exists('sodium_crypto_secretbox_open')) {
                $nonce = mb_substr($data, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, '8bit');
                $ciphertext = mb_substr($data, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null, '8bit');
                $plaintext = sodium_crypto_secretbox_open($ciphertext, $nonce, $key);
                
                if ($plaintext === false) {
                    throw new Exception('Decryption failed');
                }
                
                return $plaintext;
            } else {
                $iv = substr($data, 0, 16);
                $ciphertext = substr($data, 16);
                return openssl_decrypt($ciphertext, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
            }
        } catch (Exception $e) {
            Booking_System_Logger::log_error('Decryption failed: ' . $e->getMessage());
            return '';
        }
    }
}
