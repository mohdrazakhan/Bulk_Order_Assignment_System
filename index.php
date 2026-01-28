<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bulk Order Assignment System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            padding: 20px;
        }
        h1 {
            text-align: center;
        }
        button {
            padding: 10px 16px;
            background: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
        button:hover {
            background: #0056b3;
        }
        .container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-top: 20px;
        }
        .box {
            background: white;
            padding: 15px;
            border-radius: 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background: #f0f0f0;
        }
    </style>
</head>
<body>

<h1>Bulk Order Assignment System</h1>

<div style="text-align:center;">
    <button onclick="assignOrders()" class="btn btn-primary">Assign Orders</button>
    <button onclick="resetOrders()" style="background:#dc3545;margin-left:10px;">Reset Orders</button>
<button onclick="viewLogs()" style="background:#6c757d;margin-left:10px;">View Logs</button>

</div>



<div class="container">
    <div class="box">
        <h3>Unassigned Orders</h3>
        <table id="ordersTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Location</th>
                    <th>Value</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <div class="box">
        <h3>Available Couriers</h3>
        <table id="couriersTable">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Locations</th>
                    <th>Capacity</th>
                    <th>Assigned</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div class="box" style="margin-top:20px;">
    <h3>Order Assignments</h3>
    <table id="assignmentsTable">
        <thead>
            <tr>
                <th>Order Location</th>
                <th>Courier</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<script>
function loadOrders() {
    fetch('api/getUnassignedOrders.php')
        .then(res => res.json())
        .then(data => {
            const tbody = document.querySelector('#ordersTable tbody');
            tbody.innerHTML = '';
            data.forEach(o => {
                tbody.innerHTML += `
                    <tr>
                        <td>${o.order_id}</td>
                        <td>${o.delivery_location}</td>
                        <td>${o.order_value}</td>
                        <td>${o.status}</td>
                    </tr>`;
            });
        });
}

function loadCouriers() {
    fetch('api/getAvailableCouriers.php')
        .then(res => res.json())
        .then(data => {
            const tbody = document.querySelector('#couriersTable tbody');
            tbody.innerHTML = '';
            data.forEach(c => {
                tbody.innerHTML += `
                    <tr>
                        <td>${c.name}</td>
                        <td>${c.serviceable_locations}</td>
                        <td>${c.daily_capacity}</td>
                        <td>${c.current_assigned_count}</td>
                    </tr>`;
            });
        });
}

function loadAssignments() {
    fetch('api/getAssignments.php')
        .then(res => res.json())
        .then(data => {
            const tbody = document.querySelector('#assignmentsTable tbody');
            tbody.innerHTML = '';
            data.forEach(a => {
                tbody.innerHTML += `
                    <tr>
                        <td>${a.delivery_location}</td>
                        <td>${a.courier}</td>
                        <td>${a.assignment_date}</td>
                    </tr>`;
            });
        });
}

function resetOrders() {
    if (!confirm("Are you sure you want to reset all assignments?")) return;

    fetch('api/resetAssignments.php')
        .then(res => res.json())
        .then(data => {
            alert("System reset successful!");
            loadOrders();
            loadCouriers();
            loadAssignments();
        });
}
function viewLogs() {
    fetch('api/getLogs.php')
        .then(res => res.json())
        .then(data => {
            if (data.length === 0) {
                alert("No logs available");
            } else {
                alert(data.join("\n"));
            }
        });
}


async function assignOrders() {
    try {
        const res = await fetch("api/bulkAssignOrders.php", {
            method: "POST"
        });

        const data = await res.json();

        if (data.success) {
            alert("Orders assigned successfully");
            location.reload();
        } else {
            alert(data.message || "Assignment failed");
        }
    } catch (e) {
        alert("Error calling assignment API");
        console.error(e);
    }
}
// Initial load
loadOrders();
loadCouriers();
loadAssignments();
</script>

</body>
</html>
