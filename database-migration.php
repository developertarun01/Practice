<?php

/**
 * Database Migration Script
 * This script adds new columns to the professionals table for the new features
 * 
 * NEW FIELDS:
 * - skills: TEXT (comma-separated skills)
 * - id_proof_front: VARCHAR(255) (path to front side of ID proof)
 * - id_proof_back: VARCHAR(255) (path to back side of ID proof)
 * - police_verification: VARCHAR(255) (path to police verification document)
 * 
 * The old id_proof_image column is retained for backward compatibility
 * 
 * RUN THIS SCRIPT ONCE TO ADD THE NEW COLUMNS TO YOUR DATABASE
 */

require_once 'includes/config.php';

// Function to check if column exists
function columnExists($table, $column)
{
    global $conn;
    $result = $conn->query("SHOW COLUMNS FROM $table LIKE '$column'");
    return $result->num_rows > 0;
}

// List of columns to add
$columns_to_add = [
    'skills' => "ALTER TABLE professionals ADD COLUMN skills TEXT DEFAULT NULL AFTER hours",
    'id_proof_front' => "ALTER TABLE professionals ADD COLUMN id_proof_front VARCHAR(255) DEFAULT NULL AFTER staff_image",
    'id_proof_back' => "ALTER TABLE professionals ADD COLUMN id_proof_back VARCHAR(255) DEFAULT NULL AFTER id_proof_front",
    'police_verification' => "ALTER TABLE professionals ADD COLUMN police_verification VARCHAR(255) DEFAULT NULL AFTER id_proof_back"
];

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Database Migration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }
        .status {
            margin: 15px 0;
            padding: 12px;
            border-radius: 4px;
            font-weight: 500;
        }
        .status.success {
            background-color: #d1e7d1;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        .status.info {
            background-color: #d1ecf1;
            color: #0c5460;
            border-left: 4px solid #17a2b8;
        }
        .status.error {
            background-color: #f8d7da;
            color: #721c24;
            border-left: 4px solid #f5c6cb;
        }
        .status.warning {
            background-color: #fff3cd;
            color: #856404;
            border-left: 4px solid #ffc107;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table th, table td {
            text-align: left;
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        .button:hover {
            background-color: #0056b3;
        }
        .button.danger {
            background-color: #dc3545;
        }
        .button.danger:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🗂️ Database Migration - Professionals Table</h1>";

$columns_status = [];
$all_exist = true;

echo "<p>Checking for required columns...</p>";
echo "<table>
        <tr>
            <th>Column Name</th>
            <th>Status</th>
            <th>Description</th>
        </tr>";

foreach ($columns_to_add as $column => $sql) {
    $exists = columnExists('professionals', $column);
    $status = $exists ? '✓ EXISTS' : '✗ MISSING';
    $status_class = $exists ? 'success' : 'warning';

    $descriptions = [
        'skills' => 'Comma-separated professional skills',
        'id_proof_front' => 'Front side of ID proof image',
        'id_proof_back' => 'Back side of ID proof image',
        'police_verification' => 'Police verification document'
    ];

    echo "<tr>
            <td><strong>$column</strong></td>
            <td><span class='status $status_class'>$status</span></td>
            <td>" . $descriptions[$column] . "</td>
          </tr>";

    $columns_status[$column] = $exists;
    if (!$exists) $all_exist = false;
}

echo "</table>";

if ($all_exist) {
    echo "<div class='status success'>
            ✓ All required columns already exist! Your database is up to date.
          </div>";
    echo "<p><strong>Migration Status:</strong> Database schema is complete.</p>";
    $migration_needed = false;
} else {
    echo "<div class='status info'>
            ⚠️ Some columns are missing. Click the button below to add them automatically.
          </div>";

    // Add missing columns
    $added = 0;
    $failed = 0;

    if (isset($_GET['run_migration'])) {
        foreach ($columns_to_add as $column => $sql) {
            if (!$columns_status[$column]) {
                if ($conn->query($sql)) {
                    echo "<div class='status success'>✓ Added column: <strong>$column</strong></div>";
                    $added++;
                } else {
                    echo "<div class='status error'>✗ Failed to add column: <strong>$column</strong><br>Error: " . $conn->error . "</div>";
                    $failed++;
                }
            }
        }

        echo "<div class='status success'><strong>Migration Complete!</strong><br>
                Added: $added column(s) | Failed: $failed</div>";
        echo "<p>You can now use the new professional form features:</p>
              <ul>
                <li>✓ Add professional status when creating</li>
                <li>✓ Add professional skills</li>
                <li>✓ Separate front and back ID proof images</li>
                <li>✓ Police verification document</li>
                <li>✓ Filter by hours per day</li>
              </ul>";
    } else {
        echo "<a href='?run_migration=1' class='button' onclick=\"return confirm('This will add missing columns to your database. Continue?')\">
                🔧 Run Migration Now
              </a>";
    }
}

echo "</div>
</body>
</html>";

$conn->close();
