<?php
// This file serves as a fallback for non-AJAX submissions
session_start();

// Check if we have submission data
if (!isset($_SESSION['submission_data'])) {
    // Redirect to form if no data
    header('Location: index.php');
    exit;
}

$data = $_SESSION['submission_data'];
unset($_SESSION['submission_data']); // Clear the data
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form - Confirmation</title>
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="background-overlay"></div>
    <div class="container">
        <div class="logo">
            <h1>REGISTRATION <span>FORM</span></h1>
        </div>
        
        <div class="confirmation-container fade-in">
            <div class="confirmation-header">
                <h2>Application Submitted Successfully!</h2>
                <p>Thank you for your submission. Here's a summary of your information:</p>
            </div>
            
            <div class="confirmation-content">
                <div class="profile-section">
                    <?php if (!empty($data['photo_url'])): ?>
                        <img src="<?php echo htmlspecialchars($data['photo_url'], ENT_QUOTES, 'UTF-8'); ?>" alt="Profile Photo" class="profile-photo">
                    <?php else: ?>
                        <div class="profile-placeholder">
                            <span>No Photo</span>
                        </div>
                    <?php endif; ?>
                    <div class="profile-info">
                        <h3><?php echo htmlspecialchars($data['first_name'] . ' ' . $data['last_name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                        <p><?php echo htmlspecialchars($data['email'], ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                </div>
                
                <div class="details-grid">
                    <div class="detail-item">
                        <span class="label">Phone:</span>
                        <span class="value"><?php echo htmlspecialchars($data['phone'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Gender:</span>
                        <span class="value"><?php echo htmlspecialchars($data['gender'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Date of Birth:</span>
                        <span class="value"><?php echo htmlspecialchars($data['dob'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Qualification:</span>
                        <span class="value"><?php echo htmlspecialchars($data['qualification'] ?: 'Not specified', ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="label">Country:</span>
                        <span class="value"><?php echo htmlspecialchars($data['country'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="label">State:</span>
                        <span class="value"><?php echo htmlspecialchars($data['state'], ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <div class="detail-item full-width">
                        <span class="label">Address:</span>
                        <span class="value">
                            <?php echo htmlspecialchars($data['address1'], ENT_QUOTES, 'UTF-8'); ?>
                            <?php if (!empty($data['address2'])): ?>
                                , <?php echo htmlspecialchars($data['address2'], ENT_QUOTES, 'UTF-8'); ?>
                            <?php endif; ?>
                            , <?php echo htmlspecialchars($data['city'], ENT_QUOTES, 'UTF-8'); ?>
                        </span>
                    </div>
                    <?php if (!empty($data['skills'])): ?>
                        <div class="detail-item full-width">
                            <span class="label">Skills:</span>
                            <span class="value skills-value">
                                <?php foreach ($data['skills'] as $skill): ?>
                                    <span class="skill-tag"><?php echo htmlspecialchars($skill, ENT_QUOTES, 'UTF-8'); ?></span>
                                <?php endforeach; ?>
                            </span>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($data['bio'])): ?>
                        <div class="detail-item full-width">
                            <span class="label">Bio:</span>
                            <span class="value"><?php echo htmlspecialchars($data['bio'], ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="confirmation-actions">
                <button class="btn btn-secondary" onclick="window.print()">Print Application</button>
                <button class="btn btn-primary" onclick="window.location.href='index.php'">Submit Another Application</button>
                <button class="btn btn-export" id="exportJsonBtn">Export as JSON</button>
            </div>
        </div>
    </div>
    
    <script>
    document.getElementById('exportJsonBtn').addEventListener('click', function() {
        const dataStr = "data:text/json;charset=utf-8," + encodeURIComponent('<?php echo json_encode($data, JSON_PRETTY_PRINT); ?>');
        const downloadAnchorNode = document.createElement('a');
        downloadAnchorNode.setAttribute("href", dataStr);
        downloadAnchorNode.setAttribute("download", "application_<?php echo uniqid(); ?>.json");
        document.body.appendChild(downloadAnchorNode);
        downloadAnchorNode.click();
        downloadAnchorNode.remove();
    });
    </script>
</body>
</html>