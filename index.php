<?php
// Include data from Excel sheets
include 'data_wholesale_fmcg.php';
include 'data_wholesale_fresh_staple.php';
include 'data_progressive_pricing_fmcg.php';
include 'data_pass_moving_items.php';
include 'data_progressive_pricing_fresh_staple.php';

// Helper function to render table
function renderTable($data, $headers) {
    echo '<div class="table-responsive">';
    echo '<table class="table table-striped table-bordered">';
    echo '<thead class="table-dark"><tr>';
    foreach ($headers as $header) {
        echo '<th>' . htmlspecialchars($header) . '</th>';
    }
    echo '</tr></thead><tbody>';
    foreach ($data as $row) {
        echo '<tr>';
        foreach ($row as $cell) {
            echo '<td>' . htmlspecialchars($cell) . '</td>';
        }
        echo '</tr>';
    }
    echo '</tbody></table></div>';
}

// Default sheet
$activeSheet = isset($_GET['sheet']) ? $_GET['sheet'] : 'wholesale_fmcg';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pricing Web App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
        body {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            -webkit-touch-callout: none;
        }
        table {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
        input, button {
            -webkit-user-select: text;
            -moz-user-select: text;
            -ms-user-select: text;
            user-select: text;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="?">Pricing App</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $activeSheet == 'wholesale_fmcg' ? 'active' : ''; ?>" href="?sheet=wholesale_fmcg">WHOLESALE-FMCG</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $activeSheet == 'wholesale_fresh_staple' ? 'active' : ''; ?>" href="?sheet=wholesale_fresh_staple">WHOLESALE-Fresh & Staple</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $activeSheet == 'progressive_pricing_fmcg' ? 'active' : ''; ?>" href="?sheet=progressive_pricing_fmcg">PROGRESSIVE PRICING-FMCG</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $activeSheet == 'pass_moving_items' ? 'active' : ''; ?>" href="?sheet=pass_moving_items">PASS MOVING ITEMS</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $activeSheet == 'progressive_pricing_fresh_staple' ? 'active' : ''; ?>" href="?sheet=progressive_pricing_fresh_staple">PROGRESSIVE PRICING-Fresh & Sta</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><?php echo strtoupper(str_replace('_', '-', $activeSheet)); ?></h2>
            <button class="btn btn-success" onclick="toggleUpload()">Upload New Data (CSV)</button>
        </div>

        <div id="upload-form" style="display: none;" class="card p-3 mb-4">
            <form action="upload.php" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <input type="file" name="excel_file" class="form-control" accept=".csv" required>
                    <small class="form-text text-muted">Upload a tab-separated CSV file (save XLSX as CSV from Excel with tab delimiter).</small>
                </div>
                <button type="submit" class="btn btn-primary">Upload and Update</button>
            </form>
        </div>

        <div class="mb-3">
            <input type="text" class="form-control" id="searchInput" placeholder="Search items..." onkeyup="filterTable()">
        </div>

        <div id="table-container">
            <!-- Table will be loaded here via JS -->
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Security measures to prevent inspection
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            alert('Right-click disabled for security.');
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'F12' || (e.ctrlKey && e.key === 'u') || (e.ctrlKey && e.shiftKey && e.key === 'I') || (e.ctrlKey && e.shiftKey && e.key === 'J') || (e.ctrlKey && e.shiftKey && e.key === 'C')) {
                e.preventDefault();
                alert('Developer tools are disabled.');
                return false;
            }
        });

        // Disable copy
        document.addEventListener('copy', function(e) {
            e.preventDefault();
            alert('Copy is disabled.');
        });

        // Detect dev tools
        setInterval(function() {
            if (window.outerHeight - window.innerHeight > 200 || window.outerWidth - window.innerWidth > 200) {
                alert('Developer tools detected. This may violate terms of use.');
            }
        }, 1000);

        let currentData = [];
        let currentHeaders = [];

        async function loadData(sheet) {
            try {
                const response = await fetch(`data_loader.php?sheet=${sheet}`);
                const encodedData = await response.text();
                const jsonString = atob(encodedData);
                const data = JSON.parse(jsonString);
                currentHeaders = data.headers;
                currentData = data.data;
                renderTable();
            } catch (error) {
                console.error('Error loading data:', error);
            }
        }

function renderTable() {
            const container = document.getElementById('table-container');
            let html = '<div class="table-responsive">';
            html += '<table class="table table-striped table-bordered">';
            html += '<thead class="table-dark"><tr>';
            currentHeaders.forEach(header => {
                html += `<th>${header || ''}</th>`;
            });
            html += '</tr></thead><tbody>';
            currentData.forEach(row => {
                html += '<tr>';
                row.forEach(cell => {
                    html += `<td>${cell || ''}</td>`;
                });
                html += '</tr>';
            });
            html += '</tbody></table></div>';
            container.innerHTML = html;
        }

        function filterTable() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toUpperCase();
            const rows = document.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                let match = false;
                cells.forEach(cell => {
                    const txtValue = cell.textContent || cell.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        match = true;
                    }
                });
                row.style.display = match ? '' : 'none';
            });
        }

        function toggleUpload() {
            const form = document.getElementById('upload-form');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }

        // Load initial data
        const urlParams = new URLSearchParams(window.location.search);
        const activeSheet = urlParams.get('sheet') || 'wholesale_fmcg';
        loadData(activeSheet);
    </script>
</body>
