<?php

function check_csslint($file_path) {
    // Ensure the file exists
    if (!file_exists($file_path)) {
        throw new Exception("The file $file_path does not exist.");
    }

    // Run CSSLint on the provided file
    $command = "csslint $file_path";
    $output = [];
    $return_var = 0;

    exec($command, $output, $return_var);

    // Combine output into a single string for parsing
    $output_string = implode("\n", $output);

    // Extract the number of issues (warnings and errors)
    $issue_count = substr_count($output_string, 'warning') + substr_count($output_string, 'error');

    // Calculate the score (keeping it out of 100)
    $score = 100 - ($issue_count * 10); // Deduct 10 points per issue
    $score = max($score, 0); // Ensure score doesn't go below 0

    return [
        'output' => $output_string,
        'score' => $score
    ];
}

// Example usage
if (isset($argv[1])) {
    $student_code_file = $argv[1]; // Get file path from command line argument
    try {
        $result = check_csslint($student_code_file);
        echo "CSSLint Output:\n";
        echo $result['output'] . "\n";
        echo "Code Quality Score: " . $result['score'] . "\n";
    } catch (Exception $e) {
        echo $e->getMessage() . "\n";
    }
} else {
    echo "Please provide the path to the CSS file.\n";
}
