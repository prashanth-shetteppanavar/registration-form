<?php
/**
 * Save submissions to JSON file
 * This script can be used to view saved submissions
 */
require_once 'php/functions.php';

// Check if user is authorized to view submissions
// In a real application, you would implement proper authentication
$authorized = true; // Set to false to restrict access

if (!$authorized) {
    header('HTTP/1.0 403 Forbidden');
    die('Access denied');
}

// Get all submissions
$submissions = get_submissions();

// Sort submissions by timestamp (newest first)
usort($submissions, function($a, $b) {
    return strtotime($b['timestamp']) - strtotime($a['timestamp']);
});
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saved Submissions</title>
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="background-overlay"></div>
    <div class="container">
        <div class="logo">
            <h1>NETFLIX<span>FORM</span></h1>
        </div>
        
        <div class="form-card">
            <div class="form-header">
                <h2>Saved Applications</h2>
                <p>Total submissions: <?php echo count($submissions); ?></p>
            </div>
            
            <?php if (empty($submissions)): ?>
                <div class="confirmation-header">
                    <h3>No submissions found</h3>
                    <p>There are no saved applications at this time.</p>
                </div>
            <?php else: ?>
                <div class="submissions-list">
                    <?php foreach ($submissions as $index => $submission): ?>
                        <div class="submission-item">
                            <div class="submission-header">
                                <h3>Application #<?php echo count($submissions) - $index; ?></h3>
                                <span class="timestamp"><?php echo htmlspecialchars($submission['timestamp'], ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>
                            
                            <div class="submission-details">
                                <div class="detail-item">
                                    <span class="label">Name:</span>
                                    <span class="value"><?php echo htmlspecialchars($submission['first_name'] . ' ' . $submission['last_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                                </div>
                                
                                <div class="detail-item">
                                    <span class="label">Email:</span>
                                    <span class="value"><?php echo htmlspecialchars($submission['email'], ENT_QUOTES, 'UTF-8'); ?></span>
                                </div>
                                
                                <div class="detail-item">
                                    <span class="label">Phone:</span>
                                    <span class="value"><?php echo htmlspecialchars($submission['phone'], ENT_QUOTES, 'UTF-8'); ?></span>
                                </div>
                                
                                <div class="detail-item">
                                    <span class="label">Country:</span>
                                    <span class="value"><?php echo htmlspecialchars($submission['country'], ENT_QUOTES, 'UTF-8'); ?></span>
                                </div>
                                
                                <?php if (!empty($submission['skills'])): ?>
                                    <div class="detail-item full-width">
                                        <span class="label">Skills:</span>
                                        <span class="value skills-value">
                                            <?php foreach ($submission['skills'] as $skill): ?>
                                                <span class="skill-tag"><?php echo htmlspecialchars($skill, ENT_QUOTES, 'UTF-8'); ?></span>
                                            <?php endforeach; ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="submission-actions">
                                <button class="btn btn-secondary" onclick="viewDetails(<?php echo $index; ?>)">View Details</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <div class="button-group">
                <button class="btn btn-primary" onclick="window.location.href='index.php'">Back to Form</button>
            </div>
        </div>
    </div>
    
    <!-- Modal for detailed view -->
    <div class="modal" id="detailsModal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div id="modalBody"></div>
        </div>
    </div>
    
    <script>
        // View details function
        function viewDetails(index) {
            const submissions = <?php echo json_encode($submissions); ?>;
            const submission = submissions[index];
            
            let html = `
                <div class="confirmation-header">
                    <h2>Application Details</h2>
                </div>
                
                <div class="confirmation-content">
                    <div class="profile-section">
                        ${submission.photo_url ? 
                            `<img src="${submission.photo_url}" alt="Profile Photo" class="profile-photo">` :
                            `<div class="profile-placeholder"><span>No Photo</span></div>`
                        }
                        <div class="profile-info">
                            <h3>${escapeHtml(submission.first_name + ' ' + submission.last_name)}</h3>
                            <p>${escapeHtml(submission.email)}</p>
                        </div>
                    </div>
                    
                    <div class="details-grid">
                        <div class="detail-item">
                            <span class="label">Phone:</span>
                            <span class="value">${escapeHtml(submission.phone)}</span>
                        </div>
                        <div class="detail-item">
                            <span class="label">Gender:</span>
                            <span class="value">${escapeHtml(submission.gender)}</span>
                        </div>
                        <div class="detail-item">
                            <span class="label">Date of Birth:</span>
                            <span class="value">${escapeHtml(submission.dob)}</span>
                        </div>
                        <div class="detail-item">
                            <span class="label">Qualification:</span>
                            <span class="value">${escapeHtml(submission.qualification || 'Not specified')}</span>
                        </div>
                        <div class="detail-item">
                            <span class="label">Country:</span>
                            <span class="value">${escapeHtml(submission.country)}</span>
                        </div>
                        <div class="detail-item">
                            <span class="label">State:</span>
                            <span class="value">${escapeHtml(submission.state)}</span>
                        </div>
                        <div class="detail-item full-width">
                            <span class="label">Address:</span>
                            <span class="value">
                                ${escapeHtml(submission.address1)}
                                ${submission.address2 ? ', ' + escapeHtml(submission.address2) : ''}
                                , ${escapeHtml(submission.city)}
                            </span>
                        </div>
                        ${submission.skills && submission.skills.length > 0 ? `
                            <div class="detail-item full-width">
                                <span class="label">Skills:</span>
                                <span class="value skills-value">
                                    ${submission.skills.map(skill => `<span class="skill-tag">${escapeHtml(skill)}</span>`).join('')}
                                </span>
                            </div>
                        ` : ''}
                        ${submission.bio ? `
                            <div class="detail-item full-width">
                                <span class="label">Bio:</span>
                                <span class="value">${escapeHtml(submission.bio)}</span>
                            </div>
                        ` : ''}
                    </div>
                </div>
            `;
            
            document.getElementById('modalBody').innerHTML = html;
            document.getElementById('detailsModal').style.display = 'block';
        }
        
        // Close modal
        document.querySelector('.close').onclick = function() {
            document.getElementById('detailsModal').style.display = 'none';
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('detailsModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
        
        // Simple HTML escaping function
        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }
    </script>
    
    <style>
        .submissions-list {
            margin-bottom: 30px;
        }
        
        .submission-item {
            background: rgba(51, 51, 51, 0.3);
            border-radius: var(--border-radius);
            padding: 20px;
            margin-bottom: 20px;
            transition: transform var(--transition-speed);
        }
        
        .submission-item:hover {
            transform: translateY(-3px);
            background: rgba(51, 51, 51, 0.5);
        }
        
        .submission-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .submission-header h3 {
            margin: 0;
            color: var(--netflix-light);
        }
        
        .timestamp {
            color: var(--netflix-gray);
            font-size: 0.9rem;
        }
        
        .submission-details {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1001;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
        }
        
        .modal-content {
            background: rgba(20, 20, 20, 0.95);
            margin: 5% auto;
            padding: 30px;
            border-radius: var(--border-radius);
            width: 80%;
            max-width: 800px;
            max-height: 80vh;
            overflow-y: auto;
            position: relative;
            box-shadow: var(--shadow);
        }
        
        .close {
            color: var(--netflix-gray);
            float: right;
            font-size: 28px;
            font-weight: bold;
            position: absolute;
            right: 20px;
            top: 15px;
            cursor: pointer;
        }
        
        .close:hover {
            color: var(--netflix-light);
        }
    </style>
</body>
</html>