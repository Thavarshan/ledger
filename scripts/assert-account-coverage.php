<?php

declare(strict_types=1);

use SebastianBergmann\CodeCoverage\CodeCoverage;

$coveragePath = $argv[1] ?? 'build/coverage/account.php';

if (! is_file($coveragePath)) {
    fwrite(STDERR, "Coverage file not found: {$coveragePath}\n");

    exit(1);
}

$coverage = unserialize(file_get_contents($coveragePath));

if (! $coverage instanceof CodeCoverage) {
    fwrite(STDERR, "Invalid PHPUnit coverage file.\n");

    exit(1);
}

$report = $coverage->getReport();
$executableLines = $report->numberOfExecutableLines();
$executedLines = $report->numberOfExecutedLines();
$executableBranches = $report->numberOfExecutableBranches();
$executedBranches = $report->numberOfExecutedBranches();

if ($executableLines !== $executedLines || $executableBranches !== $executedBranches) {
    fwrite(STDERR, sprintf(
        "Account coverage gate failed: lines %d/%d, branches %d/%d.\n",
        $executedLines,
        $executableLines,
        $executedBranches,
        $executableBranches,
    ));

    exit(1);
}

printf(
    "Account coverage gate passed: lines %d/%d, branches %d/%d.\n",
    $executedLines,
    $executableLines,
    $executedBranches,
    $executableBranches,
);
