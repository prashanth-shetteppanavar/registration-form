<?php
/**
 * Utility functions for the registration form application
 */

/**
 * Validate email address
 * @param string $email
 * @return bool
 */
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Validate phone number
 * @param string $phone
 * @return bool
 */
function validate_phone($phone) {
    return preg_match('/^[\+]?[0-9]{7,15}$/', $phone);
}

/**
 * Validate date of birth (must be at least 16 years old)
 * @param string $dob
 * @return bool
 */
function validate_dob($dob) {
    $dob_timestamp = strtotime($dob);
    $age = floor((time() - $dob_timestamp) / 31556926);
    return $age >= 16;
}

/**
 * Sanitize string input
 * @param string $input
 * @return string
 */
function sanitize_input($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Generate CSRF token
 * @return string
 */
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF token
 * @param string $token
 * @return bool
 */
function validate_csrf_token($token) {
    return !empty($token) && !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Validate file upload
 * @param array $file
 * @return array ['valid' => bool, 'error' => string, 'path' => string]
 */
function validate_file_upload($file) {
    $result = ['valid' => false, 'error' => '', 'path' => ''];
    
    if (empty($file) || $file['error'] !== UPLOAD_ERR_OK) {
        $result['error'] = 'No file uploaded or upload error';
        return $result;
    }
    
    $file_name = $file['name'];
    $file_tmp = $file['tmp_name'];
    $file_size = $file['size'];
    $file_type = $file['type'];
    
    // Validate file size (max 2MB)
    if ($file_size > 2 * 1024 * 1024) {
        $result['error'] = 'File size exceeds 2MB limit';
        return $result;
    }
    
    // Validate file type
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png'];
    $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $allowed_extensions = ['jpg', 'jpeg', 'png'];
    
    if (!in_array($file_type, $allowed_types) || !in_array($file_extension, $allowed_extensions)) {
        $result['error'] = 'Only JPG, JPEG, and PNG files are allowed';
        return $result;
    }
    
    // Generate unique filename and move file
    $new_filename = uniqid('photo_') . '.' . $file_extension;
    $upload_path = 'uploads/' . $new_filename;
    
    if (move_uploaded_file($file_tmp, $upload_path)) {
        $result['valid'] = true;
        $result['path'] = $upload_path;
    } else {
        $result['error'] = 'Failed to upload photo';
    }
    
    return $result;
}

/**
 * Save submission to JSON file
 * @param array $data
 * @return bool
 */
function save_submission($data) {
    $submissions_file = 'submissions.json';
    
    // Read existing submissions
    $submissions = [];
    if (file_exists($submissions_file)) {
        $submissions_json = file_get_contents($submissions_file);
        $submissions = json_decode($submissions_json, true) ?: [];
    }
    
    // Add new submission
    $submissions[] = $data;
    
    // Save submissions to file
    return file_put_contents($submissions_file, json_encode($submissions, JSON_PRETTY_PRINT)) !== false;
}

/**
 * Get all submissions
 * @return array
 */
function get_submissions() {
    $submissions_file = 'submissions.json';
    
    if (file_exists($submissions_file)) {
        $submissions_json = file_get_contents($submissions_file);
        return json_decode($submissions_json, true) ?: [];
    }
    
    return [];
}

/**
 * Log submission event
 * @param string $message
 * @return void
 */
function log_submission($message) {
    $log_file = 'logs/submissions.log';
    
    // Create logs directory if it doesn't exist
    if (!is_dir('logs')) {
        mkdir('logs', 0755, true);
    }
    
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    $log_entry = "[{$timestamp}] [IP: {$ip}] {$message}" . PHP_EOL;
    
    file_put_contents($log_file, $log_entry, FILE_APPEND | LOCK_EX);
}

/**
 * Rate limiting function
 * @param string $identifier
 * @param int $max_requests
 * @param int $time_window
 * @return bool
 */
function check_rate_limit($identifier, $max_requests = 5, $time_window = 60) {
    $rate_limit_file = 'logs/rate_limit.json';
    
    // Create logs directory if it doesn't exist
    if (!is_dir('logs')) {
        mkdir('logs', 0755, true);
    }
    
    // Read existing rate limit data
    $rate_limit_data = [];
    if (file_exists($rate_limit_file)) {
        $rate_limit_json = file_get_contents($rate_limit_file);
        $rate_limit_data = json_decode($rate_limit_json, true) ?: [];
    }
    
    $now = time();
    $key = md5($identifier);
    
    // Remove expired entries
    foreach ($rate_limit_data as $k => $timestamps) {
        $rate_limit_data[$k] = array_filter($timestamps, function($timestamp) use ($now, $time_window) {
            return ($now - $timestamp) < $time_window;
        });
        
        // Remove empty arrays
        if (empty($rate_limit_data[$k])) {
            unset($rate_limit_data[$k]);
        }
    }
    
    // Check if limit exceeded
    if (isset($rate_limit_data[$key]) && count($rate_limit_data[$key]) >= $max_requests) {
        return false; // Rate limit exceeded
    }
    
    // Add current request
    if (!isset($rate_limit_data[$key])) {
        $rate_limit_data[$key] = [];
    }
    $rate_limit_data[$key][] = $now;
    
    // Save rate limit data
    file_put_contents($rate_limit_file, json_encode($rate_limit_data));
    
    return true; // Within rate limit
}