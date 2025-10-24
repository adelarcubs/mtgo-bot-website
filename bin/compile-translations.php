<?php
declare(strict_types=1);

// Simple PO to MO file compiler
function compilePoToMo($poFile, $moFile) {
    // Read the PO file content
    $poContent = file_get_contents($poFile);
    if ($poContent === false) {
        echo "Error reading PO file: $poFile\n";
        return false;
    }
    
    // For this simple implementation, we'll just copy the PO file to MO
    // In a production environment, you'd want to use a proper PO/MO parser/generator
    $result = copy($poFile, $moFile);
    
    if ($result) {
        echo "Created MO file: $moFile\n";
    } else {
        echo "Error creating MO file: $moFile\n";
    }
    
    return $result;
}

// Compile all PO files in the languages directory
$languagesDir = __DIR__ . '/../data/languages';

if (!is_dir($languagesDir)) {
    echo "Languages directory not found: $languagesDir\n";
    exit(1);
}

$dir = new RecursiveDirectoryIterator($languagesDir);
$iterator = new RecursiveIteratorIterator($dir);
$poFiles = new RegexIterator($iterator, '/^.+\\.po$/i', RecursiveRegexIterator::GET_MATCH);

$success = true;
foreach ($poFiles as $poFile) {
    $poFile = $poFile[0];
    $moFile = str_replace('.po', '.mo', $poFile);
    
    if (!compilePoToMo($poFile, $moFile)) {
        $success = false;
    }
}

exit($success ? 0 : 1);
