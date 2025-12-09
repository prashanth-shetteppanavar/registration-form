# Deployment Guide

Options to publish this site publicly:

1) Temporary public URL — ngrok (fast)
   - Start PHP dev server locally:
     ```powershell
     php -S localhost:8000 -t .
     ```
   - Run ngrok to expose port 8000:
     ```powershell
     ngrok http 8000
     ```
   - ngrok provides a public HTTPS URL for testing.

2) Shared PHP hosting (recommended for small sites)
   - Create an account with a host that supports PHP (cPanel/FTP).
   - Upload project files to `public_html` (or equivalent).
   - Move `data/` outside the webroot if possible or protect with `.htaccess`.

3) VPS or Cloud VM (DigitalOcean, Linode, AWS)
   - Provision server, install PHP + web server, configure vhost, enable SSL with Certbot.
   - Deploy code to `/var/www/your-site`, set correct permissions for `data/` and `uploads/`.

4) Platform-as-a-Service (Render, Railway)
   - Create a Git repository and connect to Render/Railway for automatic deployments.

Notes
- Always use HTTPS for production.
- Keep `admin_config.php` and `data/` protected. Prefer moving them outside webroot.
- Use the provided CLI tools for password-hash generation and backups.
