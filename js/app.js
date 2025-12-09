$(function(){
    // --- Auto-save draft & selected skills UI ---
    const DRAFT_KEY = 'appFormDraft';

    function getFormData() {
        const form = $('#appForm')[0];
        const fd = {};
        $(form).find('input,select,textarea').each(function() {
            const $el = $(this);
            const name = $el.attr('name');
            if (!name) return;
            if ($el.attr('type') === 'checkbox') {
                // Handle checkbox arrays
                if (name.endsWith('[]')) {
                    const key = name.replace('[]','');
                    fd[key] = fd[key] || [];
                    if ($el.is(':checked')) fd[key].push($el.val());
                } else {
                    fd[name] = $el.is(':checked');
                }
            } else if ($el.attr('type') === 'radio') {
                if ($el.is(':checked')) fd[name] = $el.val();
            } else {
                fd[name] = $el.val();
            }
        });
        return fd;
    }

    function saveDraft() {
        try {
            const data = getFormData();
            localStorage.setItem(DRAFT_KEY, JSON.stringify({ data: data, savedAt: Date.now() }));
            updateDraftStatus();
        } catch (e) {
            // ignore storage errors
        }
    }

    function loadDraft() {
        try {
            const raw = localStorage.getItem(DRAFT_KEY);
            if (!raw) return false;
            const parsed = JSON.parse(raw);
            const data = parsed.data || {};
            const form = $('#appForm')[0];
            $(form).find('input,select,textarea').each(function() {
                const $el = $(this);
                const name = $el.attr('name');
                if (!name) return;
                if ($el.attr('type') === 'checkbox') {
                    if (name.endsWith('[]')) {
                        const key = name.replace('[]','');
                        const arr = data[key] || [];
                        $el.prop('checked', arr.indexOf($el.val()) !== -1);
                    } else {
                        $el.prop('checked', !!data[name]);
                    }
                } else if ($el.attr('type') === 'radio') {
                    $el.prop('checked', $el.val() === data[name]);
                } else {
                    if (typeof data[name] !== 'undefined') $el.val(data[name]);
                }
            });
            return true;
        } catch (e) {
            return false;
        }
    }

    function clearDraft() {
        localStorage.removeItem(DRAFT_KEY);
        $('#draftStatus').remove();
    }

    let saveTimer = null;
    function scheduleSave() {
        if (saveTimer) clearTimeout(saveTimer);
        saveTimer = setTimeout(saveDraft, 600);
    }

    function updateDraftStatus() {
        const raw = localStorage.getItem(DRAFT_KEY);
        $('#draftStatus').remove();
        if (!raw) return;
        try {
            const parsed = JSON.parse(raw);
            const time = parsed.savedAt ? new Date(parsed.savedAt) : new Date();
            const stamp = time.toLocaleString();
            const statusEl = $(`<div id="draftStatus" class="draft-status">Draft saved: ${stamp}</div>`);
            $('.form-header').append(statusEl);
        } catch (e) {}
    }

    function displaySelectedSkills() {
        const $container = $('#selected_skills');
        if (!$container.length) return;
        const selected = $('input[name="skills[]"]:checked').map(function(){ return $(this).val(); }).get();
        if (selected.length === 0) {
            $container.html('<span class="no-skills">No skills selected</span>');
            return;
        }
        const html = selected.map(s => `<span class="skill-badge">${s}</span>`).join(' ');
        $container.html(html);
    }

    // restore draft on load
    loadDraft();
    updateDraftStatus();
    displaySelectedSkills();

    // wire change events to save draft and update skills display
    $('#appForm').on('input change', 'input, select, textarea', function() {
        scheduleSave();
        if ($(this).attr('name') && $(this).attr('name').indexOf('skills') !== -1) displaySelectedSkills();
    });

    $('#clearDraftBtn').on('click', function(){
        clearDraft();
        // keep form values as-is but update UI
        $('#draftStatus').remove();
    });

    // When submission succeeds, remove draft
    const originalAjaxDone = $.fn.done; // not used, we'll handle in our AJAX success below
    // Show loading spinner
    function showLoading() {
        $('#loadingSpinner').addClass('active');
    }
    
    // Hide loading spinner
    function hideLoading() {
        $('#loadingSpinner').removeClass('active');
    }
    
    // Update character counter for bio
    $('#bio').on('input', function() {
        const count = $(this).val().length;
        $('#char_count').text(count);
    });
    
    // Form validation
    function validateForm() {
        let isValid = true;
        const errors = {};
        
        // Clear previous errors
        $('.error-message').removeClass('show').text('');
        
        // First name validation
        const firstName = $('#first_name').val().trim();
        if (!firstName) {
            errors.first_name = 'First name is required';
            isValid = false;
        }
        
        // Last name validation
        const lastName = $('#last_name').val().trim();
        if (!lastName) {
            errors.last_name = 'Last name is required';
            isValid = false;
        }
        
        // Email validation
        const email = $('#email').val().trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!email) {
            errors.email = 'Email is required';
            isValid = false;
        } else if (!emailRegex.test(email)) {
            errors.email = 'Please enter a valid email address';
            isValid = false;
        }
        
        // Phone validation
        const phone = $('#phone').val().trim();
        const phoneRegex = /^[\+]?[0-9]{7,15}$/;
        if (!phone) {
            errors.phone = 'Phone number is required';
            isValid = false;
        } else if (!phoneRegex.test(phone)) {
            errors.phone = 'Please enter a valid phone number (7-15 digits)';
            isValid = false;
        }
        
        // Gender validation
        const gender = $('input[name="gender"]:checked').val();
        if (!gender) {
            errors.gender = 'Please select your gender';
            isValid = false;
        }
        
        // Date of birth validation
        const dob = $('#dob').val();
        if (!dob) {
            errors.dob = 'Date of birth is required';
            isValid = false;
        } else {
            // Check if user is at least 16 years old
            const dobDate = new Date(dob);
            const today = new Date();
            const age = Math.floor((today - dobDate) / (365.25 * 24 * 60 * 60 * 1000));
            if (age < 16) {
                errors.dob = 'You must be at least 16 years old';
                isValid = false;
            }
        }
        
        // Address line 1 validation
        const address1 = $('#address1').val().trim();
        if (!address1) {
            errors.address1 = 'Address line 1 is required';
            isValid = false;
        }
        
        // City validation
        const city = $('#city').val().trim();
        if (!city) {
            errors.city = 'City is required';
            isValid = false;
        }
        
        // State validation
        const state = $('#state').val().trim();
        if (!state) {
            errors.state = 'State/Province is required';
            isValid = false;
        }
        
        // Country validation
        const country = $('#country').val();
        if (!country) {
            errors.country = 'Country is required';
            isValid = false;
        }
        
        // Terms agreement validation
        const agree = $('#agree').is(':checked');
        if (!agree) {
            errors.agree = 'You must agree to the terms and conditions';
            isValid = false;
        }
        
        // File validation
        const fileInput = $('#photo')[0];
        if (fileInput.files.length > 0) {
            const file = fileInput.files[0];
            const fileSize = file.size;
            const fileType = file.type;
            
            // Validate file size (max 2MB)
            if (fileSize > 2 * 1024 * 1024) {
                errors.photo = 'File size exceeds 2MB limit';
                isValid = false;
            }
            
            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!allowedTypes.includes(fileType)) {
                errors.photo = 'Only JPG, JPEG, and PNG files are allowed';
                isValid = false;
            }
        }
        
        // Display errors
        if (!isValid) {
            for (const field in errors) {
                $(`#${field}_error`).addClass('show').text(errors[field]);
            }
            
            // Focus on first error field
            const firstErrorField = Object.keys(errors)[0];
            if (firstErrorField) {
                $(`#${firstErrorField}`).focus();
            }
        }
        
        return isValid;
    }
    
    // Form submission handler
    $('#appForm').on('submit', function(e){
        e.preventDefault();
        
        // Validate form
        if (!validateForm()) {
            return;
        }
        
        // Show loading spinner
        showLoading();
        
        // Get form data
        const form = $(this)[0];
        const formData = new FormData(form);
        
        // Disable submit button
        $('#submitBtn').prop('disabled', true).text('Submitting...');
        
        // AJAX request
        $.ajax({
            url: 'submit.php',
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json'
        }).done(function(res){
            if(res.status === 'success'){
                // Replace page content with confirmation HTML
                $('body').html(res.html);
                
                // Add fade-in animation to confirmation content
                $('.confirmation-container').addClass('fade-in');
                    // Clear saved draft on successful submission
                    try { localStorage.removeItem('appFormDraft'); } catch(e) {}
            } else if(res.errors) {
                // Display validation errors
                $('.error-message').removeClass('show').text('');
                for (const field in res.errors) {
                    $(`#${field}_error`).addClass('show').text(res.errors[field]);
                }
                
                // Focus on first error field
                const firstErrorField = Object.keys(res.errors)[0];
                if (firstErrorField) {
                    $(`#${firstErrorField}`).focus();
                }
            } else {
                alert(res.message || 'Submission failed. Please try again.');
            }
        }).fail(function(){
            alert('Server error. Please try again later.');
        }).always(function(){
            // Hide loading spinner
            hideLoading();
            
            // Re-enable submit button
            $('#submitBtn').prop('disabled', false).text('Submit Application');
        });
    });
    
    // Reset form handler
    $('button[type="reset"]').on('click', function() {
        // Clear all errors
        $('.error-message').removeClass('show').text('');
        
        // Reset character counter
        $('#char_count').text('0');
    });
    
    // Add animation to form elements on focus
    $('.input-wrapper input, .input-wrapper select, .input-wrapper textarea').on('focus', function() {
        $(this).parent().addClass('focused');
    }).on('blur', function() {
        $(this).parent().removeClass('focused');
    });
    
    // Add animation to radio buttons and checkboxes on hover
    $('.radio-container, .checkbox-container').on('mouseenter', function() {
        $(this).addClass('hovered');
    }).on('mouseleave', function() {
        $(this).removeClass('hovered');
    });

    // --- State autocomplete ---
    // States/provinces with abbreviations (name + abbr). Used for matching names and abbreviations.
    const states = [
        {name: 'Alabama', abbr: 'AL'},{name: 'Alaska', abbr: 'AK'},{name: 'Arizona', abbr: 'AZ'},{name: 'Arkansas', abbr: 'AR'},{name: 'California', abbr: 'CA'},
        {name: 'Colorado', abbr: 'CO'},{name: 'Connecticut', abbr: 'CT'},{name: 'Delaware', abbr: 'DE'},{name: 'Florida', abbr: 'FL'},{name: 'Georgia', abbr: 'GA'},
        {name: 'Hawaii', abbr: 'HI'},{name: 'Idaho', abbr: 'ID'},{name: 'Illinois', abbr: 'IL'},{name: 'Indiana', abbr: 'IN'},{name: 'Iowa', abbr: 'IA'},
        {name: 'Kansas', abbr: 'KS'},{name: 'Kentucky', abbr: 'KY'},{name: 'Louisiana', abbr: 'LA'},{name: 'Maine', abbr: 'ME'},{name: 'Maryland', abbr: 'MD'},
        {name: 'Massachusetts', abbr: 'MA'},{name: 'Michigan', abbr: 'MI'},{name: 'Minnesota', abbr: 'MN'},{name: 'Mississippi', abbr: 'MS'},{name: 'Missouri', abbr: 'MO'},
        {name: 'Montana', abbr: 'MT'},{name: 'Nebraska', abbr: 'NE'},{name: 'Nevada', abbr: 'NV'},{name: 'New Hampshire', abbr: 'NH'},{name: 'New Jersey', abbr: 'NJ'},
        {name: 'New Mexico', abbr: 'NM'},{name: 'New York', abbr: 'NY'},{name: 'North Carolina', abbr: 'NC'},{name: 'North Dakota', abbr: 'ND'},{name: 'Ohio', abbr: 'OH'},
        {name: 'Oklahoma', abbr: 'OK'},{name: 'Oregon', abbr: 'OR'},{name: 'Pennsylvania', abbr: 'PA'},{name: 'Rhode Island', abbr: 'RI'},{name: 'South Carolina', abbr: 'SC'},
        {name: 'South Dakota', abbr: 'SD'},{name: 'Tennessee', abbr: 'TN'},{name: 'Texas', abbr: 'TX'},{name: 'Utah', abbr: 'UT'},{name: 'Vermont', abbr: 'VT'},
        {name: 'Virginia', abbr: 'VA'},{name: 'Washington', abbr: 'WA'},{name: 'West Virginia', abbr: 'WV'},{name: 'Wisconsin', abbr: 'WI'},{name: 'Wyoming', abbr: 'WY'},
        // Canada
        {name: 'British Columbia', abbr: 'BC'},{name: 'Ontario', abbr: 'ON'},{name: 'Quebec', abbr: 'QC'},{name: 'Alberta', abbr: 'AB'},{name: 'Newfoundland and Labrador', abbr: 'NL'},
        {name: 'Nova Scotia', abbr: 'NS'},{name: 'Manitoba', abbr: 'MB'},{name: 'Saskatchewan', abbr: 'SK'},{name: 'Prince Edward Island', abbr: 'PE'},{name: 'Northwest Territories', abbr: 'NT'},
        {name: 'Yukon', abbr: 'YT'},{name: 'Nunavut', abbr: 'NU'},
        // India (common states & UTs)
        {name: 'Andhra Pradesh', abbr: 'AP'},{name: 'Arunachal Pradesh', abbr: 'AR'},{name: 'Assam', abbr: 'AS'},{name: 'Bihar', abbr: 'BR'},{name: 'Chhattisgarh', abbr: 'CG'},
        {name: 'Goa', abbr: 'GA'},{name: 'Gujarat', abbr: 'GJ'},{name: 'Haryana', abbr: 'HR'},{name: 'Himachal Pradesh', abbr: 'HP'},{name: 'Jharkhand', abbr: 'JH'},
        {name: 'Karnataka', abbr: 'KA'},{name: 'Kerala', abbr: 'KL'},{name: 'Madhya Pradesh', abbr: 'MP'},{name: 'Maharashtra', abbr: 'MH'},{name: 'Manipur', abbr: 'MN'},
        {name: 'Meghalaya', abbr: 'ML'},{name: 'Mizoram', abbr: 'MZ'},{name: 'Nagaland', abbr: 'NL'},{name: 'Odisha', abbr: 'OR'},{name: 'Punjab', abbr: 'PB'},
        {name: 'Rajasthan', abbr: 'RJ'},{name: 'Sikkim', abbr: 'SK'},{name: 'Tamil Nadu', abbr: 'TN'},{name: 'Telangana', abbr: 'TG'},{name: 'Tripura', abbr: 'TR'},
        {name: 'Uttar Pradesh', abbr: 'UP'},{name: 'Uttarakhand', abbr: 'UK'},{name: 'West Bengal', abbr: 'WB'},{name: 'Delhi', abbr: 'DL'},{name: 'Puducherry', abbr: 'PY'},
        // Australia
        {name: 'New South Wales', abbr: 'NSW'},{name: 'Victoria', abbr: 'VIC'},{name: 'Queensland', abbr: 'QLD'},{name: 'Western Australia', abbr: 'WA'},{name: 'South Australia', abbr: 'SA'},
        {name: 'Tasmania', abbr: 'TAS'},{name: 'Northern Territory', abbr: 'NT'},{name: 'Australian Capital Territory', abbr: 'ACT'}
    ];

    const $stateInput = $('#state');
    const $suggestions = $('#state_suggestions');

    function renderSuggestions(list) {
        if (!list || list.length === 0) {
            $suggestions.hide().attr('aria-hidden', 'true').empty();
            return;
        }
        const html = list.map(item => {
            // item may be a string or an object {name, abbr}
            if (typeof item === 'string') return `<div class="suggestion-item" role="option">${item}</div>`;
            return `<div class="suggestion-item" role="option">${item.name} (${item.abbr})</div>`;
        }).join('');
        $suggestions.html(html).show().attr('aria-hidden', 'false');
    }

    $stateInput.on('input', function() {
        const q = $(this).val().trim().toLowerCase();
        if (!q) {
            renderSuggestions([]);
            return;
        }
        const matches = states.filter(s => {
            // match against name or abbreviation
            const name = s.name.toLowerCase();
            const abbr = (s.abbr || '').toLowerCase();
            return name.indexOf(q) !== -1 || abbr.indexOf(q) !== -1;
        }).slice(0, 8);
        renderSuggestions(matches);
    });

    // Click on suggestion
    $suggestions.on('click', '.suggestion-item', function() {
        // suggestion text is "Name (AB)" — extract the name before the last '('
        const txt = $(this).text();
        const paren = txt.lastIndexOf('(');
        const val = paren !== -1 ? txt.substring(0, paren).trim() : txt;
        $stateInput.val(val);
        renderSuggestions([]);
        $stateInput.focus();
    });

    // Hide suggestions when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#state_suggestions, #state').length) {
            renderSuggestions([]);
        }
    });

    // Keyboard navigation (basic)
    $stateInput.on('keydown', function(e) {
        const $items = $suggestions.children('.suggestion-item');
        if ($items.length === 0) return;
        const active = $items.filter('.active');
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            if (active.length === 0) {
                $items.first().addClass('active');
            } else {
                const next = active.removeClass('active').next('.suggestion-item');
                (next.length ? next : $items.first()).addClass('active');
            }
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            if (active.length === 0) {
                $items.last().addClass('active');
            } else {
                const prev = active.removeClass('active').prev('.suggestion-item');
                (prev.length ? prev : $items.last()).addClass('active');
            }
        } else if (e.key === 'Enter') {
            const pick = $items.filter('.active').first();
            if (pick.length) {
                e.preventDefault();
                $stateInput.val(pick.text());
                renderSuggestions([]);
            }
        }
    });
});