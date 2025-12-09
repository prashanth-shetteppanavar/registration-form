<?php
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['csrf_token'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Registration Form</title>
    <link rel="stylesheet" href="css/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="background-overlay"></div>
    <div class="container">
        <div class="logo">
                <h1>REGISTRATION <span>FORM</span></h1>
        </div>
        
        <div class="form-card">
            <div class="form-header">
                    <h2>Registration Form</h2>
                <p>Please fill in all required fields</p>
            </div>
            
            <form id="appForm" action="submit.php" method="POST" enctype="multipart/form-data" novalidate>
                <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
                
                <div class="form-group">
                    <div class="input-wrapper">
                        <input type="text" id="first_name" name="first_name" required>
                        <label for="first_name">First Name *</label>
                        <div class="error-message" id="first_name_error"></div>
                    </div>
                    
                    <div class="input-wrapper">
                        <input type="text" id="last_name" name="last_name" required>
                        <label for="last_name">Last Name *</label>
                        <div class="error-message" id="last_name_error"></div>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="input-wrapper">
                        <input type="email" id="email" name="email" required>
                        <label for="email">Email Address *</label>
                        <div class="error-message" id="email_error"></div>
                    </div>
                    
                    <div class="input-wrapper">
                        <input type="tel" id="phone" name="phone" required>
                        <label for="phone">Mobile / Phone *</label>
                        <div class="error-message" id="phone_error"></div>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="radio-group">
                        <label class="radio-label">Gender *</label>
                        <div class="radio-options">
                            <label class="radio-container">
                                <input type="radio" name="gender" value="Male" required>
                                <span class="radio-checkmark"></span>
                                Male
                            </label>
                            <label class="radio-container">
                                <input type="radio" name="gender" value="Female">
                                <span class="radio-checkmark"></span>
                                Female
                            </label>
                            <label class="radio-container">
                                <input type="radio" name="gender" value="Other">
                                <span class="radio-checkmark"></span>
                                Other
                            </label>
                        </div>
                        <div class="error-message" id="gender_error"></div>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="input-wrapper">
                        <input type="date" id="dob" name="dob" required>
                        <label for="dob">Date of Birth *</label>
                        <div class="error-message" id="dob_error"></div>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="input-wrapper">
                        <input type="text" id="address1" name="address1" required>
                        <label for="address1">Address Line 1 *</label>
                        <div class="error-message" id="address1_error"></div>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="input-wrapper">
                        <input type="text" id="address2" name="address2">
                        <label for="address2">Address Line 2</label>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="input-wrapper">
                        <input type="text" id="city" name="city" required>
                        <label for="city">City *</label>
                        <div class="error-message" id="city_error"></div>
                    </div>
                    
                    <div class="input-wrapper">
                        <input type="text" id="state" name="state" required>
                        <label for="state">State / Province *</label>
                        <div class="error-message" id="state_error"></div>
                        <div id="state_suggestions" class="suggestions-list" aria-hidden="true"></div>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="input-wrapper">
                        <select id="country" name="country" required>
                            <option value="">Select Country *</option>
                            <option value="USA">United States</option>
                            <option value="UK">United Kingdom</option>
                            <option value="Canada">Canada</option>
                            <option value="Australia">Australia</option>
                            <option value="Germany">Germany</option>
                            <option value="France">France</option>
                            <option value="Other">Other</option>
                        </select>
                        <label for="country" class="select-label">Country *</label>
                        <div class="error-message" id="country_error"></div>
                    </div>
                    
                    <div class="input-wrapper">
                        <select id="qualification" name="qualification">
                            <option value="">Select Qualification</option>
                            <option value="High School">High School</option>
                            <option value="Bachelor">Bachelor's Degree</option>
                            <option value="Master">Master's Degree</option>
                            <option value="PhD">PhD</option>
                            <option value="Other">Other</option>
                        </select>
                        <label for="qualification" class="select-label">Highest Qualification</label>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="checkbox-group">
                        <label class="checkbox-label">Skills</label>
                        <div class="checkbox-options">
                            <label class="checkbox-container">
                                <input type="checkbox" name="skills[]" value="HTML">
                                <span class="checkmark"></span>
                                HTML
                            </label>
                            <label class="checkbox-container">
                                <input type="checkbox" name="skills[]" value="CSS">
                                <span class="checkmark"></span>
                                CSS
                            </label>
                            <label class="checkbox-container">
                                <input type="checkbox" name="skills[]" value="JavaScript">
                                <span class="checkmark"></span>
                                JavaScript
                            </label>
                            <label class="checkbox-container">
                                <input type="checkbox" name="skills[]" value="PHP">
                                <span class="checkmark"></span>
                                PHP
                            </label>
                            <label class="checkbox-container">
                                <input type="checkbox" name="skills[]" value="MySQL">
                                <span class="checkmark"></span>
                                MySQL
                            </label>
                            <label class="checkbox-container">
                                <input type="checkbox" name="skills[]" value="Python">
                                <span class="checkmark"></span>
                                Python
                            </label>
                            <label class="checkbox-container">
                                <input type="checkbox" name="skills[]" value="Java">
                                <span class="checkmark"></span>
                                Java
                            </label>
                            <label class="checkbox-container">
                                <input type="checkbox" name="skills[]" value="C#">
                                <span class="checkmark"></span>
                                C#
                            </label>
                            <label class="checkbox-container">
                                <input type="checkbox" name="skills[]" value="Node.js">
                                <span class="checkmark"></span>
                                Node.js
                            </label>
                            <label class="checkbox-container">
                                <input type="checkbox" name="skills[]" value="React">
                                <span class="checkmark"></span>
                                React
                            </label>
                            <label class="checkbox-container">
                                <input type="checkbox" name="skills[]" value="Docker">
                                <span class="checkmark"></span>
                                Docker
                            </label>
                            <label class="checkbox-container">
                                <input type="checkbox" name="skills[]" value="Git">
                                <span class="checkmark"></span>
                                Git
                            </label>
                            </div>
                            <div id="selected_skills" class="selected-skills" aria-live="polite"></div>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="input-wrapper">
                        <input type="file" id="photo" name="photo" accept="image/*">
                        <label for="photo">Upload Photo (Max 2MB)</label>
                        <div class="error-message" id="photo_error"></div>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="input-wrapper">
                        <textarea id="bio" name="bio" maxlength="1000"></textarea>
                        <label for="bio">Short Bio / Additional Notes</label>
                        <div class="char-count"><span id="char_count">0</span>/1000</div>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="checkbox-group">
                        <label class="checkbox-container terms-container">
                            <input type="checkbox" id="agree" name="agree" required>
                            <span class="checkmark"></span>
                            I agree to the Terms & Conditions *
                        </label>
                        <div class="error-message" id="agree_error"></div>
                    </div>
                </div>
                
                <div class="button-group">
                    <button type="button" class="btn btn-secondary" id="clearDraftBtn">Clear Draft</button>
                    <button type="reset" class="btn btn-secondary">Reset</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Submit Application</button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="loading-spinner" id="loadingSpinner">
        <div class="spinner"></div>
        <p>Processing your application...</p>
    </div>
    
    <script src="js/jquery.min.js"></script>
    <script src="js/app.js"></script>
</body>
</html>