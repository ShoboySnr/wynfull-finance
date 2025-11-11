<?php
/**
 * Laravel Reverb Monitor for cPanel
 * This script monitors and restarts Reverb if it's not running
 */

// Configuration
$projectPath = '/home/alexizyd/public_html/app/wynfull-finance';
$pidFile = $projectPath . '/storage/reverb.pid';
$logFile = $projectPath . '/storage/logs/reverb-monitor.log';

// Change to project directory
chdir($projectPath);

/**
 * Check if Reverb is running
 */
function isReverbRunning($pidFile) {
    if (!file_exists($pidFile)) {
        return false;
    }
    
    $pid = trim(file_get_contents($pidFile));
    
    if (!$pid) {
        return false;
    }
    
    // Check if process is running
    $result = shell_exec("ps -p $pid");
    
    if (strpos($result, $pid) !== false) {
        return true;
    } else {
        // Clean up stale PID file
        unlink($pidFile);
        return false;
    }
}

/**
 * Start Reverb process
 */
function startReverb($projectPath, $pidFile, $logFile) {
    $command = "cd $projectPath && nohup php artisan reverb:start >> $logFile 2>&1 & echo $!";
    $pid = shell_exec($command);
    
    if ($pid) {
        file_put_contents($pidFile, trim($pid));
        $message = date('Y-m-d H:i:s') . " - Reverb started with PID: " . trim($pid) . "\n";
        file_put_contents($logFile, $message, FILE_APPEND | LOCK_EX);
        return true;
    }
    
    return false;
}

/**
 * Log message
 */
function logMessage($message, $logFile) {
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] $message\n";
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}

// Main monitoring logic
try {
    // Ensure log directory exists
    $logDir = dirname($logFile);
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    if (!isReverbRunning($pidFile)) {
        logMessage("Reverb not running, attempting to start...", $logFile);
        
        if (startReverb($projectPath, $pidFile, $logFile)) {
            logMessage("Reverb started successfully", $logFile);
            echo "Reverb started successfully\n";
        } else {
            logMessage("Failed to start Reverb", $logFile);
            echo "Failed to start Reverb\n";
        }
    } else {
        logMessage("Reverb is running normally", $logFile);
        echo "Reverb is running\n";
    }
    
} catch (Exception $e) {
    logMessage("Error: " . $e->getMessage(), $logFile);
    echo "Error: " . $e->getMessage() . "\n";
}
?>
