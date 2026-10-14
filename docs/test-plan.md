# GlobeTrek Adventures Test Plan

## Testing Overview

The website is tested using functional testing, validation testing, security-oriented role testing, and user interface testing. The goal is to prove that customer, staff, and administrator workflows meet the assignment brief.

## Test Cases

| ID | Area | Steps | Expected Result |
| --- | --- | --- | --- |
| T01 | Setup | Start XAMPP MySQL, open `setup.php`, click create/reset database | Tables and demo data are created successfully |
| T02 | Registration | Register with name, valid email, phone, and matching passwords | Customer account is created and user is sent to planner |
| T03 | Duplicate registration | Register using an existing email | Friendly duplicate email error is shown |
| T04 | Login | Login as `customer@globetrek.local` with `customer123` | Customer dashboard opens |
| T05 | Login validation | Login with wrong password | Invalid login message is shown |
| T06 | Package search | Search packages by destination or keyword | Matching packages appear, reset clears filters |
| T07 | Package details | Open a package details page | Destination, activities, price, duration, and booking link appear |
| T08 | Booking | Customer selects package, dates, travelers, accommodation, vehicle, and guide | Booking is created with pending payment status |
| T09 | Booking validation | Submit planner with missing dates or end date before start date | Validation error is shown |
| T10 | Payment | Pay a pending booking using simulated card | Payment record is created and booking becomes paid |
| T11 | Customer dashboard | View dashboard after payment | Booking and payment reference appear |
| T12 | Query submission | Submit contact form as customer | Query appears in customer and staff dashboards |
| T13 | Staff booking management | Login as staff and confirm a paid booking | Booking status updates to confirmed |
| T14 | Staff query response | Staff enters response to customer query | Query status becomes answered and response appears for customer |
| T15 | Package update | Staff changes package price/status | Package listing reflects the update |
| T16 | Admin staff creation | Admin creates a staff account | New staff user appears in user list |
| T17 | Admin reports | Admin opens dashboard | User, booking, revenue, query, destination sales, and payment reports appear |
| T18 | Role security | Customer opens `admin/index.php` or `staff/index.php` | Access denied message is shown |
| T19 | Anonymous security | Logged-out user opens `dashboard.php` | User is redirected to login |
| T20 | SQL injection check | Enter `' OR 1=1 --` in package search/contact fields | Input is treated as text; database remains safe because prepared statements are used |
| T21 | Responsive UI | Resize browser to mobile width | Navigation becomes compact and page content stacks cleanly |

## Feedback Evaluation

Suggested user acceptance feedback questions:

- Was it easy to find and compare packages?
- Was the trip planner clear enough to select accommodation, vehicle, and guide?
- Did booking and payment status feel understandable?
- Could staff easily identify which bookings and queries need action?
- Is the admin dashboard useful for a travel agency manager?

## Known Assumptions

- Payment is simulated and does not process real card details.
- Seed data is used instead of live travel APIs.
- The system is a coursework prototype, so email notifications are represented through dashboard responses.

