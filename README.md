# Registration Form Project

This is a PHP registration form project with client-side validation, state autocomplete, autosave drafts, and an admin interface for viewing submissions.

Features
- Responsive registration form with client-side validation
- State / province autocomplete (US, Canada, India, Australia)
- Expanded skills checkboxes and live selected-skills badges
- Autosave draft in browser localStorage + Clear Draft
- Submissions saved to `data/submissions.json` with a confirmation page
- Admin interface (`admin_login.php`, `admin.php`) with session-based login and hashed password
- Admin tools: `tools/generate_hash.php` (generate password hashes), `tools/backup_submissions.php` (backup submissions)

Security notes
- Change admin password via `tools/generate_hash.php` and update `admin_config.php`.
- `data/` directory holds submissions; keep it protected and out of public webroot in production.
- Use HTTPS for public deployments.

Quick local run
1. Start PHP dev server from project root:
```powershell
php -S localhost:8000 -t .
```
2. Open http://localhost:8000/

Admin
- Login: http://localhost:8000/admin_login.php
- Logout: http://localhost:8000/admin_logout.php

Deployment
See `DEPLOY.md` for options: ngrok (temporary), shared hosting, VPS, or Render.
# Netflix-Style Registration Form

A fully functional, responsive registration form web application with Netflix-inspired design, animations, and premium UI/UX. This application includes client-side and server-side validation, file upload capabilities, and a beautiful confirmation page.

## Features

- **Netflix-Inspired Design**: Dark theme with red accents reminiscent of Netflix
- **Responsive Layout**: Works on all device sizes
- **Form Validation**: Both client-side (JavaScript) and server-side (PHP) validation
- **File Upload**: Secure image upload with validation (JPG, JPEG, PNG up to 2MB)
- **CSRF Protection**: Security tokens to prevent cross-site request forgery
- **Data Persistence**: Saves submissions to a JSON file
- **Export Options**: Print and JSON export functionality
- **Animations**: Smooth transitions and hover effects
- **Accessibility**: WCAG AA compliant with proper labeling and focus management

## File Structure

```
/project-root
  /css
    styles.css           # Main stylesheet with Netflix theme
  /js
    jquery.min.js        # jQuery library
    app.js               # Client-side validation and AJAX handling
  /php
    functions.php        # Utility functions
  /uploads               # Uploaded images (git ignored)
  /logs                  # Application logs (git ignored)
  index.php              # Main form page
  submit.php             # Handles form submission
  confirmation.php       # Server-side rendered confirmation
  save_submissions.php   # View saved submissions
  submissions.json       # Saved form data (git ignored)
  .gitignore             # Git ignore file
  README.md              # This file
```

## Requirements

- PHP 7.0 or higher
- Web server with PHP support (Apache, Nginx, etc.)
- Modern web browser

## Local Development

### Option 1: PHP Built-in Server

1. Navigate to the project root directory in your terminal
2. Run the command:
   ```
   php -S localhost:8000
   ```
3. Open your browser and go to `http://localhost:8000`

### Option 2: XAMPP/WAMP/MAMP

1. Install XAMPP, WAMP, or MAMP
2. Clone or copy the project files to your web server's document root:
   - XAMPP: `C:\xampp\htdocs\` (Windows) or `/Applications/XAMPP/htdocs/` (Mac)
   - WAMP: `C:\wamp64\www\`
   - MAMP: `/Applications/MAMP/htdocs/`
3. Start your web server
4. Access the application through your browser:
   - XAMPP: `http://localhost/your-project-folder/`
   - WAMP: `http://localhost/your-project-folder/`
   - MAMP: `http://localhost:8888/your-project-folder/`

## Deployment to Free PHP Hosting

### InfinityFree

1. Sign up at [infinityfree.net](https://infinityfree.net/)
2. Log in to your control panel
3. Create a new account and subdomain
4. Go to the File Manager
5. Upload all project files to the `htdocs` folder
6. Make sure the `uploads` folder has write permissions (755 or 777)
7. Access your form via the provided subdomain URL

### 000Webhost

1. Sign up at [000webhost.com](https://www.000webhost.com/)
2. Create a new website
3. Go to File Manager
4. Upload all project files to the `public_html` folder
5. Set permissions for the `uploads` folder to 755 or 777
6. Your form will be accessible via your website URL

### Hostinger

1. Sign up at [hostinger.com](https://www.hostinger.com/)
2. Set up your hosting plan
3. Access your hPanel
4. Go to File Manager
5. Upload all project files to the `public_html` directory
6. Change permissions of the `uploads` folder to 755
7. Access your website via your domain

## Security Features

- CSRF token protection
- Input sanitization and validation
- File type and size validation
- Secure file upload with unique naming
- Rate limiting to prevent abuse
- Logging of submissions and errors

## Customization

### Changing the Theme

To modify the color scheme, edit the CSS variables in `css/styles.css`:

```css
:root {
  --netflix-red: #e50914;
  --netflix-dark: #141414;
  --netflix-gray: #808080;
  --netflix-light: #f5f5f5;
  --netflix-blue: #007bff;
}
```

### Adding New Fields

1. Add the field to `index.php` in the appropriate form section
2. Add validation rules in `app.js` for client-side validation
3. Add server-side validation in `submit.php`
4. Include the field in the confirmation display

## Testing

### Manual Testing Checklist

1. Submit with empty required fields → Should show inline errors
2. Submit with invalid email → Should be blocked
3. Submit valid data with photo → Confirmation page shows sanitized values and photo
4. Disable JS → Submit to submit.php and see server-rendered confirmation.php page
5. Click Print → Should open print dialog with print-friendly layout
6. Upload invalid file types → Should show error
7. Upload files larger than 2MB → Should show error

### Browser Compatibility

The application has been tested and works correctly on:
- Chrome (latest)
- Edge (latest)
- Firefox (latest)
- Mobile Chrome/Android browser
- iOS Safari

## Troubleshooting

### Common Issues

1. **Uploads not working**: Check that the `uploads` directory exists and has write permissions (755 or 777)
2. **CSRF token errors**: Clear browser cookies and refresh the page
3. **Form not submitting**: Ensure JavaScript is enabled in your browser
4. **Styling issues**: Check that `css/styles.css` is loaded correctly

### Server Requirements

- PHP 7.0+
- File upload enabled
- Session support enabled
- Write permissions for `uploads` and `logs` directories

## Contributing

1. Fork the repository
2. Create a new branch for your feature
3. Commit your changes
4. Push to the branch
5. Create a new Pull Request

## License

This project is open source and available under the [MIT License](LICENSE).

## Support

For support, please open an issue on the GitHub repository or contact the developer.