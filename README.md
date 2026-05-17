# CampusWire PHP — Setup & Deployment Guide

## Complete PHP/MySQL application ready for WAMP Server

---

## 📁 Project Structure (53 files)

```
campuswire-php/
├── app/
│   ├── config/         → Database & app configuration
│   │   ├── config.php
│   │   └── database.php
│   ├── controllers/    → 10 Controllers (MVC logic)
│   │   ├── AdminController.php
│   │   ├── AlertsController.php
│   │   ├── AuthController.php
│   │   ├── BlogsController.php
│   │   ├── ClubsController.php
│   │   ├── CommunityController.php
│   │   ├── EventsController.php
│   │   ├── HomeController.php
│   │   ├── NewsController.php
│   │   └── ProfileController.php
│   ├── core/           → Base framework classes
│   │   ├── BaseController.php
│   │   └── Session.php
│   ├── middleware/      → Auth protection
│   │   └── AuthMiddleware.php
│   └── models/          → 6 Database models
│       ├── AlertModel.php
│       ├── BlogModel.php
│       ├── DiscussionModel.php
│       ├── EventModel.php
│       ├── NewsModel.php
│       └── UserModel.php
├── database/
│   ├── schema.sql       → Full MySQL table definitions
│   └── seed.php         → Demo data insertion script
├── public/              → Web-accessible root
│   ├── .htaccess        → Apache URL rewriting
│   ├── index.php        → Front Controller (entry point)
│   └── assets/
│       ├── css/style.css
│       └── js/app.js
└── views/               → 17 PHP view templates
    ├── landing.php
    ├── layouts/ (header, footer, sidebar, app_open, app_close)
    ├── auth/ (login, register, forgot)
    ├── dashboard/feed.php
    ├── news/ (show, create, moderation)
    ├── events/ (index, create)
    ├── community/index.php
    ├── blogs/ (index, create, show)
    ├── clubs/index.php
    ├── profile/index.php
    ├── alerts/index.php
    └── admin/ (analytics, users)
```

---

## 🚀 Setup Instructions (Step by Step)

### 1. Install WAMP Server
Download from https://www.wampserver.com and install. Ensure the tray icon turns **green**.

### 2. Copy Project
Copy the entire `campuswire-php` folder to:
```
C:\wamp64\www\campuswire-php
```

### 3. Create Database
1. Open browser → `http://localhost/phpmyadmin`
2. Click **"New"** in the left panel
3. Enter database name: `campuswire_db`
4. Select charset: `utf8mb4_unicode_ci`
5. Click **"Create"**

### 4. Import Schema
1. Select `campuswire_db` in phpMyAdmin
2. Click **"Import"** tab
3. Choose file: `C:\wamp64\www\campuswire-php\database\schema.sql`
4. Click **"Go"** — all 11 tables will be created

### 5. Seed Demo Data
Open your browser and navigate to:
```
http://localhost/campuswire-php/database/seed.php
```
This will create demo users, news articles, events, blogs, discussions, and alerts.

### 6. Enable Apache mod_rewrite
1. Click WAMP tray icon → Apache → Apache Modules
2. Ensure `rewrite_module` is checked (enabled)
3. Restart WAMP if needed

### 7. Access the Application
```
http://localhost/campuswire-php/public/
```

---

## 🔐 Demo Login Credentials

| Role    | Email                    | Password     |
|---------|--------------------------|--------------|
| Admin   | admin@campuswire.com     | password123  |
| Faculty | sharma@campuswire.com    | password123  |
| Student | karthik@campuswire.com   | password123  |

---

## 🧭 All Available Routes

| URL Path                         | Method | Description            | Access        |
|----------------------------------|--------|------------------------|---------------|
| `/`                              | GET    | Landing page           | Public        |
| `/auth/login`                    | GET    | Login form             | Public        |
| `/auth/register`                 | GET    | Register form          | Public        |
| `/auth/forgot`                   | GET    | Reset password form    | Public        |
| `/auth/logout`                   | GET    | Destroy session        | Logged in     |
| `/feed`                          | GET    | Dashboard news feed    | Logged in     |
| `/news/{id}`                     | GET    | Single article view    | Logged in     |
| `/news/create`                   | GET    | Post news form         | Faculty/Admin |
| `/news/pending`                  | GET    | Moderation queue       | Admin         |
| `/events`                        | GET    | Events listing         | Logged in     |
| `/events/create`                 | GET    | Create event form      | Faculty/Admin |
| `/community`                     | GET    | Discussion forum       | Logged in     |
| `/blogs`                         | GET    | Blog articles          | Logged in     |
| `/blogs/create`                  | GET    | Write article form     | Logged in     |
| `/clubs`                         | GET    | Clubs listing          | Logged in     |
| `/profile`                       | GET    | User profile           | Logged in     |
| `/alerts`                        | GET    | Alert notifications    | Logged in     |
| `/admin/analytics`               | GET    | Analytics dashboard    | Admin         |
| `/admin/users`                   | GET    | User management        | Admin         |

---

## 🛡️ Security Implemented

- **Bcrypt** password hashing via `password_hash()`
- **PDO Prepared Statements** for all database queries
- **CSRF tokens** on every form
- **XSS protection** via `htmlspecialchars()` on all output
- **Session fixation prevention** via `session_regenerate_id()`
- **Role-based middleware** protecting admin/faculty routes
- **Secure file uploads** with extension + size validation
