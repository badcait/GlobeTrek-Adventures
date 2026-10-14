# GlobeTrek Adventures

PHP/MySQL travel and tourism booking system for the CSE5009 Web Application Development assignment.

## Run Locally

1. Open XAMPP Control Panel.
2. Start Apache and MySQL.
3. Double-click `run-website.bat`, or from this project folder run:

```powershell
& 'C:\xampp\php\php.exe' -S 127.0.0.1:8000 -t "."
```

4. Open `http://localhost:8000/setup.php` and click **Create / Reset Database**.
5. Open `http://localhost:8000`.

Setup resets existing project tables. Use it only for a disposable local demo database. The supplied database settings and demo accounts are for local coursework use, not public hosting.

## Demo Accounts

- Admin: `admin@globetrek.local` / `admin123`
- Staff: `staff@globetrek.local` / `staff123`
- Customer: `customer@globetrek.local` / `customer123`

## Main Features

- Customer registration and login
- Travel package browsing and search
- Destination, accommodation, vehicle, and guide browsing
- Custom trip planner with booking creation
- Simulated secure payment workflow
- Customer dashboard for bookings and queries
- Staff dashboard for booking confirmation, package updates, and query responses
- Admin dashboard for staff creation, user role management, and reports
- MySQL database schema with seeded data

## Coursework Notes

Real payment gateway and external travel API integrations are simulated for assessment purposes. The website uses local PHP, MySQL, HTML, CSS, and JavaScript to match the technologies taught in lectures.

## Design Evidence

- Figma mockup file: https://www.figma.com/design/TZ3SYiKOMvvfZg5whkW8L6
- Local theme reference: `assets/images/landing-reference.png`
