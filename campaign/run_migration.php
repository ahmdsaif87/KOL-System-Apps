<?php
/**
 * Standalone Migration Script
 * Nuke semua tabel di DB dan import ulang dari kol.sql
 *
 * Cara pakai:
 *   php run_migration.php
 */

// Disable exception mode
mysqli_report(MYSQLI_REPORT_OFF);

$host     = 'localhost';
$user     = 'root';
$password = '';
$dbname   = 'KOL';
$sqlFile  = __DIR__ . '/kol.sql';

if (!file_exists($sqlFile)) {
    die("ERROR: kol.sql not found at $sqlFile\n");
}

echo "Connecting to MySQL...\n";
$mysqli = new mysqli($host, $user, $password);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error . "\n");
}
echo "Connected to MySQL server OK.\n";

$mysqli->query("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8 COLLATE utf8_general_ci");
$mysqli->select_db($dbname);
$mysqli->set_charset('utf8');

// ---- Drop all existing tables ----
$mysqli->query("SET FOREIGN_KEY_CHECKS = 0");
echo "Dropping all existing tables...\n";
$result = $mysqli->query("SHOW TABLES");
while ($row = $result->fetch_array()) {
    $t = $row[0];
    $mysqli->query("DROP TABLE IF EXISTS `$t`");
    echo "  Dropped: $t\n";
}
echo "All tables dropped.\n\n";

// ---- Import kol.sql ----
echo "Reading kol.sql...\n";
$sql = file_get_contents($sqlFile);

// Strip comments
$sql = preg_replace('/^--.*$/m', '', $sql);
$sql = preg_replace('/\/\*![0-9]+ .*?\*\//s', '', $sql);
$sql = preg_replace('/\/\*.*?\*\//s', '', $sql);

// Split statements with DELIMITER support
$statements = [];
$lines = explode("\n", $sql);
$buf = '';
$delimiter = ';';

foreach ($lines as $line) {
    $trimmedLine = trim($line);
    
    // Ignore empty lines
    if ($trimmedLine === '') {
        continue;
    }
    
    // Handle DELIMITER change
    if (strpos(strtoupper($trimmedLine), 'DELIMITER ') === 0) {
        $delimiter = trim(substr($trimmedLine, 10));
        continue;
    }
    
    // Add line to buffer
    if ($buf !== '') {
        $buf .= "\n";
    }
    $buf .= $line;
    
    // Check if buffer ends with current delimiter
    if (substr(rtrim($buf), -strlen($delimiter)) === $delimiter) {
        // Remove delimiter from end of statement
        $stmt = rtrim($buf);
        $stmt = substr($stmt, 0, -strlen($delimiter));
        $stmt = trim($stmt);
        
        if ($stmt !== '') {
            $statements[] = $stmt;
        }
        $buf = '';
    }
}
if (trim($buf) !== '') {
    $statements[] = trim($buf);
}

echo "Total statements: " . count($statements) . "\n\n";

$ok = $fail = $skip = 0;

foreach ($statements as $idx => $stmt) {
    if (trim($stmt) === '') { $skip++; continue; }
    if (preg_match('/^(START TRANSACTION|COMMIT|ROLLBACK|SET\s+SQL_MODE|SET\s+time_zone)/i', $stmt)) {
        $skip++;
        continue;
    }
    if ($mysqli->query($stmt) === TRUE) {
        $ok++;
    } else {
        $fail++;
        echo "  [WARN] Stmt #" . ($idx+1) . " failed: " . $mysqli->error . "\n";
        echo "  SQL: " . mb_substr($stmt, 0, 150) . "\n\n";
    }
}

$mysqli->query("SET FOREIGN_KEY_CHECKS = 1");
$mysqli->close();

echo "========================================\n";
echo "Done! OK: $ok  FAILED: $fail  SKIPPED: $skip\n\n";

// Verify tables
$mysqli2 = new mysqli($host, $user, $password, $dbname);
$r = $mysqli2->query("SHOW TABLES");
$tables = [];
while ($row = $r->fetch_array()) $tables[] = $row[0];
$mysqli2->close();

echo "Tables in DB (" . count($tables) . "):\n";
foreach ($tables as $t) echo "  - $t\n";
