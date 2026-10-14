@echo off
set "ROOT=%~dp0"
set "PHP=C:\xampp\php\php.exe"

if not exist "%PHP%" (
    echo XAMPP PHP was not found at %PHP%
    echo Please install XAMPP or update this file with your php.exe path.
    pause
    exit /b 1
)

echo.
echo GlobeTrek Adventures website
echo --------------------------------
echo 1. Make sure MySQL is started in XAMPP Control Panel.
echo 2. Keep this window open while using the website.
echo 3. Open this URL in your browser:
echo.
echo    http://127.0.0.1:8000
echo.

"%PHP%" -S 127.0.0.1:8000 -t "%ROOT%"
pause
