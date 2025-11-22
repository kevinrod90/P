<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['excel_file'])) {
    header('Location: index.php');
    exit;
}

$message = '';
$uploadDir = 'uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$file = $_FILES['excel_file'];
$fileName = $file['name'];
$fileTmp = $file['tmp_name'];
$fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

$allowedExts = ['xlsx', 'xls', 'csv'];
if (!in_array($fileExt, $allowedExts)) {
    $message = 'Error: Only XLSX, XLS, or CSV files are allowed.';
} else {
    $newFileName = 'uploaded.' . $fileExt;
    $uploadPath = $uploadDir . $newFileName;
    if (move_uploaded_file($fileTmp, $uploadPath)) {
        $message = 'Note: For XLSX, please save as tab-separated CSV.';

        // Simple tab-separated handling for now
        $dataSheets = [];
        $content = file_get_contents($uploadPath);

        if ($fileExt === 'xlsx' || $fileExt === 'xls') {
            $message = 'Error: XLSX/XLS not supported yet. Please save as tab-separated CSV.';
        } else {
            // Assume tab-separated CSV
            $lines = explode("\n", $content);
            $sheetData = [];
            $sheetName = null;
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '') continue;
                $data = explode("\t", $line);
                if (preg_match('/^--- Sheet: (.+) ---$/', $line, $matches)) {
                    if ($sheetData && $sheetName) {
                        $dataSheets[$sheetName] = $sheetData;
                    }
                    $sheetName = strtolower(str_replace(['WHOLESALE-', 'PROGRESSIVE PRICING-', 'PASS MOVING ITEMS', 'WHOLESALE ', 'FRESH & STAPLE', '&', '(', ')'], ['', '', 'pass_moving_items', '', 'fresh_staple', '', ''], $matches[1]));
                    $sheetData = [];
                } elseif ($sheetName) {
                    $sheetData[] = array_map('trim', $data);
                }
            }
            if ($sheetData && $sheetName) {
                $dataSheets[$sheetName] = $sheetData;
            }
        }

        // Map to our sheet names
        $sheetMappings = [
            'wholesale_fmcg' => 'wholesale-fmcg',
            'wholesale_fresh_staple' => 'wholesale-fresh_staple', // adjust for your sheet names
            'progressive_pricing_fmcg' => 'progressive_pricing-fmcg',
            'pass_moving_items' => 'pass_moving_items',
            'progressive_pricing_fresh_staple' => 'progressive_pricing-fresh_staple'
        ];

        // Update data files
        foreach ($dataSheets as $originalSheet => $data) {
            $sheetKey = array_search($originalSheet, $sheetMappings);
            if ($sheetKey !== false) {
                // Convert tab-separated to PHP array and save
                $phpCode = "<?php\n";
                if (!empty($data)) {
                    $headers = array_shift($data);
                    $phpCode .= '$' . str_replace('-', '_', $sheetKey) . '_headers = ' . var_export($headers, true) . ";\n";
                    $phpCode .= '$' . str_replace('-', '_', $sheetKey) . '_data = ' . var_export($data, true) . ";\n";
                } else {
                    $phpCode .= '$' . str_replace('-', '_', $sheetKey) . '_headers = [];' . "\n";
                    $phpCode .= '$' . str_replace('-', '_', $sheetKey) . '_data = [];' . "\n";
                }
                $phpCode .= "?>";
                file_put_contents('data_' . str_replace('_', '', $sheetKey) . '.php', $phpCode);
            }
        }

        $message = 'Data updated successfully!';
    } else {
        $message = 'Error uploading file.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="3;url=index.php">
    <title>Upload Result</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4 text-center">
        <h3><?php echo $message; ?></h3>
        <p>Redirecting to main page in 3 seconds...</p>
        <a href="index.php" class="btn btn-primary">Go Back</a>
    </div>
</body>
</html>
