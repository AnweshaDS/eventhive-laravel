# EventHive - Discover, Host & Attend Events Near You
## Overview
EventHive is a full-stack public event discovery and ticketing platform built with Laravel. Anyone can register as an organizer to host events - concerts, workshops, tech conferences, food fests, comedy nights, and more - while attendees browse, book, and manage tickets from a single open marketplace. The platform is designed as a real-world application, not limited to any university or club, with a focus on clean architecture, role-based access, and a polished user experience.

## Tools & Technologies

Frontend: HTML5, CSS, JavaScript  
Backend: PHP (OCI8)  
Framework: Laravel 11 - MVC version  
Database: MySQL  
Server: XAMPP (Apache)  
ORM: Eloquent  
Templating: Blade  
External API: OpenWeatherMap (via Guzzle HTTP Client)  
QR Code: endroid/qr-code  
Authentication: Laravel Breeze  
Icons: Font Awesome   
Package Manager: Composer + npm  
Version Control: Git + GitHub  



## Application Features

### Public

1. Browse all published events with category, city, and keyword filters  
2. Live AJAX search - results update instantly without page reload, powered by the REST API  
3. Event detail page with full information, ticket tiers, reviews, and weather forecast  
4. Weather widget using OpenWeatherMap API - shows forecast or current weather for the event city  

### Authentication

1. Register as an Attendee or Organizer with role selection  
2. Secure login with bcrypt hashing and role-based redirect  
3. CSRF protection and session management on all forms  

### Attendee

1. Book tickets with quantity selection and real-time price calculation  
2. QR code ticket generated automatically on every successful booking  
3. Download QR code as PNG and verify authenticity via a unique token URL  
4. My Tickets page with Active and Cancelled tabs  
5. Cancel bookings up to 7 days before the event with automatic refund tracking  
6. Profile management - update personal info and change password  

### Organizer

1. Personal dashboard with event stats and revenue overview  
2. Full CRUD for events - create, edit, and delete with dynamic ticket tier management  
3. Banner image upload support  
4. Attendees list filtered by event with QR scan status  

### Admin

1. Platform-wide dashboard with user, event, booking, and revenue statistics  
2. Manage all users - search, filter by role, change roles, and delete accounts  
3. Manage all events - change status (published/draft/cancelled) and delete  

### REST API

1. GET /api/v1/events - list all published upcoming events with optional filters  
2. GET /api/v1/events/{id} - single event with ticket types and reviews  
3. GET /api/v1/events/stats - platform statistics  
4. GET /api/v1/events/categories - all available categories  

### Security

1. Custom RoleMiddleware protecting organizer and admin routes  
2. Eloquent parameterized queries preventing SQL injection  
3. Blade auto-escaping preventing XSS  
4. Environment variables for all sensitive keys  


## Conclusion
This project shows how a complete examination system can be built with the database as the core of all business logic. By implementing answer evaluation, result generation, access enforcement, and activity logging through Oracle PL/SQL rather than application code, the system ensures data consistency and integrity regardless of the frontend layer used. The Laravel framework provides a clean MVC structure for the application layer while the Oracle database and PL/SQL components remain the single source of truth for all operations.
