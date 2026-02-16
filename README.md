# WordPress Theme Project -- EventPress Demo

This project is a custom WordPress theme featuring **Blog** and **Events
(Custom Post Type)** sections.\
The project is fully runnable using **Docker Compose** to ensure
consistent development environments.

------------------------------------------------------------------------

# 📌 Project Goal

Transform static design files into a fully dynamic WordPress theme.\
All content is managed through the WordPress CMS.

------------------------------------------------------------------------

# 🚀 Features

## 1️⃣ Blog

-   Standard WordPress posts
-   Categories
-   Dynamic archive & single pages
-   Featured image support

## 2️⃣ Events (Custom Post Type)

-   Dedicated `event` post type
-   Custom fields:
    -   Start Date
    -   End Date
-   Separate Event Categories
-   Sorted dynamically by event date

------------------------------------------------------------------------

# 📁 Project Structure

    EventPress-Demo/
    │
    ├── README.md
    │
    └── wordpress-docker/
        │
        ├── docker-compose.yml
        │
        ├── wp-content/
        │   └── themes/
        │       └── test-wordpress/
        │           ├── style.css
        │           ├── functions.php
        │           ├── index.php
        │           ├── header.php
        │           ├── footer.php
        │           ├── single.php
        │           ├── archive.php
        │           ├── template-parts/
        │           └── assets/
        │
        └── db-init/
            └── database.sql   (optional)

------------------------------------------------------------------------

# 🐳 Run with Docker

## 1) Clone Repository

    git clone https://github.com/Kkanjana/EventPress-Demo.git
    cd EventPress-Demo/wordpress-docker

## 2) Start Containers

    docker compose up -d

## 3) Access

-   WordPress: http://localhost:8000
-   phpMyAdmin: http://localhost:8080

------------------------------------------------------------------------

# 🔐 Test Login

After installation:

URL: http://localhost:8000/wp-admin\
Username: Kanjana
Password: WP_kan@6629

(For demo purposes only)

------------------------------------------------------------------------

# 🗄 Database Configuration

From docker-compose.yml:

-   DB Host: db
-   Database: wpdb
-   User: wpuser
-   Password: wppass
-   Root Password: root

------------------------------------------------------------------------

# 🔁 Useful Commands

Stop containers:

    docker compose down

Reset everything (remove database):

    docker compose down -v

View logs:

    docker compose logs -f

------------------------------------------------------------------------

# 🛠 Development Requirements

-   WordPress
-   PHP
-   HTML, CSS, JavaScript
-   Understanding of WordPress Theme Development Best Practices

------------------------------------------------------------------------

# 📦 Deliverable

This repository provides:

-   Fully functional WordPress theme
-   Docker environment for self-hosting
-   Test admin account
-   Optional SQL dump for database setup

------------------------------------------------------------------------

# 📌 License

For educational and demo purposes only.