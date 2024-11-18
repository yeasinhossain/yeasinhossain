<?php
require_once 'includes/functions.php';

// Check if URL is provided
if (!isset($_POST['url'])) {
    echo '<div class="error-message">Error: No URL provided</div>';
    exit;
}

$url = $_POST['url'];
$result = checkWordPressSite($url);

if (isset($result['error'])) {
    echo '<div class="error-message">Error: ' . htmlspecialchars($result['error']) . '</div>';
    exit;
}

// Display results
if ($result['isWordPress']) {
    echo '<div class="success-message">✅ This is a WordPress site!</div>';

    // Security Information
    echo '<div class="security-section">';
    echo '<h3>Security Status</h3>';
    echo '<table class="security-details">';
    
    $securityItems = [
        'version_exposed' => 'WordPress Version Exposed',
        'readme_exposed' => 'Readme File Exposed',
        'debug_log_accessible' => 'Debug Log Accessible',
        'directory_listing' => 'Directory Listing Enabled'
    ];

    foreach ($securityItems as $key => $label) {
        $status = $result['security'][$key];
        echo '<tr>';
        echo '<td>' . $label . '</td>';
        echo '<td class="' . ($status ? 'warning' : 'good') . '">';
        echo $status ? '⚠️ Yes' : '✅ No';
        echo '</td>';
        echo '</tr>';
    }
    
    echo '</table>';

    // Security Recommendations
    $hasIssues = array_filter($result['security']);
    if (!empty($hasIssues)) {
        echo '<div class="security-recommendations">';
        echo '<h4>Security Recommendations</h4>';
        echo '<ul>';
        
        $recommendations = [
            'version_exposed' => 'Hide WordPress version number to prevent potential attackers from targeting known vulnerabilities.',
            'readme_exposed' => 'Remove or restrict access to the readme.html file to prevent information disclosure.',
            'debug_log_accessible' => 'Restrict access to the debug.log file to prevent sensitive information disclosure.',
            'directory_listing' => 'Disable directory listing to prevent exposure of file structure.'
        ];

        foreach ($result['security'] as $key => $value) {
            if ($value && isset($recommendations[$key])) {
                echo '<li>' . $recommendations[$key] . '</li>';
            }
        }
        
        echo '</ul>';
        echo '</div>';
    }
    echo '</div>';

    // Performance Information
    echo '<div class="performance-section">';
    echo '<h3>Performance Metrics</h3>';
    echo '<table class="performance-details">';
    
    echo '<tr><td>Page Load Time</td><td>' . number_format($result['performance']['load_time'], 3) . ' seconds</td></tr>';
    echo '<tr><td>Page Size</td><td>' . formatBytes($result['performance']['size']) . '</td></tr>';
    
    echo '</table>';

    if ($result['performance']['load_time'] > 2 || $result['performance']['size'] > 1000000) {
        echo '<div class="performance-recommendations">';
        echo '<h4>Performance Recommendations</h4>';
        echo '<ul>';
        if ($result['performance']['load_time'] > 2) {
            echo '<li>Page load time is higher than recommended (2 seconds). Consider optimizing your site.</li>';
        }
        if ($result['performance']['size'] > 1000000) {
            echo '<li>Page size is quite large. Consider implementing compression and optimization.</li>';
        }
        echo '</ul>';
        echo '</div>';
    }
    echo '</div>';

    // WordPress Version Information
    if (!empty($result['version'])) {
        echo '<div class="wordpress-section">';
        echo '<h3>WordPress Information</h3>';
        echo '<table class="wordpress-details">';
        echo '<tr><td>Version</td><td>' . htmlspecialchars($result['version']);
        if (version_compare($result['version'], '5.0', '<')) {
            echo ' <span class="warning">⚠️ This version might be outdated</span>';
        }
        echo '</td></tr>';
        echo '</table>';
        echo '</div>';
    }

    // Theme Information
    if (!empty($result['theme'])) {
        echo '<div class="theme-section">';
        echo '<h3>Active Theme</h3>';
        $themeInfo = getThemeInfo($url, $result['theme']);
        
        if (!empty($themeInfo)) {
            echo '<table class="theme-details">';
            foreach ($themeInfo as $key => $value) {
                if ($key !== 'Parent Theme Info' && $key !== 'Is Child Theme') {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($key) . '</td>';
                    echo '<td>' . htmlspecialchars($value) . '</td>';
                    echo '</tr>';
                }
            }
            echo '</table>';

            if ($themeInfo['Is Child Theme'] && !empty($themeInfo['Parent Theme Info'])) {
                echo '<h3>Parent Theme Details</h3>';
                echo '<table class="theme-details">';
                foreach ($themeInfo['Parent Theme Info'] as $key => $value) {
                    if ($key !== 'Parent Theme Info' && $key !== 'Is Child Theme') {
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($key) . '</td>';
                        echo '<td>' . htmlspecialchars($value) . '</td>';
                        echo '</tr>';
                    }
                }
                echo '</table>';
            }
        }
        echo '</div>';
    }

    // Plugin Information
    if (!empty($result['plugins'])) {
        echo '<div class="plugins-section">';
        echo '<h3>Detected Plugins</h3>';
        echo '<div class="plugins-grid">';
        foreach ($result['plugins'] as $index => $plugin) {
            $pluginInfo = getPluginInfo($plugin);
            echo '<div class="plugin-item">';
            echo '<div class="plugin-number">' . ($index + 1) . '</div>';
            echo '<h4>' . htmlspecialchars($plugin) . '</h4>';
            if ($pluginInfo) {
                echo '<div class="plugin-details">';
                echo '<p>' . htmlspecialchars($pluginInfo['short_description'] ?? '') . '</p>';
                if (isset($pluginInfo['version'])) {
                    echo '<p>Version: ' . htmlspecialchars($pluginInfo['version']) . '</p>';
                }
                echo '</div>';
            }
            echo '</div>';
        }
        echo '</div>';
        echo '</div>';
    }
} else {
    echo '<div class="error-message">❌ This does not appear to be a WordPress site.</div>';
}