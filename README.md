# 📦 Bulk Order Assignment System

A high-performance backend service designed to efficiently assign thousands of orders to couriers in bulk. Built with **PHP** and **MySQL**, featuring race-condition handling, REST APIs, and a real-time dashboard.

![Dashboard Preview](https://via.placeholder.com/800x400?text=Dashboard+Preview) *(Replace with actual screenshot)*

## 🚀 Key Features
*   **Bulk Assignment Algorithm**: Optimizes order distribution based on courier capacity and location.
*   **Concurrency Safe**: Uses atomic database updates to prevent race conditions during parallel execution.
*   **RESTful API**: Clean endpoints for managing orders, couriers, and assignments.
*   **Interactive Dashboard**: A simple frontend (HTML/JS) to visualize data and trigger assignments.
*   **Scalable Schema**: Optimized MySQL indexes for fast retrieval of unassigned orders.

## 🛠️ Tech Stack
*   **Backend**: PHP 8.x (Vanilla, No Framework)
*   **Database**: MySQL 8.0+
*   **Frontend**: HTML5, CSS3, JavaScript (Fetch API)

## 📋 System Design
For a deep dive into the database schema and assignment logic, check out the [Design Document](DESIGN_DOCUMENT.md).

## Setup

## Setup

1.  **Database Setup**:
    *   Ensure MySQL is running.
    *   Create a database named `losung360`.
    *   **Note**: The default Homebrew MySQL installation has **no password** for the `root` user. Just press Enter if prompted.
    *   Import the schema:
        ```bash
        mysql -u root -p losung360 < sql/schema.sql
        ```
    *   (Optional) Configure credentials in `src/Database.php`.

2.  **Seed Data**:
    *   Populate the database with test data:
        ```bash
        php scripts/seed.php
        ```

3.  **Run API**:
    *   You can use the built-in PHP server for testing:
        ```bash
        php -S localhost:8000 -t public
        ```

## API Usage

*   **Assign Orders**:
    ```bash
    curl -X POST http://localhost:8000/assignments/bulk
    ```

*   **View Unassigned Orders**:
    ```bash
    curl "http://localhost:8000/orders/unassigned?location=Zone%20A"
    ```
# Bulk_Order_Assignment_System
