<?php
/**
 * WP Detector Functions
 */

// Add cache directory constant at the top
define('CACHE_DIR', __DIR__ . '/../cache');
define('CACHE_EXPIRY', 3600); // 1 hour cache expiry
define('DEFAULT_TIMEOUT', 10);
define('DEFAULT_CONNECT_TIMEOUT', 5);

// Ensure cache directory exists
if (!file_exists(CACHE_DIR)) {
    mkdir(CACHE_DIR, 0755, true);
}

// Function to format URL properly
function formatUrl($url) {
    // Remove any whitespace
    $url = trim($url);
    
    // Add http:// if no protocol specified
    if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
        $url = "http://" . $url;
    }
    
    // Parse the URL
    $parsed_url = parse_url($url);
    
    // Get the host
    $host = $parsed_url['host'];
    
    // Remove 'www.' if present
    if (substr($host, 0, 4) === 'www.') {
        $host = substr($host, 4);
    }
    
    // Reconstruct the base URL
    $scheme = isset($parsed_url['scheme']) ? $parsed_url['scheme'] : 'http';
    $base_url = $scheme . "://" . $host;
    
    // Add port if specified and not default
    if (isset($parsed_url['port'])) {
        $base_url .= ":" . $parsed_url['port'];
    }
    
    return $base_url;
}

// Improved cache helper functions with better error handling and security
function getCache($key) {
    try {
        if (empty($key)) {
            return null;
        }
        
        $cacheFile = CACHE_DIR . '/' . md5($key) . '.cache';
        
        if (!file_exists($cacheFile)) {
            return null;
        }
        
        if (time() - filemtime($cacheFile) >= CACHE_EXPIRY) {
            unlink($cacheFile); // Clean up expired cache
            return null;
        }
        
        $data = file_get_contents($cacheFile);
        if ($data === false) {
            error_log("Failed to read cache file: " . $cacheFile);
            return null;
        }
        
        $unserializedData = unserialize($data, ['allowed_classes' => false]);
        if ($unserializedData === false) {
            error_log("Failed to unserialize cache data for key: " . $key);
            unlink($cacheFile); // Clean up corrupted cache
            return null;
        }
        
        return $unserializedData;
    } catch (Exception $e) {
        error_log("Cache read error: " . $e->getMessage());
        return null;
    }
}

function setCache($key, $data) {
    try {
        if (empty($key)) {
            return false;
        }
        
        if (!is_dir(CACHE_DIR)) {
            if (!mkdir(CACHE_DIR, 0750, true)) {
                error_log("Failed to create cache directory: " . CACHE_DIR);
                return false;
            }
        }
        
        $cacheFile = CACHE_DIR . '/' . md5($key) . '.cache';
        $serializedData = serialize($data);
        
        if ($serializedData === false) {
            error_log("Failed to serialize data for key: " . $key);
            return false;
        }
        
        $result = file_put_contents($cacheFile, $serializedData, LOCK_EX);
        if ($result === false) {
            error_log("Failed to write cache file: " . $cacheFile);
            return false;
        }
        
        // Set restrictive permissions
        chmod($cacheFile, 0640);
        
        return true;
    } catch (Exception $e) {
        error_log("Cache write error: " . $e->getMessage());
        return false;
    }
}

// Optimized cURL initialization with better defaults
function initCurl($url) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => DEFAULT_TIMEOUT,
        CURLOPT_CONNECTTIMEOUT => DEFAULT_CONNECT_TIMEOUT,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_ENCODING => '',
        CURLOPT_HEADER => false,
        CURLOPT_FAILONERROR => true
    ]);
    return $ch;
}

// Optimize cURL initialization
function checkWordPressSite($url) {
    // Clean and format the URL
    $url = trim($url);
    
    if (empty($url)) {
        return ['error' => 'Please enter a URL'];
    }
    
    // Check cache first
    $cacheKey = 'site_' . md5($url);
    $cachedData = getCache($cacheKey);
    if ($cachedData !== null) {
        return $cachedData;
    }
    
    // Standardize URL format
    $url = preg_replace('#^[^:/.]*[:/]+#i', '', $url);
    if (!preg_match('~^(?:f|ht)tps?://~i', $url)) {
        $url = 'http://' . $url;
    }

    // Pre-compile regex patterns for better performance
    static $patterns = [
        'meta' => '/<meta[^>]+(?:generator|powered-by)[^>]+(?:WordPress|WP)[^>]*>/i',
        'includes' => '/\/wp-includes\/[^"\']*\.(js|css)/i',
        'version' => '/<meta[^>]+generator[^>]+WordPress\s+([0-9.]+)/i',
        'theme' => '/wp-content\/themes\/([^\/"\'\s]+)/i',
        'plugin' => '/wp-content\/plugins\/([^\/"\'\s]+)/i'
    ];

    try {
        // Initialize cURL with optimized settings
        $ch = initCurl($url);
        
        // Execute request with error handling
        $startTime = microtime(true);
        $content = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        // Gather performance metrics
        $performance = [
            'load_time' => microtime(true) - $startTime,
            'size' => curl_getinfo($ch, CURLINFO_SIZE_DOWNLOAD),
            'speed' => curl_getinfo($ch, CURLINFO_SPEED_DOWNLOAD)
        ];
        
        if ($content === false) {
            $error = curl_error($ch);
            curl_close($ch);
            return ['error' => 'Failed to connect to the website: ' . $error];
        }
        
        curl_close($ch);

        if ($httpCode !== 200) {
            return ['error' => 'Failed to access the website (HTTP ' . $httpCode . ')'];
        }

        $result = [
            'isWordPress' => false,
            'theme' => '',
            'plugins' => [],
            'version' => '',
            'security' => [
                'version_exposed' => false,
                'readme_exposed' => false,
                'directory_listing' => false,
                'debug_log_accessible' => false
            ],
            'performance' => $performance
        ];

        // Quick check for WordPress paths
        $wpPaths = ['/wp-content/', '/wp-includes/', '/wp-admin/'];
        foreach ($wpPaths as $path) {
            if (stripos($content, $path) !== false) {
                $result['isWordPress'] = true;
                break;
            }
        }

        // Secondary check with meta and includes patterns
        if (!$result['isWordPress'] && (
            preg_match($patterns['meta'], $content) || 
            preg_match($patterns['includes'], $content)
        )) {
            $result['isWordPress'] = true;
        }

        if (!$result['isWordPress']) {
            return ['error' => 'This does not appear to be a WordPress site'];
        }

        // Extract version information
        if (preg_match($patterns['version'], $content, $matches)) {
            $result['version'] = $matches[1];
            $result['security']['version_exposed'] = true;
        } elseif (preg_match('/ver=([0-9.]+)/i', $content, $matches)) {
            $result['version'] = $matches[1];
        }

        // Extract theme and plugins efficiently
        if (preg_match($patterns['theme'], $content, $matches)) {
            $result['theme'] = $matches[1];
        }

        if (preg_match_all($patterns['plugin'], $content, $matches)) {
            $result['plugins'] = array_values(array_unique($matches[1]));
        }

        // Perform security checks
        $result['security'] = performSecurityChecks($url, $content);
        
        // Cache valid results
        setCache($cacheKey, $result);
        
        return $result;
    } catch (Exception $e) {
        return ['error' => 'An error occurred: ' . $e->getMessage()];
    }
}

function performSecurityChecks($url, $content) {
    $security = [
        'version_exposed' => false,
        'readme_exposed' => false,
        'directory_listing' => false,
        'debug_log_accessible' => false
    ];

    // Check version exposure in meta tags
    static $versionPattern = '/<meta[^>]+generator[^>]+WordPress\s+([0-9.]+)/i';
    $security['version_exposed'] = preg_match($versionPattern, $content) === 1;

    // Prepare URLs for parallel checking
    $baseUrl = rtrim($url, '/');
    $urls = [
        'readme' => $baseUrl . '/readme.html',
        'debug' => $baseUrl . '/wp-content/debug.log',
        'wp_content' => $baseUrl . '/wp-content/'
    ];

    // Initialize parallel curl handles
    $mh = curl_multi_init();
    $handles = [];
    
    foreach ($urls as $key => $checkUrl) {
        $handles[$key] = initCurl($checkUrl);
        curl_setopt($handles[$key], CURLOPT_NOBODY, true);
        curl_multi_add_handle($mh, $handles[$key]);
    }

    // Execute parallel requests
    $running = null;
    do {
        curl_multi_exec($mh, $running);
    } while ($running);

    // Process results
    foreach ($handles as $key => $ch) {
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        switch ($key) {
            case 'readme':
                $security['readme_exposed'] = ($httpCode == 200);
                break;
            case 'debug':
                $security['debug_log_accessible'] = ($httpCode == 200);
                break;
            case 'wp_content':
                if ($httpCode == 200) {
                    $response = curl_multi_getcontent($ch);
                    $security['directory_listing'] = (
                        stripos($response, 'Index of') !== false || 
                        stripos($response, '<title>Index of') !== false
                    );
                }
                break;
        }
        
        curl_multi_remove_handle($mh, $ch);
        curl_close($ch);
    }

    curl_multi_close($mh);
    
    return $security;
}

function getPluginInfo($plugin) {
    // Check cache first
    $cacheKey = 'plugin_' . $plugin;
    $cachedData = getCache($cacheKey);
    if ($cachedData !== null) {
        return $cachedData;
    }
    
    $plugin = basename($plugin);
    $api_url = "https://api.wordpress.org/plugins/info/1.0/{$plugin}.json";
    
    $ch = initCurl($api_url);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200 && $response) {
        $data = json_decode($response, true);
        if ($data) {
            $result = [
                'name' => $data['name'] ?? '',
                'version' => $data['version'] ?? '',
                'description' => $data['short_description'] ?? '',
                'rating' => $data['rating'] ?? 0,
                'num_ratings' => $data['num_ratings'] ?? 0,
                'active_installs' => $data['active_installs'] ?? 0,
                'last_updated' => $data['last_updated'] ?? '',
                'author' => $data['author'] ?? '',
                'url' => $data['homepage'] ?? ''
            ];
            setCache($cacheKey, $result);
            return $result;
        }
    }
    
    return null;
}

function getWordPressOrgLinks($name, $type = 'plugin') {
    // Check cache first
    $cacheKey = "{$type}_links_" . $name;
    $cachedData = getCache($cacheKey);
    if ($cachedData !== null) {
        return $cachedData;
    }
    
    $name = strtolower(trim($name));
    $api_url = ($type === 'plugin') 
        ? "https://api.wordpress.org/plugins/info/1.2/?action=plugin_information&request[slug]={$name}"
        : "https://api.wordpress.org/themes/info/1.2/?action=theme_information&request[slug]={$name}";
    
    $ch = initCurl($api_url);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200 && $response) {
        $data = json_decode($response, true);
        if ($data && !isset($data['error'])) {
            $result = [
                'url' => $type === 'plugin' 
                    ? "https://wordpress.org/plugins/" . $name . "/"
                    : "https://wordpress.org/themes/" . $name . "/",
                'description' => $data['short_description'] ?? $data['description'] ?? '',
                'rating' => $data['rating'] ?? 0,
                'active_installs' => $data['active_installs'] ?? ($data['downloaded'] ?? 0),
                'last_updated' => $data['last_updated'] ?? '',
                'screenshot_url' => $type === 'theme' ? ($data['screenshot_url'] ?? '') : '',
                'version' => $data['version'] ?? ''
            ];
            setCache($cacheKey, $result);
            return $result;
        }
    }
    
    return null;
}

function getThemeInfo($url, $theme) {
    if (empty($url) || empty($theme)) {
        return [
            'Theme Name' => 'Unknown',
            'Theme URI' => '',
            'Description' => '',
            'Author' => '',
            'Author URI' => '',
            'Version' => '',
            'License' => '',
            'Screenshot' => '',
            'Parent Theme' => null,
            'Is Child Theme' => false
        ];
    }

    // Clean the URL
    $url = rtrim($url, '/');
    
    // Try to get theme's style.css content
    $styleUrl = $url . '/wp-content/themes/' . $theme . '/style.css';
    
    $ch = initCurl($styleUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
    $content = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $themeInfo = [
        'Theme Name' => 'Unknown',
        'Theme URI' => '',
        'Description' => '',
        'Author' => '',
        'Author URI' => '',
        'Version' => '',
        'License' => '',
        'Screenshot' => '',
        'Parent Theme' => null,
        'Is Child Theme' => false
    ];
    
    if ($httpCode === 200 && $content) {
        // Extract theme information from style.css
        $headers = [
            'Theme Name',
            'Theme URI',
            'Description',
            'Author',
            'Author URI',
            'Version',
            'License',
            'License URI',
            'Text Domain',
            'Tags',
            'Template'  // This is the parent theme identifier
        ];
        
        foreach ($headers as $header) {
            if (preg_match('/' . $header . '\s*:\s*(.+)$/mi', $content, $matches)) {
                $themeInfo[$header] = trim($matches[1]);
            }
        }

        // Check if this is a child theme
        if (!empty($themeInfo['Template'])) {
            $themeInfo['Is Child Theme'] = true;
            $themeInfo['Parent Theme'] = $themeInfo['Template'];
            
            // Get parent theme information
            $parentThemeInfo = getThemeInfo($url, $themeInfo['Template']);
            if ($parentThemeInfo['Theme Name'] !== 'Unknown') {
                $themeInfo['Parent Theme Info'] = $parentThemeInfo;
            }
        }
        
        // Try to get screenshot with multiple format support
        $screenshotFormats = ['png', 'jpg', 'jpeg'];
        foreach ($screenshotFormats as $format) {
            $screenshotUrl = $url . '/wp-content/themes/' . $theme . '/screenshot.' . $format;
            $ch = initCurl($screenshotUrl);
            curl_setopt($ch, CURLOPT_NOBODY, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            if ($httpCode === 200) {
                $themeInfo['Screenshot'] = $screenshotUrl;
                break;
            }
        }
    }
    
    return $themeInfo;
}

function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    return round($bytes / (1024 ** $pow), $precision) . ' ' . $units[$pow];
}

function sanitize_plugin_name($name) {
    $name = strtolower(trim($name));
    $name = preg_replace('/[^a-z0-9-]/', '-', $name);
    $name = preg_replace('/-+/', '-', $name);
    return trim($name, '-');
}