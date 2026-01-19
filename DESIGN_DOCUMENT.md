# System Design Document: Bulk Order Assignment System

## 1. Database Design

### Schema Overview
The database is designed for data integrity and high-performance querying of unassigned orders and available couriers.

#### Tables
*   **couriers**: Stores courier details and capacity.
    *   `id` (PK), `daily_capacity`, `current_assigned_count`.
*   **courier_locations**: normalized mapping of couriers to serviceable zones.
    *   `courier_id` (FK), `location`.
    *   *Index*: `(location, courier_id)` for fast retrieval of couriers in a specific zone.
*   **orders**: Stores order details.
    *   `order_id` (PK), `delivery_location`, `status`, `order_date`.
    *   *Index*: `(status, delivery_location, order_date)` covers the most frequent query: "Get unassigned orders in Zone X sorted by date".
*   **assignments**: Logs successful assignments.
    *   `assignment_id` (PK), `order_id` (Unique FK), `courier_id` (FK).

### Optimization Strategies
*   **Composite Indexes**: The `orders` table index allows the database to filter by `status`, drill down to `delivery_location`, and sort by `order_date` without a separate sort step (filesort).
*   **Atomic Updates**: Courier capacity is managed via `UPDATE couriers SET count = count + 1 WHERE id = ? AND count < capacity`. This prevents over-assignment without complex locking mechanisms.

---

## 2. Bulk Assignment Logic

### Algorithm: **Location-Based Load Balancing**

1.  **Grouping**: The system first identifies all locations with 'UNASSIGNED' orders.
2.  **Batch Processing**: For each location, orders are fetched in batches (e.g., 100) to manage memory usage.
3.  **Matching**:
    *   Couriers responsible for that location are fetched, filtering out those who are already at full capacity (`current_assigned_count < daily_capacity`).
    *   The system iterates through the unassigned orders (FIFO - oldest first).
    *   It attempts to assign the order to the first available courier.
4.  **Concurrency Control**:
    *   The assignment uses an **Optimistic Locking** approach via the SQL `UPDATE` statement.
    *   If the update affects 0 rows, it implies the courier became full in the split second between reading and writing (race condition). The system gracefully handles this by trying the next courier.
5.  **Failure Handling**:
    *   Transactions ensure that an order is only marked 'ASSIGNED' if the courier capacity was successfully reserved.
    *   Failed assignments (e.g., no couriers available) are logged or skipped until the next run.

---

## 3. API Design

### Endpoints

*   `GET /orders/unassigned?location={zone}`
    *   Fetches unassigned orders for a specific zone.
    *   **Response**: JSON array of orders.
*   `GET /couriers/available?location={zone}`
    *   Fetches couriers in a zone who have remaining capacity.
    *   **Response**: JSON array of couriers.
*   `POST /assignments/bulk`
    *   Triggers the bulk assignment process.
    *   **Response**: `{"message": "Bulk assignment completed", "details": {"total_assigned": 50, "errors": []}}`
*   `GET /assignments/results`
    *   (Placeholder) View recent assignment logs.

---

## 4. Edge Cases & Error Handling

*   **No Available Couriers**: The system logs the error for that batch and moves on. The orders remain 'UNASSIGNED' and will be picked up in the next run.
*   **Race Conditions**: Handled by the atomic SQL update on `couriers`.
*   **Partial Assignments**: If a run crashes halfway, database transactions (if fully enabled) would roll back. In our script, we process per-order transactions to ensure maximum progress (at-least-once processing).
*   **Duplicate Prevention**: The `orders.status` check and unique constraint on `assignments.order_id` prevent double booking.

## 5. Scalability
*   **Pagination**: Order fetching is batched (`LIMIT 100`) to scale to thousands of orders.
*   **Indexes**: Essential for performance as table sizes grow to millions of rows.
*   **Horizontal Scaling**: The PHP script is stateless. Multiple instances can run if the database handles the locking (which it does via the atomic updates).
