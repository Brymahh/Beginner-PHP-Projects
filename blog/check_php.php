<?php

function check_phpstan($file_path) {
    // Ensure the file exists
    if (!file_exists($file_path)) {
        throw new Exception("The file $file_path does not exist.");
    }

    // Run PHPStan on the provided file with the specified configuration
    $command = "vendor/bin/phpstan analyse $file_path --level=max -c phpstan.neon";
    $output = [];
    $return_var = 0;

    exec($command, $output, $return_var);

    // Combine output into a single string for parsing
    $output_string = implode("\n", $output);

    // Extract the number of errors and warnings
    preg_match('/Errors:\s+(\d+)/', $output_string, $error_matches);
    preg_match('/Warnings:\s+(\d+)/', $output_string, $warning_matches);

    $errors_count = isset($error_matches[1]) ? (int)$error_matches[1] : 0;
    $warnings_count = isset($warning_matches[1]) ? (int)$warning_matches[1] : 0;

    // Calculate the total issues and score
    $total_issues = $errors_count + $warnings_count;
    $score = 100 - ($total_issues * 10); // Example scoring: 10 points deducted per issue

    return [
        'output' => $output_string,
        'score' => max($score, 0) // Ensure score doesn't go below 0
    ];
}

// Example usage
if (isset($argv[1])) {
    $student_code_file = $argv[1]; // Get file path from command line argument
    try {
        $result = check_phpstan($student_code_file);
        echo "Errors found in your code:\n";
        echo $result['output'] . "\n";
        echo "Code Quality Score: " . $result['score'] . "\n";
    } catch (Exception $e) {
        echo $e->getMessage() . "\n";
    }
} else {
    echo "Please provide the path to the PHP file.\n";
} 
