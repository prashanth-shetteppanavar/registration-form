<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

// Function to respond with JSON
function respond($arr) {
    echo json_encode($arr);
    exit;
}

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(['status' => 'error', 'message' => 'Invalid request method']);
}

// CSRF token validation
if (empty($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    respond(['status' => 'error', 'message' => 'Invalid CSRF token']);
}

// Get and sanitize form data
$first_name = trim($_POST['first_name'] ?? '');
$last_name = trim($_POST['last_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$gender = trim($_POST['gender'] ?? '');
$dob = trim($_POST['dob'] ?? '');
$address1 = trim($_POST['address1'] ?? '');
$address2 = trim($_POST['address2'] ?? '');
$city = trim($_POST['city'] ?? '');
$state = trim($_POST['state'] ?? '');
$country = trim($_POST['country'] ?? '');
$qualification = trim($_POST['qualification'] ?? '');
$bio = trim($_POST['bio'] ?? '');
$agree = isset($_POST['agree']);

// Skills array
$skills = isset($_POST['skills']) ? $_POST['skills'] : [];

// Validation errors array
$errors = [];

// Validate required fields
if (empty($first_name)) {
    $errors['first_name'] = 'First name is required';
}

if (empty($last_name)) {
    $errors['last_name'] = 'Last name is required';
}

if (empty($email)) {
    $errors['email'] = 'Email is required';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Please enter a valid email address';
}

if (empty($phone)) {
    $errors['phone'] = 'Phone number is required';
} elseif (!preg_match('/^[\+]?[0-9]{7,15}$/', $phone)) {
    $errors['phone'] = 'Please enter a valid phone number (7-15 digits)';
}

if (empty($gender)) {
    $errors['gender'] = 'Please select your gender';
}

if (empty($dob)) {
    $errors['dob'] = 'Date of birth is required';
} else {
    // Check if user is at least 16 years old
    $dob_timestamp = strtotime($dob);
    $age = floor((time() - $dob_timestamp) / 31556926);
    if ($age < 16) {
        $errors['dob'] = 'You must be at least 16 years old';
    }
}

if (empty($address1)) {
    $errors['address1'] = 'Address line 1 is required';
}

if (empty($city)) {
    $errors['city'] = 'City is required';
}

if (empty($state)) {
    $errors['state'] = 'State/Province is required';
}

// Server-side allowed states/provinces mapping (abbr lower => proper name)
$stateMap = [
    'al' => 'Alabama','ak' => 'Alaska','az' => 'Arizona','ar' => 'Arkansas','ca' => 'California',
    'co' => 'Colorado','ct' => 'Connecticut','de' => 'Delaware','fl' => 'Florida','ga' => 'Georgia',
    'hi' => 'Hawaii','id' => 'Idaho','il' => 'Illinois','in' => 'Indiana','ia' => 'Iowa',
    'ks' => 'Kansas','ky' => 'Kentucky','la' => 'Louisiana','me' => 'Maine','md' => 'Maryland',
    'ma' => 'Massachusetts','mi' => 'Michigan','mn' => 'Minnesota','ms' => 'Mississippi','mo' => 'Missouri',
    'mt' => 'Montana','ne' => 'Nebraska','nv' => 'Nevada','nh' => 'New Hampshire','nj' => 'New Jersey',
    'nm' => 'New Mexico','ny' => 'New York','nc' => 'North Carolina','nd' => 'North Dakota','oh' => 'Ohio',
    'ok' => 'Oklahoma','or' => 'Oregon','pa' => 'Pennsylvania','ri' => 'Rhode Island','sc' => 'South Carolina',
    'sd' => 'South Dakota','tn' => 'Tennessee','tx' => 'Texas','ut' => 'Utah','vt' => 'Vermont',
    'va' => 'Virginia','wa' => 'Washington','wv' => 'West Virginia','wi' => 'Wisconsin','wy' => 'Wyoming',
    // Canada
    'bc' => 'British Columbia','on' => 'Ontario','qc' => 'Quebec','ab' => 'Alberta','nl' => 'Newfoundland and Labrador',
    'ns' => 'Nova Scotia','mb' => 'Manitoba','sk' => 'Saskatchewan','pe' => 'Prince Edward Island','nt' => 'Northwest Territories',
    'yt' => 'Yukon','nu' => 'Nunavut',
    // India
    'ap' => 'Andhra Pradesh','ar' => 'Arunachal Pradesh','as' => 'Assam','br' => 'Bihar','cg' => 'Chhattisgarh',
    'ga' => 'Goa','gj' => 'Gujarat','hr' => 'Haryana','hp' => 'Himachal Pradesh','jh' => 'Jharkhand',
    'ka' => 'Karnataka','kl' => 'Kerala','mp' => 'Madhya Pradesh','mh' => 'Maharashtra','mn' => 'Manipur',
    'ml' => 'Meghalaya','mz' => 'Mizoram','nl' => 'Nagaland','or' => 'Odisha','pb' => 'Punjab',
    'rj' => 'Rajasthan','sk' => 'Sikkim','tn' => 'Tamil Nadu','tg' => 'Telangana','tr' => 'Tripura',
    'up' => 'Uttar Pradesh','uk' => 'Uttarakhand','wb' => 'West Bengal','dl' => 'Delhi','py' => 'Puducherry',
    // Australia
    'nsw' => 'New South Wales','vic' => 'Victoria','qld' => 'Queensland','wa' => 'Western Australia','sa' => 'South Australia',
    'tas' => 'Tasmania','nt' => 'Northern Territory','act' => 'Australian Capital Territory'
];

// If state provided and not empty, accept abbreviations or full names (case-insensitive)
if (!empty($state)) {
    $stLower = strtolower($state);
    // If user entered abbreviation like 'CA' or 'ca'
    if (isset($stateMap[$stLower])) {
        $state = $stateMap[$stLower];
    } else {
        // Try to match full name (case-insensitive) to one of the map values
        $found = false;
        foreach ($stateMap as $abbr => $proper) {
            if (strtolower($proper) === $stLower) {
                $state = $proper;
                $found = true;
                break;
            }
        }
        if (!$found) {
            $errors['state'] = 'Please select a valid state or province';
        }
    }
}

// Normalize country input: accept codes like 'US','UK','IN','CA','AU' or full names
$country = trim($country);
$countryMap = [
    'us' => 'United States', 'usa' => 'United States', 'united states' => 'United States',
    'uk' => 'United Kingdom', 'gb' => 'United Kingdom', 'united kingdom' => 'United Kingdom',
    'ca' => 'Canada', 'can' => 'Canada', 'canada' => 'Canada',
    'au' => 'Australia', 'australia' => 'Australia',
    'in' => 'India', 'india' => 'India',
    'de' => 'Germany', 'germany' => 'Germany',
    'fr' => 'France', 'france' => 'France'
];

if (!empty($country)) {
    $cLower = strtolower($country);
    if (isset($countryMap[$cLower])) {
        $country = $countryMap[$cLower];
    } else {
        // Try matching common full-name variants
        foreach ($countryMap as $k => $v) {
            if (strtolower($v) === $cLower) {
                $country = $v;
                break;
            }
        }
        // If still not normalized, keep as-is (allow 'Other')
    }
}

if (empty($country)) {
    $errors['country'] = 'Country is required';
}

if (empty($agree)) {
    $errors['agree'] = 'You must agree to the terms and conditions';
}

// Handle file upload
$photo_url = '';
if (!empty($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $file_name = $_FILES['photo']['name'];
    $file_tmp = $_FILES['photo']['tmp_name'];
    $file_size = $_FILES['photo']['size'];
    $file_type = $_FILES['photo']['type'];
    
    // Validate file size (max 2MB)
    if ($file_size > 2 * 1024 * 1024) {
        $errors['photo'] = 'File size exceeds 2MB limit';
    } else {
        // Validate file type
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png'];
        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png'];
        
        if (!in_array($file_type, $allowed_types) || !in_array($file_extension, $allowed_extensions)) {
            $errors['photo'] = 'Only JPG, JPEG, and PNG files are allowed';
        } else {
            // Generate unique filename and move file
            $new_filename = uniqid('photo_') . '.' . $file_extension;
            $upload_path = 'uploads/' . $new_filename;
            
            if (move_uploaded_file($file_tmp, $upload_path)) {
                $photo_url = $upload_path;
            } else {
                $errors['photo'] = 'Failed to upload photo';
            }
        }
    }
}

// If there are validation errors, return them
if (!empty($errors)) {
    respond(['status' => 'error', 'errors' => $errors]);
}

// Save submission data
$dataDir = __DIR__ . '/data';
$submissions_file = $dataDir . '/submissions.json';
if (!is_dir($dataDir)) {
    @mkdir($dataDir, 0755, true);
}
$submission_data = [
    'timestamp' => date('Y-m-d H:i:s'),
    'ip' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown',
    'first_name' => htmlspecialchars($first_name, ENT_QUOTES, 'UTF-8'),
    'last_name' => htmlspecialchars($last_name, ENT_QUOTES, 'UTF-8'),
    'email' => htmlspecialchars($email, ENT_QUOTES, 'UTF-8'),
    'phone' => htmlspecialchars($phone, ENT_QUOTES, 'UTF-8'),
    'gender' => htmlspecialchars($gender, ENT_QUOTES, 'UTF-8'),
    'dob' => htmlspecialchars($dob, ENT_QUOTES, 'UTF-8'),
    'address1' => htmlspecialchars($address1, ENT_QUOTES, 'UTF-8'),
    'address2' => htmlspecialchars($address2, ENT_QUOTES, 'UTF-8'),
    'city' => htmlspecialchars($city, ENT_QUOTES, 'UTF-8'),
    'state' => htmlspecialchars($state, ENT_QUOTES, 'UTF-8'),
    'country' => htmlspecialchars($country, ENT_QUOTES, 'UTF-8'),
    'qualification' => htmlspecialchars($qualification, ENT_QUOTES, 'UTF-8'),
    'skills' => array_map(function($skill) {
        return htmlspecialchars($skill, ENT_QUOTES, 'UTF-8');
    }, $skills),
    'bio' => htmlspecialchars($bio, ENT_QUOTES, 'UTF-8'),
    'photo_url' => $photo_url
];

// Read existing submissions
$submissions = [];
if (file_exists($submissions_file)) {
    $submissions_json = file_get_contents($submissions_file);
    $submissions = json_decode($submissions_json, true) ?: [];
}

// Add new submission
$submissions[] = $submission_data;

// Save submissions to file
file_put_contents($submissions_file, json_encode($submissions, JSON_PRETTY_PRINT));

// Count submissions for confirmation display
$submissions_count = count($submissions);

// Build confirmation HTML
ob_start();
?>
<div class="confirmation-container fade-in">
    <div class="confirmation-header">
        <h2>Application Submitted Successfully!</h2>
        <p>Thank you for your submission. Here's a summary of your information:</p>
        <p style="color: #bdbdbd; margin-top:8px;">Total submissions: <?php echo $submissions_count; ?></p>
    </div>
    
    <div class="confirmation-content">
        <div class="profile-section">
            <?php if (!empty($photo_url)): ?>
                <img src="<?php echo htmlspecialchars($photo_url, ENT_QUOTES, 'UTF-8'); ?>" alt="Profile Photo" class="profile-photo">
            <?php else: ?>
                <div class="profile-placeholder">
                    <span>No Photo</span>
                </div>
            <?php endif; ?>
            <div class="profile-info">
                <h3><?php echo htmlspecialchars($first_name . ' ' . $last_name, ENT_QUOTES, 'UTF-8'); ?></h3>
                <p><?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
        </div>
        
        <div class="details-grid">
            <div class="detail-item">
                <span class="label">Phone:</span>
                <span class="value"><?php echo htmlspecialchars($phone, ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
            <div class="detail-item">
                <span class="label">Gender:</span>
                <span class="value"><?php echo htmlspecialchars($gender, ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
            <div class="detail-item">
                <span class="label">Date of Birth:</span>
                <span class="value"><?php echo htmlspecialchars($dob, ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
            <div class="detail-item">
                <span class="label">Qualification:</span>
                <span class="value"><?php echo htmlspecialchars($qualification ?: 'Not specified', ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
            <div class="detail-item">
                <span class="label">Country:</span>
                <span class="value"><?php echo htmlspecialchars($country, ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
            <div class="detail-item">
                <span class="label">State:</span>
                <span class="value"><?php echo htmlspecialchars($state, ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
            <div class="detail-item full-width">
                <span class="label">Address:</span>
                <span class="value">
                    <?php echo htmlspecialchars($address1, ENT_QUOTES, 'UTF-8'); ?>
                    <?php if (!empty($address2)): ?>
                        , <?php echo htmlspecialchars($address2, ENT_QUOTES, 'UTF-8'); ?>
                    <?php endif; ?>
                    , <?php echo htmlspecialchars($city, ENT_QUOTES, 'UTF-8'); ?>
                </span>
            </div>
            <?php if (!empty($skills)): ?>
                <div class="detail-item full-width">
                    <span class="label">Skills:</span>
                    <span class="value skills-value">
                        <?php foreach ($skills as $skill): ?>
                            <span class="skill-tag"><?php echo htmlspecialchars($skill, ENT_QUOTES, 'UTF-8'); ?></span>
                        <?php endforeach; ?>
                    </span>
                </div>
            <?php endif; ?>
            <?php if (!empty($bio)): ?>
                <div class="detail-item full-width">
                    <span class="label">Bio:</span>
                    <span class="value"><?php echo htmlspecialchars($bio, ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="confirmation-actions">
        <button class="btn btn-secondary" onclick="window.print()">Print Application</button>
        <button class="btn btn-primary" id="newApplicationBtn">Submit Another Application</button>
        <button class="btn btn-export" id="exportJsonBtn">Export as JSON</button>
    </div>
</div>

<script>
document.getElementById('newApplicationBtn').addEventListener('click', function() {
    window.location.href = 'index.php';
});

document.getElementById('exportJsonBtn').addEventListener('click', function() {
    const dataStr = "data:text/json;charset=utf-8," + encodeURIComponent('<?php echo json_encode($submission_data, JSON_PRETTY_PRINT); ?>');
    const downloadAnchorNode = document.createElement('a');
    downloadAnchorNode.setAttribute("href", dataStr);
    downloadAnchorNode.setAttribute("download", "application_<?php echo uniqid(); ?>.json");
    document.body.appendChild(downloadAnchorNode);
    downloadAnchorNode.click();
    downloadAnchorNode.remove();
});
</script>
<?php
$confirmHtml = ob_get_clean();

// Return success response with confirmation HTML
respond(['status' => 'success', 'html' => $confirmHtml]);
?>