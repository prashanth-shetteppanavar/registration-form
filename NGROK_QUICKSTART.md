# Quick Public Exposure with ngrok

## What is ngrok?
ngrok creates a secure tunnel to your local PHP server and provides a public HTTPS URL that anyone can access.

## Setup & Run

### 1. Download ngrok
- Visit https://ngrok.com/download
- Choose Windows, download and extract the ZIP
- Add ngrok to your PATH or note the folder location

### 2. Start PHP Server (in PowerShell)
```powershell
cd "C:\Users\Prashanth S\Desktop\HariGG2"
php -S 0.0.0.0:8000
```
You should see: `[timestamp] Development Server (http://0.0.0.0:8000) started`

### 3. Start ngrok (in a NEW PowerShell window)
```powershell
# If ngrok is in PATH:
ngrok http 8000

# OR if you extracted ngrok to a folder:
C:\path\to\ngrok.exe http 8000
```

### 4. Share Your Site
- ngrok will display a forwarding URL like: `https://abc123def456.ngrok.io`
- Share this URL with anyone—they can access your site
- It auto-updates if your local server restarts

## Stop ngrok
- Press `Ctrl+C` in the ngrok terminal

## Notes
- Free ngrok expires after 2 hours of inactivity
- For permanent public exposure, use GitHub Pages, shared hosting, or VPS (see DEPLOY.md)
- Make sure your site works locally first before sharing via ngrok

---

Questions? See `README.md` and `DEPLOY.md` for full deployment options.
