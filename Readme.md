Project Overview: “Review Transit Backend”
This is a shipment tracking and feedback management system built for both public users (customers) and admins (internal staff). Its main purpose is to let customers track packages in real time, submit feedback, and allow admins to manage shipments, track events, and handle users. It also includes full authentication and role-based access control.

1. Users & Roles
Public Users


Can track shipments by tracking number.


Can submit feedback about shipments or services.


No login required for these actions.


Admins


Must login to the system via email & password.


Roles:


admin: Can manage shipments, tracking events, and feedback.


super_admin: Can also manage other admin users, set roles, and reset passwords.




JWT-based authentication ensures that only logged-in admins can access protected routes.



2. Core Modules
A. Shipments


Represent packages being sent/delivered.


Admin Capabilities:


Create shipments with sender & recipient info.


Update shipment details (weight, contents, expected delivery date, recipient email).


Delete/cancel shipments.




Public Capabilities:


Track shipment by trackingNumber.




Tracking Events:
Each shipment has a list of tracking events (like "Picked up," "In transit," "Out for delivery"). Admins can add these events. Public users see them when tracking.



B. Feedback


Public users can submit feedback on shipments/services.


Feedback has fields like:


Name, Email, Phone


Rating (1–5)


Type: review or complaint


Shipment number (optional)


Status: new, in_review, resolved




Admin Capabilities:


List all feedback (filter by status, type, rating).


Get single feedback detail.


Update status and add internal notes.


Mark as spam if necessary.





C. Users


Admins manage users internally.


Capabilities:


Create new admin accounts.


List all admins.


Change user roles (admin/super_admin) with rules:


You cannot demote yourself.


Last super_admin cannot be demoted.




Change/set passwords.





D. Authentication & Security


JWT-based auth:


AuthMiddleware validates tokens and loads user info.


RequireRole middleware ensures role-based access control.




Passwords are hashed before storing.


Users can change their own password via /api/auth/me/password.



E. Rate Limiting


RateLimiter middleware prevents abuse of public endpoints.


Ensures that an IP cannot overload the system.



3. API Structure
Public Endpoints


GET /api/public/track/:trackingNumber → Track shipment status.


POST /api/public/feedback → Submit feedback.


Authentication Endpoints


POST /api/auth/login → Admin login, returns JWT.


POST /api/auth/register → Register admin.


PUT /api/auth/me/password → Change own password.


Admin Endpoints


Shipments (AdminHandler)


CRUD operations on shipments.


Add tracking events.




Feedback (AdminFeedbackHandler)


List feedback with filters.


Get feedback by ID.


Update feedback status/notes.




Users (AdminUsersHandler)


List admins.


Create admins.


Update roles.


Set passwords.





4. Tech Stack & Architecture


Backend: Go (Gin framework)


Database: MongoDB (for Users, Feedback) + possibly embedded collections for tracking events.


Authentication: JWT


Validation: Gin binding (binding:"required") + custom error handling.


Middleware:


AuthMiddleware → JWT validation.


RequireRole → Role-based access.


RateLimitMiddleware → Protects public endpoints.





5. Key Features


Public shipment tracking with event history.


Feedback system with admin review and status updates.


Full admin management with role-based control.


JWT authentication with secure password handling.


Rate-limited public APIs to prevent abuse.


UUID tracking for shipments and events, MongoDB ObjectIDs for users and feedback.


Scalable structure with services and handlers separated cleanly.



In short:
This project is essentially a modern, role-based shipment tracking and customer feedback system. Public users can track shipments and give feedback, while admins manage shipments, track events, and handle users and feedback—all securely, with JWT-based authentication and rate-limiting in place.



<!-- File Structure -->
/project-root
│
├── config/
│   └── db.php                 // PDO connection
│
├── classes/
│   ├── AdminUser.php          // Admin CRUD + auth
│   ├── Auth.php               // Login/Token handling
│   ├── Branch.php             // Branch operations
│   ├── Operations.php         // Daily operations logic
│   ├── Trip.php               // Trips + scheduling
│   ├── Booking.php            // Ticket bookings + payments
│   ├── Finance.php            // Finance records
│   └── Utils.php              // Shared tools (validation, random strings)
│
├── middlewares/
│   ├── auth.php               // JWT/Session check
│   └── super_admin.php        // Restrict sensitive operations
│
├── api/
│   ├── admin/
│   │   ├── list.php
│   │   ├── create.php
│   │   ├── update-role.php
│   │   └── set-password.php
│   │
│   ├── branches/
│   │   ├── list.php
│   │   ├── create.php
│   │   ├── update.php
│   │   └── delete.php
│   │
│   ├── trips/
│   │   ├── list.php
│   │   ├── create.php
│   │   ├── update.php
│   │   └── delete.php
│   │
│   ├── bookings/
│   │   ├── book.php
│   │   ├── verify-payment.php
│   │   └── list.php
│   │
│   └── dashboard/
│       ├── stats.php
│       ├── today.php
│       └── finance.php
│
└── index.php                  // Router (optional)
