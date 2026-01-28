# Bulk Order Assignment System

A full-stack web application designed to automate the assignment of delivery orders to couriers based on location compatibility and daily capacity constraints. This project demonstrates core backend logic, database management, and a clean frontend interface.

🚀 Features

*   Automated Assignment Algorithm: Greedily matches orders to couriers based on:
    *   Location: Courier must serve the order's delivery location.
    *   Capacity: Courier must have remaining daily capacity.
*   Real-time Dashboard: View Unassigned Orders, Available Couriers, and Current Assignments.
*   System Reset: One-click reset to clear all assignments and restore original states.
*   Activity Logging: Tracks system actions (Assignments, Resets) for auditing.
*   Database Transactions: Ensures data integrity during bulk updates.

🛠️ Tech Stack

*   Frontend: HTML5, CSS3, JavaScript (Vanilla ES6+), Fetch API.
*   Backend: PHP (OOP & Procedural), RESTful API architecture.
*   Database: MySQL (Relational Data Model).
*   Environment: MAMP/XAMPP (Apache/Nginx Server).

📂 Project Structure

```
Losung360p/
├── api/                    # Backend API Endpoints
│   ├── bulkAssignOrders.php    # Core Assignment Logic
│   ├── getUnassignedOrders.php # Fetch Orders
│   ├── getAvailableCouriers.php# Fetch Couriers
│   ├── resetAssignments.php    # Reset System
│   └── getLogs.php             # System Logs
├── config/
│   └── db.php              # Database Connection Class
├── models/
│   ├── Assignment.php      # Assignment Model
│   ├── Courier.php         # Courier Model
│   └── Order.php           # Order Model
├── logs/                   # System Log Files
│   └── system.log
└── index.php               # Main Dashboard Interface
```

⚙️ Setup & Installation

1.  Clone the Repository:
    ```bash
    git clone 
    ```

2.  Configure Database:
    *   Create a MySQL database named `bulk_order_system`.
    *   Import the provided SQL schema (not included in repo, assuming standard structure):
        *   `orders` (order_id, delivery_location, order_value, status)
        *   `couriers` (id, name, serviceable_locations, daily_capacity, current_assigned_count)
        *   `order_assignments` (assignment_id, order_id, agent_id, assignment_date)
    *   Update `config/db.php` with your database credentials.

3.  Run the Application:
    *   Place the project folder in your local server directory (e.g., `htdocs` for MAMP/XAMPP).
    *   Start Apache and MySQL.
    *   Open your browser and navigate to: `http://localhost:8888/Losung360p/`

🔌 API Reference

| Method | Endpoint | Description |
| :--- | :--- | :--- |
| `GET` | `/api/getUnassignedOrders.php` | Fetches all pending orders. |
| `GET` | `/api/getAvailableCouriers.php` | Fetches couriers and their capacity. |
| `POST` | `/api/bulkAssignOrders.php` | Triggers the assignment algorithm. |
| `POST` | `/api/resetAssignments.php` | Resets all data to initial state. |

🧠 Core Logic (The Algorithm)

The assignment logic follows a Greedy Approach:
1.  Iterates through all `UNASSIGNED` orders.
2.  For each order, checks the list of `active` couriers.
3.  Assigns the order to the first available courier who:
    *   Services the order's location.
    *   Has `current_assigned_count < daily_capacity`.
4.  Updates the database transactions safely to prevent race conditions.

---
Author: Mohd Raza Khan
