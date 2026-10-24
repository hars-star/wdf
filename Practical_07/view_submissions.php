<?php
$storageDir = __DIR__ . '/storage';
$jsonFile = $storageDir . '/submissions.json';
$records = [];

if (file_exists($jsonFile)) {
    $content = file_get_contents($jsonFile);
    $records = json_decode($content, true) ?: [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Practical 7: View Stored Submissions</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .container { max-width: 1050px; }
        .stats-badge {
            display: inline-block;
            background: #dbeafe;
            color: #1e40af;
            padding: 0.3rem 0.8rem;
            border-radius: 9999px;
            font-weight: bold;
            font-size: 0.85rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap;">
                <div>
                    <h1>Stored Submissions Directory</h1>
                    <p style="color: var(--text-muted);">Loaded directly from server-side JSON/CSV files.</p>
                </div>
                <a href="index.php" class="btn btn-secondary">← Back to Form</a>
            </div>

            <span class="stats-badge">Total Stored Records: <?= count($records) ?></span>

            <?php if (empty($records)): ?>
                <div class="alert alert-danger" style="margin-top: 1rem;">
                    No submissions recorded yet. Return to the form and submit your first entry.
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Timestamp</th>
                            <th>Full Name</th>
                            <th>Email Address</th>
                            <th>Mobile</th>
                            <th>Department</th>
                            <th>Inquiry Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_reverse($records) as $idx => $row): ?>
                            <tr>
                                <td><?= count($records) - $idx ?></td>
                                <td><?= htmlspecialchars($row['timestamp']) ?></td>
                                <td><strong><?= htmlspecialchars($row['name']) ?></strong></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td><?= htmlspecialchars($row['mobile']) ?></td>
                                <td><?= htmlspecialchars($row['department']) ?></td>
                                <td><?= htmlspecialchars($row['inquiry']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
