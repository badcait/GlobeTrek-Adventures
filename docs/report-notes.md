# GlobeTrek Adventures Report Notes

## Scenario Understanding

GlobeTrek Adventures is a new travel and tourism company in Negombo. The web application supports customers, travel agency staff, and administrators. Customers can browse packages, customize travel plans, book trips, make simulated payments, and submit queries. Staff can manage bookings, update packages, and respond to inquiries. Administrators can manage staff accounts, oversee bookings, and view reports.

## Similar System Comparison Ideas

Use these criteria to compare two or more travel websites in the report:

- Homepage clarity and first impression
- Navigation structure
- Search and filtering features
- Package detail information
- Booking workflow
- Accommodation and transport support
- Trust signals such as ratings, reviews, and support
- Mobile responsiveness
- Visual consistency and use of images

Suggested websites to analyze manually:

- Booking.com for accommodation search, filtering, ratings, and booking usability.
- Expedia or Trip.com for package-based travel search and multi-service booking.
- International tour agency sites for destination storytelling and cultural package presentation.

## UI Design Decisions

The theme follows the provided landing page reference:

- Large sunset beach hero image for emotional travel appeal.
- Glass-style sticky navigation for a modern travel site feel.
- Coral accent color for primary calls to action.
- Soft white cards for accommodations, packages, and dashboard panels.
- Destination image cards for visual browsing.
- Dark footer for strong page closure and contrast.
- Consistent spacing, typography, and rounded 8px card style.

Figma evidence file: https://www.figma.com/design/TZ3SYiKOMvvfZg5whkW8L6

## Database Design Summary

Main tables:

- `users`: customer, staff, and admin accounts.
- `destinations`: travel locations and destination descriptions.
- `packages`: tour packages linked to destinations.
- `accommodations`: hotels and villas linked to destinations.
- `vehicles`: transport options.
- `guides`: travel guide options.
- `bookings`: customer booking records and selected services.
- `payments`: simulated payment records.
- `queries`: customer support inquiries and staff responses.

## Backend Functionality Evidence

- Registration uses password hashing.
- Login uses password verification and sessions.
- SQL statements use prepared statements for user input.
- Role checks protect customer, staff, and admin pages.
- Booking total is calculated on the server.
- Payment creates a transaction reference and updates booking status.
- Staff and admin dashboards update database records.
- Validation errors are shown for incomplete or invalid forms.

## Demonstration Script

1. Run `setup.php` to create the database.
2. Register a new customer account.
3. Search packages and open a package details page.
4. Use the planner to choose dates, travelers, accommodation, vehicle, and guide.
5. Complete simulated payment.
6. View the booking in the customer dashboard.
7. Submit a query from the contact page.
8. Login as staff and respond to the query.
9. Confirm the customer booking.
10. Login as admin and show user management and reports.
