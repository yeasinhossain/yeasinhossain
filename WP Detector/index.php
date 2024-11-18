<?php
require_once 'includes/functions.php';

// Initialize variables
$url = '';
$result = null;
$input_url = '';

// Start session at the beginning
session_start();

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST["url"])) {
    $input_url = trim($_POST["url"]);
    $url = formatUrl($input_url);
    $result = checkWordPressSite($url);
    
    // Store the complete result in session
    $_SESSION['wp_result'] = $result;
    $_SESSION['wp_url'] = $url;
    $_SESSION['input_url'] = $input_url;
    
    // Redirect to prevent form resubmission
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit();
}

// Check for stored result
if (isset($_SESSION['wp_result'])) {
    $result = $_SESSION['wp_result'];
    $url = $_SESSION['wp_url'];
    $input_url = $_SESSION['input_url'];
    
    // Clear the session data
    unset($_SESSION['wp_result']);
    unset($_SESSION['wp_url']);
    unset($_SESSION['input_url']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WordPress Theme & Plugin Detector | Find Any WP Theme or Plugin Instantly</title>
    <meta name="description" content="Free WordPress Theme and Plugin Detector - Instantly analyze and identify themes, plugins, and technologies used by any WordPress website. No registration required.">
    <meta name="keywords" content="WordPress theme detector, WordPress plugin finder, what theme is this, WP theme identifier, detect WordPress plugins, free WordPress analyzer, website theme checker, WordPress site scanner">
    <link rel="canonical" href="https://yeasin.me/wordpress-theme-plugin-detector">

    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="WordPress Theme & Plugin Detector | Find Any WP Theme or Plugin">
    <meta property="og:description" content="Free tool to instantly identify WordPress themes, plugins, and technologies used by any website. No registration required.">
    <meta property="og:url" content="https://yeasin.me/wordpress-theme-plugin-detector">
    <meta property="og:type" content="website">
    <meta property="og:image" content="https://yeasin.me/wp-content/uploads/2024/11/wp-detector-thumbnail.jpg">
    <meta property="og:image:alt" content="WordPress Theme & Plugin Detector Tool">
    <meta property="og:site_name" content="Yeasin Hossain">

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:creator" content="@meetyeasin">
    <meta name="twitter:title" content="WordPress Theme & Plugin Detector | Find Any WP Theme or Plugin">
    <meta name="twitter:description" content="Free tool to instantly identify WordPress themes, plugins, and technologies used by any website. No registration required.">
    <meta name="twitter:image" content="https://yeasin.me/wp-content/uploads/2024/11/wp-detector-thumbnail.jpg">
    <meta name="twitter:image:alt" content="WordPress Theme & Plugin Detector Tool">

    <!-- Additional Meta Tags -->
    <meta name="author" content="Yeasin Hossain">
    <meta name="robots" content="index, follow">
    <meta name="language" content="English">

    <!-- Favicon -->
    <link rel="icon" href="https://yeasin.me/wp-content/uploads/2024/05/fav.webp" type="image/webp">
    <link rel="apple-touch-icon" href="https://yeasin.me/wp-content/uploads/2024/05/fav.webp">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/your-font-awesome-kit.js" crossorigin="anonymous"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- Structured Data (JSON-LD) -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebApplication",
        "name": "WordPress Theme & Plugin Detector",
        "description": "A free online tool to instantly identify themes, plugins, and technologies used by any WordPress website.",
        "applicationCategory": "WebApplication",
        "operatingSystem": "All",
        "url": "https://yeasin.me/wordpress-theme-plugin-detector",
        "author": {
            "@type": "Person",
            "name": "Yeasin Hossain",
            "url": "https://yeasin.me",
            "sameAs": [
                "https://twitter.com/meetyeasin",
                "https://github.com/yeasinhossain"
            ]
        },
        "offers": {
            "@type": "Offer",
            "price": "0.00",
            "priceCurrency": "USD"
        },
        "featureList": [
            "WordPress Theme Detection",
            "Plugin Identification",
            "Real-time Analysis",
            "No Registration Required"
        ],
        "screenshot": "https://yeasin.me/wp-content/uploads/2024/11/wp-detector-thumbnail.jpg",
        "browserRequirements": "Requires JavaScript. Works in all modern browsers."
    }
    </script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#4F46E5',
                        secondary: '#111827',
                        success: '#10B981',
                        warning: '#F59E0B',
                        danger: '#EF4444',
                    }
                }
            }
        }
    </script>
    <script>
    function handleSubmit(form) {
        if (form.submitted) {
            return false;
        }
        form.submitted = true;
        
        // Add a small delay to allow the form to submit
        setTimeout(function() {
            form.submitted = false;
        }, 1000);
        
        return true;
    }

    // Reset form.submitted on page load
    window.onload = function() {
        document.getElementById('analyzeForm').submitted = false;
    }

    // Reset form.submitted when user changes the URL
    document.getElementById('siteUrl').addEventListener('input', function() {
        document.getElementById('analyzeForm').submitted = false;
    });
    </script>
    <style type="text/tailwindcss">
        @layer components {
            .btn-primary {
                @apply bg-primary text-white font-semibold py-3 px-6 rounded-lg hover:bg-indigo-600 transition duration-300 ease-in-out transform hover:-translate-y-1;
            }
            .input-primary {
                @apply w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent outline-none transition duration-300;
            }
            .result-card {
                @apply bg-white rounded-lg shadow-md p-6 mb-6;
            }
            .status-badge {
                @apply px-3 py-1 rounded-full text-sm font-semibold;
            }
            .status-success {
                @apply bg-green-100 text-green-800;
            }
            .status-warning {
                @apply bg-yellow-100 text-yellow-800;
            }
            .status-danger {
                @apply bg-red-100 text-red-800;
            }
        }
    </style>
    <style>
        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.9);
            z-index: 1000;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }
        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #4F46E5;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="bg-gray-100 dark:bg-gray-900">
    <header class="bg-white dark:bg-gray-900 border-b dark:border-gray-700 fixed w-full top-0 z-50">
        <nav class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-2">
            <a href="https://yeasin.me/wordpress-theme-plugin-detector/" class="flex items-center space-x-3 rtl:space-x-reverse">
                <i class="fas fa-bolt text-primary text-2xl"></i>
                <span class="self-center text-xl font-semibold whitespace-nowrap dark:text-white">WP Detector</span>
            </a>
            <div class="flex md:order-2 space-x-3 md:space-x-0 rtl:space-x-reverse">
                <a href="https://yeasin.me/free-resources/" target="_blank" rel="noopener">
                    <button type="button" class="text-white bg-primary hover:bg-primary-600 focus:ring-4 focus:outline-none focus:ring-primary-300 font-medium rounded-lg text-sm px-4 py-2 text-center dark:bg-primary dark:hover:bg-primary-700 dark:focus:ring-primary-800">Free Resources</button>
                </a>
                <button id="mobile-menu-button" type="button" class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600" aria-controls="navbar-sticky" aria-expanded="false">
                    <span class="sr-only">Open main menu</span>
                    <i class="fas fa-bars w-5 h-5"></i>
                </button>
            </div>
            <div id="mobile-menu" class="items-center justify-between hidden w-full md:flex md:w-auto md:order-1">
                <ul class="flex flex-col p-4 md:p-0 mt-4 font-medium border border-gray-100 rounded-lg bg-gray-50 md:space-x-8 rtl:space-x-reverse md:flex-row md:mt-0 md:border-0 md:bg-white dark:bg-gray-900 dark:border-gray-700">
                    <li>
                        <a href="https://yeasin.me/wordpress-theme-plugin-detector/" class="block py-2 px-3 text-primary bg-transparent rounded md:p-0" aria-current="page">Detector</a>
                    </li>
                    <li>
                        <a href="https://yeasin.me/wordpress-theme-plugin-detector/top-themes/" class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-primary md:p-0 md:dark:hover:text-primary dark:text-white dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent dark:border-gray-700">Top Themes</a>
                    </li>
                    <li>
                        <a href="https://yeasin.me/wordpress-theme-plugin-detector/top-plugins/" class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-primary md:p-0 md:dark:hover:text-primary dark:text-white dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent dark:border-gray-700">Top Plugins</a>
                    </li>
                    <li>
                        <a href="#" class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:hover:text-primary md:p-0 md:dark:hover:text-primary dark:text-white dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent dark:border-gray-700">Top Providers</a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
    <div class="pt-20">
    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" style="display: none;">
        <div class="bg-white p-6 rounded-lg shadow-xl flex items-center">
            <div class="animate-spin rounded-full h-8 w-8 border-4 border-primary border-t-transparent mr-4"></div>
            <div class="flex flex-col">
                <p class="text-gray-700">Analyzing website...</p>
                <p class="text-sm text-gray-500">This may take a few moments</p>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8 max-w-6xl">
        <!-- Header Section -->
        <div class="text-center mb-10">
            <a href="https://yeasin.me/wordpress-theme-plugin-detector/" class="inline-block transform hover:scale-105 transition-transform duration-300" title="WordPress Theme & Plugin Detector">
                <i class="fab fa-wordpress text-primary text-7xl mb-2 animate-pulse"></i>
            </a>
            
            <?php if (!isset($result)): ?>
                <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6 leading-tight">
                    <span class="block">Detect WordPress Themes</span>
                    <span class="block">or Detect WordPress Plugins</span>
                    <span class="text-primary">100% Free & Accurate!</span>
                </h1>
                <div class="max-w-4xl mx-auto space-y-4">
                    <h2 class="text-gray-600 text-xl max-w-4xl mx-auto leading-relaxed">
                        Have you ever encountered a captivating WordPress website and wondered about the theme and plugins powering its design and functionality? You're not alone. Many seek to uncover the tools behind impressive sites. Our <strong class="text-primary">Instant WordPress Theme & Plugin Detector</strong> simplifies this process, providing detailed insights into the themes and plugins used by any WordPress site. Discover the building blocks of your favorite websites effortlessly.
                    </h2>
                </div>
            <?php endif; ?>

            <?php if (isset($result)): ?>
                <?php if (isset($result['isWordPress']) && $result['isWordPress']): ?>
                    <div class="space-y-4">
                        <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4 leading-tight">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            WordPress Site Detected!
                        </h2>
                        <p class="text-xl font-medium text-gray-600">
                            We've found WordPress! Check out the detailed analysis below or analyze another website.
                        </p>
                    </div>
                <?php else: ?>
                    <div class="space-y-4">
                        <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4 leading-tight">
                            <i class="fas fa-times-circle text-red-500 mr-2"></i>
                            Not a WordPress Site
                        </h2>
                        <p class="text-xl font-medium text-gray-600">
                            This website doesn't appear to be using WordPress. Try analyzing a different website!
                        </p>
                        <div class="mt-4 text-gray-500">
                            <p>Need help? <a href="https://yeasin.me/contact/" target="_blank" rel="noopener" class="text-primary hover:underline">Contact Support</a></p>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <!-- Main Form -->
        <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
            <form id="analyzeForm" method="post" class="flex flex-col md:flex-row items-center justify-center gap-4 py-6" onsubmit="return handleSubmit(this);">
            <div class="flex-grow w-full md:max-w-3xl relative">
                <i class="fas fa-link absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                <input type="text" 
                    name="url" 
                    id="siteUrl"
                    placeholder="Enter website URL..." 
                    class="w-full px-6 py-4 pl-12 border-2 border-gray-200 rounded-xl text-lg focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200 hover:border-primary"
                    required
                    value="<?php echo htmlspecialchars($input_url); ?>">
            </div>
                <button type="submit" 
                        class="w-full md:w-auto bg-primary text-white px-8 py-4 rounded-xl hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary focus:ring-offset-2 whitespace-nowrap flex items-center justify-center text-lg font-semibold transition-all duration-200 hover:shadow-lg">
                    <i class="fas fa-search mr-3 text-xl"></i>
                    Analyze Website
                </button>
            </form>
<script>
    // Get the input element
    const urlInput = document.getElementById('siteUrl');
    
    // URL validation function with TLD check
    function isValidUrl(url) {
        // Regular expression to validate URL with optional protocol and www
        const urlPattern = /^(https?:\/\/)?(www\.)?([a-zA-Z0-9-]+\.)+[a-zA-Z]{2,}(\/[a-zA-Z0-9-._~:/?#[\]@!$&'()*+,;=]*)?$/;
        return urlPattern.test(url);
    }

    // Add input event listener for real-time validation
    urlInput.addEventListener('input', function() {
        const value = this.value.trim();
        
        if (value && !isValidUrl(value)) {
            this.setCustomValidity('Please enter a valid website URL (e.g., example.com or www.example.com)');
            this.classList.add('border-red-500');
            this.classList.remove('border-gray-200', 'hover:border-primary');
        } else {
            this.setCustomValidity('');
            this.classList.remove('border-red-500');
            this.classList.add('border-gray-200', 'hover:border-primary');
        }
    });

    // Form submit validation
    function handleSubmit(form) {
        const input = form.querySelector('#siteUrl');
        const value = input.value.trim();
        
        if (!value) {
            input.setCustomValidity('Please enter a website URL');
            input.reportValidity();
            return false;
        }
        
        if (!isValidUrl(value)) {
            input.setCustomValidity('Please enter a valid website URL (e.g., example.com or www.example.com)');
            input.reportValidity();
            return false;
        }
        
        return true;
    }
</script>
            <?php if (!isset($result)): ?>
            <div class="flex items-center justify-center mt-4 gap-3 text-gray-600">
                <i class="fas fa-lightbulb text-primary text-xl"></i>
                <p class="text-lg font-medium">Discover the tools behind successful WordPress websites to optimize your own!</p>
            </div>
            <?php endif; ?>


            <?php if (isset($result)): ?>
                <!-- WordPress Detection Notice -->


                <?php if (isset($result['isWordPress']) && $result['isWordPress']): ?>
                <!-- 1. WordPress Information -->
                <div class="mt-10 mb-16">
                    <h2 class="text-2xl font-bold mb-6 flex items-center">
                        <i class="fab fa-wordpress text-primary mr-3"></i>
                        WordPress Information
                    </h2>
                    <div class="bg-white shadow rounded-lg overflow-hidden mb-6">
                        <table class="min-w-full">
                            <tbody class="divide-y divide-gray-200">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap bg-gray-50 text-sm font-medium text-gray-500"><i class="fab fa-wordpress text-primary mr-3"></i>WordPress Version</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo !empty($result['version']) ? htmlspecialchars($result['version']) : 'Unknown/Hidden'; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap bg-gray-50 text-sm font-medium text-gray-500"><i class="fas fa-paint-brush mr-2 text-primary"></i>Active Theme</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo !empty($result['theme']) ? htmlspecialchars(basename($result['theme'])) : 'Unknown'; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap bg-gray-50 text-sm font-medium text-gray-500"><i class="fas fa-plug mr-2 text-primary"></i>Active Plugins</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo !empty($result['plugins']) ? count($result['plugins']) : '0'; ?> plugins detected
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Theme Information -->
                    <div class="mt-6 mb-10">
                        <h3 class="text-xl font-semibold mb-4"><i class="fas fa-paint-brush mr-2 text-primary"></i> Active Theme Details</h3>
                        <?php if (!empty($result['theme'])): ?>
                            <div class="result-card">
                                <?php 
                                $themeInfo = getThemeInfo($url, $result['theme'] ?? '');
                                $wpThemeData = getWordPressOrgLinks($result['theme'] ?? '', 'theme');
                                ?>
                                
                                <div class="theme-info space-y-6">
                                    <!-- Theme Details Table -->
                                    <div class="overflow-x-auto shadow rounded-lg">
                                        <table class="w-full theme-details">
                                            <tbody class="divide-y divide-gray-200">
                                                <!-- Theme Name -->
                                                <tr>
                                                    <td class="py-2 px-4 font-medium text-gray-600 bg-gray-50 w-1/4">Theme Name</td>
                                                    <td class="py-2 px-4">
                                                        <?php echo htmlspecialchars($themeInfo['Theme Name'] ?? ''); ?>
                                                        <?php if ($wpThemeData): ?>
                                                            <a href="<?php echo htmlspecialchars($wpThemeData['url']); ?>" 
                                                                target="_blank" 
                                                                class="ml-2 text-primary hover:underline">
                                                                <i class="fab fa-wordpress"></i>View on WordPress.org
                                                            </a>
                                                        <?php else: ?>
                                                            <span class="ml-2 text-sm text-gray-500">(Custom/Premium Theme)</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>

                                                <!-- Theme Version -->
                                                <?php if (!empty($themeInfo['Version'])): ?>
                                                <tr>
                                                    <td class="py-2 px-4 font-medium text-gray-600 bg-gray-50">Version</td>
                                                    <td class="py-2 px-4"><?php echo htmlspecialchars($themeInfo['Version']); ?></td>
                                                </tr>
                                                <?php endif; ?>

                                                <!-- Theme Author -->
                                                <?php if (!empty($themeInfo['Author'])): ?>
                                                <tr>
                                                    <td class="py-2 px-4 font-medium text-gray-600 bg-gray-50">Author</td>
                                                    <td class="py-2 px-4">
                                                        <?php if (!empty($themeInfo['Author URI'])): ?>
                                                            <a href="<?php echo htmlspecialchars($themeInfo['Author URI']); ?>" 
                                                                target="_blank" 
                                                                class="text-primary hover:underline">
                                                                <?php echo htmlspecialchars($themeInfo['Author']); ?>
                                                            </a>
                                                        <?php else: ?>
                                                            <?php echo htmlspecialchars($themeInfo['Author']); ?>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                                <?php endif; ?>

                                                <!-- Theme Description -->
                                                <?php if (!empty($themeInfo['Description'])): ?>
                                                <tr>
                                                    <td class="py-2 px-4 font-medium text-gray-600 bg-gray-50">Description</td>
                                                    <td class="py-2 px-4"><?php echo htmlspecialchars($themeInfo['Description']); ?></td>
                                                </tr>
                                                <?php endif; ?>

                                                <!-- Theme License -->
                                                <?php if (!empty($themeInfo['License'])): ?>
                                                <tr>
                                                    <td class="py-2 px-4 font-medium text-gray-600 bg-gray-50">License</td>
                                                    <td class="py-2 px-4"><?php echo htmlspecialchars($themeInfo['License']); ?></td>
                                                </tr>
                                                <?php endif; ?>
                                                <!-- Theme Screenshot -->
                                                <?php if (!empty($themeInfo['Screenshot'])): ?>
                                                <tr>
                                                    <td class="py-2 px-4 font-medium text-gray-600 bg-gray-50">Theme Screenshot</td>
                                                    <td class="py-2 px-4">
                                                        <a href="<?php echo htmlspecialchars($themeInfo['Screenshot']); ?>" target="_blank" class="block">
                                                            <img style="width: 200px;" 
                                                                src="<?php echo htmlspecialchars($themeInfo['Screenshot']); ?>" 
                                                                alt="Theme Screenshot" 
                                                                class="h-auto rounded-xl border border-gray-200 shadow-md hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- WordPress.org Theme Link -->
                                    <?php if ($wpThemeData): ?>
                                        <div class="p-4 bg-gray-50 shadow rounded-lg theme-item mb-6 hover:shadow-md transition-shadow duration-300">
                                            <div class="mt-4">
                                                <h4 class="text-lg font-semibold mb-2">
                                                    <a href="<?php echo htmlspecialchars($wpThemeData['url']); ?>" 
                                                        target="_blank" 
                                                        class="text-primary hover:underline flex items-center">
                                                        <?php echo htmlspecialchars(basename($result['theme'])) . '/' . htmlspecialchars($themeInfo['Theme Name']); ?>
                                                        <svg class="external-link ml-1" width="12" height="12" viewBox="0 0 24 24">
                                                            <path fill="currentColor" d="M21 13v10h-21v-19h12v2h-10v15h17v-8h2z"/>
                                                            <path fill="currentColor" d="M13 12l5.7-5.7-1.4-1.4 3.7-3.9 4 4-3.9 3.7-1.4-1.4-5.7 5.7z"/>
                                                        </svg>
                                                    </a>
                                                </h4>
                                                
                                                <?php if (!empty($themeInfo['Description'])): ?>
                                                    <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($themeInfo['Description']); ?></p>
                                                <?php endif; ?>
                                                
                                                <div class="theme-meta flex flex-wrap items-center gap-4 text-sm text-gray-600 mb-4">
                                                    <?php if (!empty($themeInfo['Version'])): ?>
                                                        <span class="version flex items-center">
                                                            <i class="fas fa-code-branch mr-1"></i>v<?php echo htmlspecialchars($themeInfo['Version']); ?>
                                                        </span>
                                                    <?php endif; ?>
                                                    
                                                    <?php if (!empty($wpThemeData['rating'])): ?>
                                                        <span class="rating flex items-center">
                                                            <i class="fas fa-star text-yellow-400 mr-1"></i><?php echo number_format($wpThemeData['rating'] / 20, 1); ?>/5
                                                        </span>
                                                    <?php endif; ?>
                                                    
                                                    <?php if (!empty($wpThemeData['active_installs'])): ?>
                                                        <span class="installs flex items-center">
                                                            <i class="fas fa-download mr-1"></i><?php echo number_format($wpThemeData['active_installs']); ?>+ active installs
                                                        </span>
                                                    <?php endif; ?>
                                                    
                                                    <?php if (!empty($wpThemeData['last_updated'])): ?>
                                                        <span class="updated flex items-center">
                                                            <i class="fas fa-clock mr-1"></i>Updated: <?php echo date('M j, Y', strtotime($wpThemeData['last_updated'])); ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                                
                                                <div class="flex space-x-3">
                                                    <a href="<?php echo htmlspecialchars($wpThemeData['url']); ?>" 
                                                        target="_blank" 
                                                        rel="noopener"
                                                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                                        <i class="fab fa-wordpress text-primary mr-2"></i> Download this Theme from WordPress.org
                                                    </a>
                                                    
                                                    <?php if (!empty($wpThemeData['download_link'])): ?>
                                                        <a href="<?php echo htmlspecialchars($wpThemeData['download_link']); ?>" 
                                                            target="_blank" 
                                                            rel="noopener"
                                                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                                            <i class="fas fa-download mr-2"></i> Download this Theme
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Non WordPress.org Theme Link -->
                                    <?php if (!$wpThemeData): ?>
                                        <div class="p-4 bg-gray-50 rounded-lg theme-item mb-6 hover:shadow-md transition-shadow duration-300">
                                            <div class="mt-4">
                                                <p class="text-gray-600 mb-4"><b><?php echo htmlspecialchars(basename($result['theme'])) . '/' . htmlspecialchars($themeInfo['Theme Name']); ?></b> not found on WordPress.org, this might be a custom or premium theme.</p>
                                                <div class="flex space-x-3">
                                                    <!-- Search on Google button -->
                                                    <a href="https://www.google.com/search?q=<?php echo urlencode($themeInfo['Theme Name'] . ' wordpress theme'); ?>" 
                                                    target="_blank" 
                                                    rel="noopener"
                                                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                                        <i class="fab fa-google mr-2"></i> Search on Google
                                                    </a>

                                                    <!-- Search on WordPress.org button -->
                                                    <a href="https://wordpress.org/themes/search/<?php echo urlencode($themeInfo['Theme Name'] ?? ''); ?>/" 
                                                    target="_blank" 
                                                    rel="noopener"
                                                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                                        <i class="fab fa-wordpress mr-2"></i> Search on WordPress.org
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($themeInfo['Is Child Theme']) && !empty($themeInfo['Parent Theme Info'])): ?>
                        <div class="mb-4 p-4 bg-blue-50 border-l-4 border-blue-500 text-blue-700">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-info-circle"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm">This is a Child Theme based on the Parent Theme below</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8">
                            <h3 class="text-xl font-semibold mb-4">
                                <i class="fas fa-code-branch mr-2 text-primary"></i> Parent Theme Details
                            </h3>
                            <div class="result-card">
                                <div class="theme-info space-y-6">
                                    <!-- Parent Theme Details Table -->
                                    <div class="overflow-x-auto shadow rounded-lg">
                                        <table class="w-full theme-details">
                                            <tbody class="divide-y divide-gray-200">
                                                <!-- Parent Theme Name -->
                                                <tr>
                                                    <td class="py-2 px-4 font-medium text-gray-600 bg-gray-50 w-1/4">Theme Name</td>
                                                    <td class="py-2 px-4">
                                                        <?php 
                                                        $parentThemeInfo = $themeInfo['Parent Theme Info'];
                                                        echo htmlspecialchars($parentThemeInfo['Theme Name'] ?? '');
                                                        $parentWpThemeData = getWordPressOrgLinks($themeInfo['Parent Theme'], 'theme');
                                                        if ($parentWpThemeData): 
                                                        ?>
                                                            <a href="<?php echo htmlspecialchars($parentWpThemeData['url']); ?>" 
                                                                target="_blank" 
                                                                class="text-primary hover:underline">
                                                                <i class="fab fa-wordpress"></i>View on WordPress.org
                                                            </a>
                                                        <?php else: ?>
                                                            <span class="ml-2 text-sm text-gray-500">(Custom/Premium Theme)</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>

                                                <!-- Parent Theme Version -->
                                                <?php if (!empty($parentThemeInfo['Version'])): ?>
                                                <tr>
                                                    <td class="py-2 px-4 font-medium text-gray-600 bg-gray-50">Version</td>
                                                    <td class="py-2 px-4"><?php echo htmlspecialchars($parentThemeInfo['Version']); ?></td>
                                                </tr>
                                                <?php endif; ?>

                                                <!-- Parent Theme Author -->
                                                <?php if (!empty($parentThemeInfo['Author'])): ?>
                                                <tr>
                                                    <td class="py-2 px-4 font-medium text-gray-600 bg-gray-50">Author</td>
                                                    <td class="py-2 px-4">
                                                        <?php if (!empty($parentThemeInfo['Author URI'])): ?>
                                                            <a href="<?php echo htmlspecialchars($parentThemeInfo['Author URI']); ?>" 
                                                                target="_blank" 
                                                                class="text-primary hover:underline">
                                                                <?php echo htmlspecialchars($parentThemeInfo['Author']); ?>
                                                            </a>
                                                        <?php else: ?>
                                                            <?php echo htmlspecialchars($parentThemeInfo['Author']); ?>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                                <?php endif; ?>

                                                <!-- Parent Theme Description -->
                                                <?php if (!empty($parentThemeInfo['Description'])): ?>
                                                <tr>
                                                    <td class="py-2 px-4 font-medium text-gray-600 bg-gray-50">Description</td>
                                                    <td class="py-2 px-4"><?php echo htmlspecialchars($parentThemeInfo['Description']); ?></td>
                                                </tr>
                                                <?php endif; ?>

                                                <!-- Parent Theme License -->
                                                <?php if (!empty($parentThemeInfo['License'])): ?>
                                                <tr>
                                                    <td class="py-2 px-4 font-medium text-gray-600 bg-gray-50">License</td>
                                                    <td class="py-2 px-4"><?php echo htmlspecialchars($parentThemeInfo['License']); ?></td>
                                                </tr>
                                                <?php endif; ?>

                                                <!-- Parent Theme Screenshot -->
                                                <?php if (!empty($parentThemeInfo['Screenshot'])): ?>
                                                <tr>
                                                    <td class="py-2 px-4 font-medium text-gray-600 bg-gray-50">Theme Screenshot</td>
                                                    <td class="py-2 px-4">
                                                        <a href="<?php echo htmlspecialchars($parentThemeInfo['Screenshot']); ?>" target="_blank" class="block">
                                                            <img style="width: 200px;" 
                                                                    src="<?php echo htmlspecialchars($parentThemeInfo['Screenshot']); ?>" 
                                                                    alt="Parent Theme Screenshot" 
                                                                    class="h-auto rounded-xl border border-gray-200 shadow-md hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                

                                    <!-- WordPress.org Parent Theme Link -->
                                    <?php if ($parentWpThemeData): ?>
                                        <div class="p-4 bg-gray-50 shadow rounded-lg theme-item mb-6 hover:shadow-md transition-shadow duration-300">
                                            <div class="mt-4">
                                                <h4 class="text-lg font-semibold mb-2">
                                                    <a href="<?php echo htmlspecialchars($parentWpThemeData['url']); ?>" 
                                                        target="_blank" 
                                                        class="text-primary hover:underline flex items-center">
                                                        <?php echo htmlspecialchars($themeInfo['Parent Theme']) . '/' . htmlspecialchars($parentThemeInfo['Theme Name']); ?>
                                                        <svg class="external-link ml-1" width="12" height="12" viewBox="0 0 24 24">
                                                            <path fill="currentColor" d="M21 13v10h-21v-19h12v2h-10v15h17v-8h2z"/>
                                                            <path fill="currentColor" d="M13 12l5.7-5.7-1.4-1.4 3.7-3.9 4 4-3.9 3.7-1.4-1.4-5.7 5.7z"/>
                                                        </svg>
                                                    </a>
                                                </h4>
                                                
                                                <?php if (!empty($parentThemeInfo['Description'])): ?>
                                                    <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($parentThemeInfo['Description']); ?></p>
                                                <?php endif; ?>

                                                <div class="theme-meta flex flex-wrap items-center gap-4 text-sm text-gray-600 mb-4">
                                                    <?php if (!empty($parentThemeInfo['Version'])): ?>
                                                        <span class="version flex items-center">
                                                            <i class="fas fa-code-branch mr-1"></i>v<?php echo htmlspecialchars($parentThemeInfo['Version']); ?>
                                                        </span>
                                                    <?php endif; ?>
                                                    
                                                    <?php if (!empty($parentWpThemeData['rating'])): ?>
                                                        <span class="rating flex items-center">
                                                            <i class="fas fa-star text-yellow-400 mr-1"></i><?php echo number_format($parentWpThemeData['rating'] / 20, 1); ?>/5
                                                        </span>
                                                    <?php endif; ?>
                                                    
                                                    <?php if (!empty($parentWpThemeData['active_installs'])): ?>
                                                        <span class="installs flex items-center">
                                                            <i class="fas fa-download mr-1"></i><?php echo number_format($parentWpThemeData['active_installs']); ?>+ active installs
                                                        </span>
                                                    <?php endif; ?>
                                                    
                                                    <?php if (!empty($parentWpThemeData['last_updated'])): ?>
                                                        <span class="updated flex items-center">
                                                            <i class="fas fa-clock mr-1"></i>Updated: <?php echo date('M j, Y', strtotime($parentWpThemeData['last_updated'])); ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                                
                                                <div class="flex space-x-3">
                                                    <a href="<?php echo htmlspecialchars($parentWpThemeData['url']); ?>" 
                                                        target="_blank" 
                                                        rel="noopener"
                                                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                                        <i class="fab fa-wordpress mr-2"></i> View Parent Theme on WordPress.org
                                                    </a>
                                                    
                                                    <a href="https://downloads.wordpress.org/theme/<?php echo urlencode($themeInfo['Parent Theme']); ?>.zip" 
                                                        target="_blank" 
                                                        rel="noopener"
                                                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                                        <i class="fas fa-download mr-2"></i> Download Latest Version
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Non WordPress.org Parent Theme Link -->
                                    <?php if (!$parentWpThemeData): ?>
                                        <div class="p-4 bg-gray-50 rounded-lg theme-item mb-6 hover:shadow-md transition-shadow duration-300">
                                            <div class="mt-4">
                                                <p class="text-gray-600 mb-4"><b><?php echo htmlspecialchars($themeInfo['Parent Theme']) . '/' . htmlspecialchars($parentThemeInfo['Theme Name']); ?></b> not found on WordPress.org, this might be a custom or premium theme.</p>
                                                <div class="flex space-x-3">
                                                    <!-- Search on Google button -->
                                                    <a href="https://www.google.com/search?q=<?php echo urlencode($parentThemeInfo['Theme Name'] . ' wordpress theme'); ?>" 
                                                    target="_blank" 
                                                    rel="noopener"
                                                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                                        <i class="fab fa-google mr-2"></i> Search on Google
                                                    </a>

                                                    <!-- Search on WordPress.org button -->
                                                    <a href="https://wordpress.org/themes/search/<?php echo urlencode($parentThemeInfo['Theme Name'] ?? ''); ?>/" 
                                                    target="_blank" 
                                                    rel="noopener"
                                                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                                        <i class="fab fa-wordpress mr-2"></i> Search on WordPress.org
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                    </div>


                    <!-- Plugin Information -->
                    <div class="mt-6">
                        <h3 class="text-xl font-semibold mb-4"><i class="fas fa-puzzle-piece mr-2 text-primary"></i> Active Plugins Details</h3>
                        <?php if (!empty($result['plugins'])): ?>
                            <div class="plugins-grid grid gap-6">
                                <?php $index = 1; foreach ($result['plugins'] as $plugin): ?>
                                <?php $pluginInfo = getPluginInfo($plugin); ?>
                                <?php $pluginSlug = basename($plugin); ?>
                                <div class="p-4 bg-gray-50 shadow rounded-lg plugin-item hover:shadow-md transition-shadow duration-300 relative">
                                    <!-- Plugin Number Badge -->
                                    <div class="absolute -top-2 -left-3 w-8 h-8 bg-gray-500 text-white rounded-full flex items-center justify-center shadow-md z-10 text-sm font-semibold">
                                        <?php echo $index++; ?>
                                    </div>
                                    <div class="flex flex-col pt-2">
                                        <!-- Plugin Name and WordPress.org Link or Custom Label -->
                                        <?php if (!empty($pluginInfo)): ?>
                                            <h4 class="text-lg font-semibold mb-2 flex items-center">
                                                <a href="https://wordpress.org/plugins/<?php echo urlencode($pluginSlug); ?>/" 
                                                   target="_blank" 
                                                   class="text-primary hover:underline flex items-center">
                                                    <?php echo htmlspecialchars($pluginInfo['name'] ?: $plugin); ?> 
                                                    <svg class="external-link ml-1" width="12" height="12" viewBox="0 0 24 24">
                                                        <path fill="currentColor" d="M21 13v10h-21v-19h12v2h-10v15h17v-8h2z"/>
                                                        <path fill="currentColor" d="M13 12l5.7-5.7-1.4-1.4 3.7-3.9 4 4-3.9 3.7-1.4-1.4-5.7 5.7z"/>
                                                    </svg>
                                                </a>
                                            </h4>
                                            
                                            <!-- Plugin Meta Information -->
                                            <div class="plugin-meta flex flex-wrap items-center gap-4 text-sm text-gray-600 mb-4">
                                                <?php if (!empty($pluginInfo['version'])): ?>
                                                    <span class="version flex items-center">
                                                        <i class="fas fa-code-branch mr-1"></i>v<?php echo htmlspecialchars($pluginInfo['version']); ?>
                                                    </span>
                                                <?php endif; ?>
                                                
                                                <?php if (!empty($pluginInfo['rating'])): ?>
                                                    <span class="rating flex items-center">
                                                        <i class="fas fa-star text-yellow-400 mr-1"></i><?php echo number_format($pluginInfo['rating'] / 20, 1); ?>/5
                                                    </span>
                                                <?php endif; ?>
                                                
                                                <?php if (!empty($pluginInfo['active_installs'])): ?>
                                                    <span class="installs flex items-center">
                                                        <i class="fas fa-download mr-1"></i><?php echo number_format($pluginInfo['active_installs']); ?>+ active installs
                                                    </span>
                                                <?php endif; ?>
                                                
                                                <?php if (!empty($pluginInfo['last_updated'])): ?>
                                                    <span class="updated flex items-center">
                                                        <i class="fas fa-clock mr-1"></i>Updated: <?php echo date('M j, Y', strtotime($pluginInfo['last_updated'])); ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>

                                            <!-- Plugin Description -->
                                            <?php if (!empty($pluginInfo['description'])): ?>
                                                <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($pluginInfo['description']); ?></p>
                                            <?php endif; ?>

                                            <!-- Plugin Download/View Buttons -->
                                            <div class="flex space-x-3">
                                                <a href="https://wordpress.org/plugins/<?php echo urlencode($pluginSlug); ?>/" 
                                                   target="_blank" 
                                                   rel="noopener"
                                                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                                    <i class="fab fa-wordpress mr-2"></i> View on WordPress.org
                                                </a>
                                                
                                                <a href="https://downloads.wordpress.org/plugin/<?php echo urlencode($pluginSlug); ?>.zip" 
                                                   target="_blank" 
                                                   rel="noopener"
                                                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                                    <i class="fas fa-download mr-2"></i> Download Latest Version
                                                </a>
                                            </div>
                                        <?php else: ?>
                                            <!-- Unknown/Custom Plugin Display -->
                                            <h4 class="text-lg font-semibold mb-2 flex items-center">
                                                <?php echo htmlspecialchars($pluginSlug); ?>
                                                <span class="ml-2 text-sm text-gray-500">(Custom/Premium Plugin)</span>
                                            </h4>
                                            
                                            <!-- Search buttons for unknown plugins -->
                                            <div class="flex space-x-3 mt-4">
                                                <a href="https://www.google.com/search?q=<?php echo urlencode($pluginSlug . ' wordpress plugin'); ?>" 
                                                   target="_blank" 
                                                   rel="noopener"
                                                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                                    <i class="fab fa-google mr-2"></i> Search on Google
                                                </a>
                                                
                                                <a href="https://wordpress.org/plugins/search/<?php echo urlencode($pluginSlug); ?>/" 
                                                   target="_blank" 
                                                   rel="noopener"
                                                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                                                    <i class="fab fa-wordpress mr-2"></i> Search on WordPress.org
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-gray-600">No active plugins detected.</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- 2. Security Analysis -->
                    <div class="mt-8 mb-16">
                        <h2 class="text-2xl font-bold mb-6 flex items-center">
                            <i class="fas fa-shield-virus text-primary mr-3"></i>
                            Security Analysis
                        </h2>
                        <?php if (isset($result['security'])): ?>
                            <div class="bg-white shadow rounded-lg overflow-hidden">
                                <table class="min-w-full">
                                    <tbody class="divide-y divide-gray-200">
                                        <?php foreach ($result['security'] as $key => $value): ?>
                                            <?php 
                                                // Reverse the logic - if something is exposed/accessible, it's vulnerable
                                                $isSecure = !$value;
                                                $statusClass = $isSecure ? 'text-green-700 bg-green-50' : 'text-red-700 bg-red-50';
                                                $icon = $isSecure ? 'fa-check-circle' : 'fa-times-circle';
                                                $status = $isSecure ? 'Secure' : 'Vulnerable';
                                                
                                                // Better labels for security checks
                                                $labels = [
                                                    'version_exposed' => 'WordPress Version Exposure',
                                                    'readme_exposed' => 'readme.html File Access',
                                                    'directory_listing' => 'Directory Listing',
                                                    'debug_log_accessible' => 'Debug.log Access'
                                                ];
                                                $label = $labels[$key] ?? ucwords(str_replace('_', ' ', $key));
                                            ?>
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap bg-gray-50 text-sm font-medium text-gray-600 w-1/3"><?php echo $label; ?></td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?php echo $statusClass; ?>">
                                                        <i class="fas <?php echo $icon; ?> mr-2"></i>
                                                        <?php echo $status; ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <!-- Security Recommendations -->
                            <?php if (isset($result['security'])): ?>
                                <div class="mt-6">
                                    <h3 class="text-lg font-semibold mb-4 flex items-center">
                                        <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>
                                        Security Recommendations
                                    </h3>
                                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
                                        <ul class="space-y-3">
                                            <?php
                                            $recommendations = [];
                                            
                                            if ($result['security']['version_exposed']) {
                                                $recommendations[] = [
                                                    'icon' => 'fa-code-branch',
                                                    'text' => 'Remove WordPress version exposure by adding the following to your theme\'s functions.php:<br>
                                                    <code class="bg-blue-100 px-2 py-1 rounded">remove_action(\'wp_head\', \'wp_generator\');</code>'
                                                ];
                                            }
                                            
                                            if ($result['security']['readme_exposed']) {
                                                $recommendations[] = [
                                                    'icon' => 'fa-file-alt',
                                                    'text' => 'Delete or restrict access to readme.html file in your WordPress root directory'
                                                ];
                                            }
                                            
                                            if ($result['security']['directory_listing']) {
                                                $recommendations[] = [
                                                    'icon' => 'fa-folder',
                                                    'text' => 'Disable directory listing by adding this line to your .htaccess file:<br>
                                                    <code class="bg-blue-100 px-2 py-1 rounded">Options -Indexes</code>'
                                                ];
                                            }
                                            
                                            if ($result['security']['debug_log_accessible']) {
                                                $recommendations[] = [
                                                    'icon' => 'fa-file-code',
                                                    'text' => 'Protect or disable the debug.log file:<br>
                                                    1. Add to wp-config.php: <code class="bg-blue-100 px-2 py-1 rounded">define(\'WP_DEBUG_LOG\', false);</code><br>
                                                    2. Or restrict access via .htaccess'
                                                ];
                                            }

                                            // If all checks pass
                                            if (empty($recommendations)) {
                                                echo '<li class="flex items-start">
                                                    <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                                                    <span>Great job! Your site passes all basic security checks. Continue monitoring and maintaining good security practices.</span>
                                                </li>';
                                            } else {
                                                foreach ($recommendations as $rec) {
                                                    echo '<li class="flex items-start">
                                                        <i class="fas ' . $rec['icon'] . ' text-blue-500 mt-1 mr-3"></i>
                                                        <span>' . $rec['text'] . '</span>
                                                    </li>';
                                                }
                                            }
                                            ?>
                                        </ul>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <!-- 3. Performance Metrics -->
                    <div class="mt-8">
                        <h2 class="text-2xl font-bold mb-6 flex items-center">
                            <i class="fas fa-tachometer-alt text-primary mr-3"></i>
                            Performance Metrics
                        </h2>
                        <?php if (isset($result['performance'])): ?>
                        <div class="bg-white shadow rounded-lg overflow-hidden">
                            <div class="p-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Load Time Section -->
                                    <div class="bg-white rounded-xl p-6 shadow-md hover:shadow-lg transition-all duration-300 border border-gray-100 relative overflow-hidden group">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0 w-10 h-10 bg-primary bg-opacity-10 rounded-full flex items-center justify-center mr-4">
                                                <i class="fas fa-clock text-primary"></i>
                                            </div>
                                            <div class="flex-grow">
                                                <p class="text-sm font-medium text-gray-600 mb-2">Load Time</p>
                                                <p class="text-2xl font-bold text-primary mb-2">
                                                    <?php echo number_format($result['performance']['load_time'], 2); ?> 
                                                    <span class="text-base font-normal text-gray-600">seconds</span>
                                                </p>
                                                <p class="text-xs text-gray-500 italic">Faster load times provide better user experience</p>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Page Size Section -->
                                    <div class="bg-white rounded-xl p-6 shadow-md hover:shadow-lg transition-all duration-300 border border-gray-100 relative overflow-hidden group">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0 w-10 h-10 bg-primary bg-opacity-10 rounded-full flex items-center justify-center mr-4">
                                                <i class="fas fa-database text-primary"></i>
                                            </div>
                                            <div class="flex-grow">
                                                <p class="text-sm font-medium text-gray-600 mb-2">Page Size</p>
                                                <p class="text-2xl font-bold text-primary mb-2">
                                                    <?php echo formatBytes($result['performance']['size'] ?? 0); ?>
                                                </p>
                                                <p class="text-xs text-gray-500 italic">Smaller page sizes ensure faster loading</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Performance Recommendations -->
                        <div class="mt-6">
                            <h4 class="text-lg font-semibold mb-4 flex items-center">
                                <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>
                                Performance Recommendations
                            </h4>
                            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
                                <ul class="space-y-3">
                                    <?php
                                    $recommendations = [];
                                    
                                    // Load time recommendations
                                    if ($result['performance']['load_time'] > 2) {
                                        $recommendations[] = [
                                            'icon' => 'fa-clock',
                                            'text' => 'Page load time (' . number_format($result['performance']['load_time'], 2) . 's) is higher than recommended (2s). Consider implementing the following optimizations:'
                                        ];
                                        $recommendations[] = [
                                            'icon' => 'fa-server',
                                            'text' => 'Enable server-side caching:<br>
                                            <code class="bg-blue-100 px-2 py-1 rounded">Install and configure WP Super Cache or W3 Total Cache</code>'
                                        ];
                                        $recommendations[] = [
                                            'icon' => 'fa-compress',
                                            'text' => 'Minify and combine CSS/JS files:<br>
                                            <code class="bg-blue-100 px-2 py-1 rounded">Use Autoptimize or WP Rocket plugin</code>'
                                        ];
                                    }
                                    
                                    // Page size recommendations
                                    if ($result['performance']['size'] > 2097152) {
                                        $recommendations[] = [
                                            'icon' => 'fa-image',
                                            'text' => 'Optimize images using WebP format and compress resources to reduce page size'
                                        ];
                                    }

                                    // If all checks pass
                                    if (empty($recommendations)) {
                                        echo '<li class="flex items-start">
                                            <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                                            <span>Excellent! Your site\'s performance metrics are within recommended ranges.</span>
                                        </li>';
                                    } else {
                                        foreach ($recommendations as $rec) {
                                            echo '<li class="flex items-start">
                                                <i class="fas ' . $rec['icon'] . ' text-blue-500 mt-1 mr-3"></i>
                                                <span>' . $rec['text'] . '</span>
                                            </li>';
                                        }
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>
                </div>




                <?php if (!isset($result)): ?>
                    <!-- Landing Page Content -->
                <div class="bg-white rounded-2xl shadow-xl p-8 mb-8">
                    <div class="mt-12 prose prose-lg max-w-6xl mx-auto">
                        <h2 class="text-3xl font-bold text-gray-900 mb-6 text-center">WordPress Theme & Plugin Detection Made Easy</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-12">
                            <div class="bg-white rounded-xl shadow-md p-8 hover:shadow-lg transition-shadow duration-300 border border-gray-100">
                                <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                                    <i class="fas fa-bolt text-primary mr-3"></i>
                                    Why Use Our Tool?
                                </h3>
                                <ul class="space-y-4 text-gray-600">
                                    <li class="flex items-start transform hover:translate-x-2 transition-transform duration-300">
                                        <span class="flex-shrink-0 w-8 h-8 bg-primary bg-opacity-10 rounded-full flex items-center justify-center mr-3 mt-1">
                                            <i class="fas fa-check text-primary"></i>
                                        </span>
                                        <div>
                                            <b class="text-gray-800 block mb-1">Enter a WordPress Site URL</b>
                                            <span class="text-gray-600">Paste the website's link in the search bar.</span>
                                        </div>
                                    </li>
                                    <li class="flex items-start transform hover:translate-x-2 transition-transform duration-300">
                                        <span class="flex-shrink-0 w-8 h-8 bg-primary bg-opacity-10 rounded-full flex items-center justify-center mr-3 mt-1">
                                            <i class="fas fa-check text-primary"></i>
                                        </span>
                                        <div>
                                            <b class="text-gray-800 block mb-1">Click "Analyze"</b>
                                            <span class="text-gray-600">Let our detector work its magic.</span>
                                        </div>
                                    </li>
                                    <li class="flex items-start transform hover:translate-x-2 transition-transform duration-300">
                                        <span class="flex-shrink-0 w-8 h-8 bg-primary bg-opacity-10 rounded-full flex items-center justify-center mr-3 mt-1">
                                            <i class="fas fa-check text-primary"></i>
                                        </span>
                                        <div>
                                            <b class="text-gray-800 block mb-1">Get Detailed Results</b>
                                            <span class="text-gray-600">Instantly view the active theme, plugin details, and more.</span>
                                        </div>
                                    </li>
                                    <li class="flex items-start transform hover:translate-x-2 transition-transform duration-300">
                                        <span class="flex-shrink-0 w-8 h-8 bg-primary bg-opacity-10 rounded-full flex items-center justify-center mr-3 mt-1">
                                            <i class="fas fa-check text-primary"></i>
                                        </span>
                                        <div>
                                            <b class="text-gray-800 block mb-1">Learn and Improve</b>
                                            <span class="text-gray-600">Use the information to optimize your website or inspire your next project.</span>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            
                            <div class="bg-white rounded-xl shadow-md p-8 hover:shadow-lg transition-shadow duration-300 border border-gray-100">
                                <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                                    <i class="fas fa-cogs text-primary mr-3"></i>
                                    How It Works
                                </h3>
                                <ol class="space-y-4 text-gray-600">
                                    <li class="flex items-start transform hover:translate-x-2 transition-transform duration-300">
                                        <span class="flex-shrink-0 w-8 h-8 bg-primary text-primary bg-opacity-10 rounded-full flex items-center justify-center mr-3 mt-1">
                                            1
                                        </span>
                                        <div>
                                            <b class="text-gray-800 block mb-1">Enter a WordPress Site URL</b>
                                            <span class="text-gray-600">Simply paste the link of the website you want to analyze.</span>
                                        </div>
                                    </li>
                                    <li class="flex items-start transform hover:translate-x-2 transition-transform duration-300">
                                        <span class="flex-shrink-0 w-8 h-8 bg-primary text-primary bg-opacity-10 rounded-full flex items-center justify-center mr-3 mt-1">
                                            2
                                        </span>
                                        <div>
                                            <b class="text-gray-800 block mb-1">Click "Analyze"</b>
                                            <span class="text-gray-600">Let our detector work its magic.</span>
                                        </div>
                                    </li>
                                    <li class="flex items-start transform hover:translate-x-2 transition-transform duration-300">
                                        <span class="flex-shrink-0 w-8 h-8 bg-primary text-primary bg-opacity-10 rounded-full flex items-center justify-center mr-3 mt-1">
                                            3
                                        </span>
                                        <div>
                                            <b class="text-gray-800 block mb-1">Get Detailed Results</b>
                                            <span class="text-gray-600">Instantly view the active theme, plugin details, and more.</span>
                                        </div>
                                    </li>
                                    <li class="flex items-start transform hover:translate-x-2 transition-transform duration-300">
                                        <span class="flex-shrink-0 w-8 h-8 bg-primary text-primary bg-opacity-10 rounded-full flex items-center justify-center mr-3 mt-1">
                                            4
                                        </span>
                                        <div>
                                            <b class="text-gray-800 block mb-1">Learn and Improve</b>
                                            <span class="text-gray-600">Use the information to optimize your website or inspire your next project.</span>
                                        </div>
                                    </li>
                                </ol>
                            </div>
                        </div>

                        <div class="bg-gray-50 rounded-xl shadow-md p-8 mb-12 hover:shadow-lg transition-shadow duration-300">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 mr-6">
                                    <div class="w-12 h-12 bg-primary bg-opacity-10 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-tools text-primary text-2xl"></i>
                                    </div>
                                </div>
                                <div>
                                    <h3 class="text-2xl font-bold text-gray-800 mb-3 flex items-center">
                                        About the Tool
                                        <span class="ml-3 px-3 py-1 bg-primary bg-opacity-10 text-primary text-sm rounded-full font-medium">Free & Fast</span>
                                    </h3>
                                    <div class="prose prose-lg text-gray-600">
                                        <p class="leading-relaxed mb-4">Our tool is powered by advanced algorithms to scan and identify WordPress themes and plugins with precision. Whether you're a web developer, designer, or just curious about how websites are built, this tool is your go-to resource.</p>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                                            <div class="flex items-center">
                                                <i class="fas fa-bolt text-primary mr-2"></i>
                                                <span class="text-sm">Lightning Fast Results</span>
                                            </div>
                                            <div class="flex items-center">
                                                <i class="fas fa-shield-alt text-primary mr-2"></i>
                                                <span class="text-sm">Safe & Secure</span>
                                            </div>
                                            <div class="flex items-center">
                                                <i class="fas fa-chart-line text-primary mr-2"></i>
                                                <span class="text-sm">99.9% Accuracy</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 rounded-xl shadow-md p-8 mb-12 hover:shadow-lg transition-shadow duration-300">
                            <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                                <i class="fas fa-question-circle text-primary mr-3"></i>
                                Frequently Asked Questions
                            </h3>
                            <div class="space-y-6">
                                <div class="bg-white rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-100">
                                    <div class="flex items-start">
                                        <span class="flex-shrink-0 w-8 h-8 bg-primary bg-opacity-10 rounded-full flex items-center justify-center mr-3 mt-1">
                                            <i class="fas fa-check text-primary"></i>
                                        </span>
                                        <div>
                                            <h4 class="font-bold text-gray-800 mb-2">Can I detect if a website is built with WordPress?</h4>
                                            <p class="text-gray-600 leading-relaxed">Yes! Our tool instantly identifies whether a website is built on WordPress, along with its active theme and plugins.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-white rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-100">
                                    <div class="flex items-start">
                                        <span class="flex-shrink-0 w-8 h-8 bg-primary bg-opacity-10 rounded-full flex items-center justify-center mr-3 mt-1">
                                            <i class="fas fa-code text-primary"></i>
                                        </span>
                                        <div>
                                            <h4 class="font-bold text-gray-800 mb-2">Does this tool work with custom WordPress themes?</h4>
                                            <p class="text-gray-600 leading-relaxed">Yes, it can detect custom themes too. For custom themes, you'll get details like the theme's name and author, if available.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-white rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-100">
                                    <div class="flex items-start">
                                        <span class="flex-shrink-0 w-8 h-8 bg-primary bg-opacity-10 rounded-full flex items-center justify-center mr-3 mt-1">
                                            <i class="fas fa-chart-line text-primary"></i>
                                        </span>
                                        <div>
                                            <h4 class="font-bold text-gray-800 mb-2">How accurate is the WordPress Theme & Plugin Detector?</h4>
                                            <p class="text-gray-600 leading-relaxed">Our tool is designed to provide highly accurate results by analyzing the website's source code. However, in some cases, plugins or themes with custom settings might not display full details.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-white rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-100">
                                    <div class="flex items-start">
                                        <span class="flex-shrink-0 w-8 h-8 bg-primary bg-opacity-10 rounded-full flex items-center justify-center mr-3 mt-1">
                                            <i class="fas fa-puzzle-piece text-primary"></i>
                                        </span>
                                        <div>
                                            <h4 class="font-bold text-gray-800 mb-2">Can I use this tool to detect inactive plugins on a site?</h4>
                                            <p class="text-gray-600 leading-relaxed">No, the tool only identifies active plugins currently being used on the website.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-white rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-100">
                                    <div class="flex items-start">
                                        <span class="flex-shrink-0 w-8 h-8 bg-primary bg-opacity-10 rounded-full flex items-center justify-center mr-3 mt-1">
                                            <i class="fas fa-crown text-primary"></i>
                                        </span>
                                        <div>
                                            <h4 class="font-bold text-gray-800 mb-2">Does the tool detect premium or paid WordPress themes and plugins?</h4>
                                            <p class="text-gray-600 leading-relaxed">Yes, the tool can detect premium themes and plugins if their metadata is accessible. You'll also see whether a plugin is free or paid if the information is publicly available.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-white rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-100">
                                    <div class="flex items-start">
                                        <span class="flex-shrink-0 w-8 h-8 bg-primary bg-opacity-10 rounded-full flex items-center justify-center mr-3 mt-1">
                                            <i class="fas fa-cogs text-primary"></i>
                                        </span>
                                        <div>
                                            <h4 class="font-bold text-gray-800 mb-2">Will this tool work on websites with heavy customizations?</h4>
                                            <p class="text-gray-600 leading-relaxed">It depends on how the customizations are applied. If the WordPress themes and plugins are still active and their data is available, our tool will detect them.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-white rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-100">
                                    <div class="flex items-start">
                                        <span class="flex-shrink-0 w-8 h-8 bg-primary bg-opacity-10 rounded-full flex items-center justify-center mr-3 mt-1">
                                            <i class="fas fa-user-friends text-primary"></i>
                                        </span>
                                        <div>
                                            <h4 class="font-bold text-gray-800 mb-2">Do I need technical knowledge to use this tool?</h4>
                                            <p class="text-gray-600 leading-relaxed">Not at all! The tool is designed to be user-friendly for anyone, whether you're a developer or just someone curious about a website's design.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-white rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-100">
                                    <div class="flex items-start">
                                        <span class="flex-shrink-0 w-8 h-8 bg-primary bg-opacity-10 rounded-full flex items-center justify-center mr-3 mt-1">
                                            <i class="fas fa-infinity text-primary"></i>
                                        </span>
                                        <div>
                                            <h4 class="font-bold text-gray-800 mb-2">Can I use this tool to analyze multiple websites?</h4>
                                            <p class="text-gray-600 leading-relaxed">Absolutely! You can use the tool as many times as you want to analyze different WordPress websites.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-white rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-100">
                                    <div class="flex items-start">
                                        <span class="flex-shrink-0 w-8 h-8 bg-primary bg-opacity-10 rounded-full flex items-center justify-center mr-3 mt-1">
                                            <i class="fas fa-cloud text-primary"></i>
                                        </span>
                                        <div>
                                            <h4 class="font-bold text-gray-800 mb-2">Does this tool work on websites using caching or CDN?</h4>
                                            <p class="text-gray-600 leading-relaxed">Yes, but in some cases, caching or a CDN may mask certain details. Even then, our tool works to provide the most accurate results possible.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-white rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-100">
                                    <div class="flex items-start">
                                        <span class="flex-shrink-0 w-8 h-8 bg-primary bg-opacity-10 rounded-full flex items-center justify-center mr-3 mt-1">
                                            <i class="fas fa-lightbulb text-primary"></i>
                                        </span>
                                        <div>
                                            <h4 class="font-bold text-gray-800 mb-2">How can I use the information provided by this tool?</h4>
                                            <p class="text-gray-600 leading-relaxed">You can use the insights to understand the technologies behind successful websites, get inspiration for your own WordPress project, and identify plugins and themes to improve your website.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-white rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-100">
                                    <div class="flex items-start">
                                        <span class="flex-shrink-0 w-8 h-8 bg-primary bg-opacity-10 rounded-full flex items-center justify-center mr-3 mt-1">
                                            <i class="fas fa-shield-alt text-primary"></i>
                                        </span>
                                        <div>
                                            <h4 class="font-bold text-gray-800 mb-2">Is it safe to use this tool?</h4>
                                            <p class="text-gray-600 leading-relaxed">Absolutely! The tool only analyzes publicly available data and does not access sensitive or private information.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-white rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-100">
                                    <div class="flex items-start">
                                        <span class="flex-shrink-0 w-8 h-8 bg-primary bg-opacity-10 rounded-full flex items-center justify-center mr-3 mt-1">
                                            <i class="fas fa-download text-primary"></i>
                                        </span>
                                        <div>
                                            <h4 class="font-bold text-gray-800 mb-2">Can I use the detected plugins and themes on my website?</h4>
                                            <p class="text-gray-600 leading-relaxed">Yes, but remember to download them from legitimate sources like WordPress.org or official plugin/theme developers to avoid security risks.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-white rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-100">
                                    <div class="flex items-start">
                                        <span class="flex-shrink-0 w-8 h-8 bg-primary bg-opacity-10 rounded-full flex items-center justify-center mr-3 mt-1">
                                            <i class="fas fa-question-circle text-primary"></i>
                                        </span>
                                        <div>
                                            <h4 class="font-bold text-gray-800 mb-2">Why can't the tool detect some plugins or themes?</h4>
                                            <p class="text-gray-600 leading-relaxed">Some themes and plugins might not disclose their metadata or use advanced customizations, making it harder to identify them. However, this is rare.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-white rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-100">
                                    <div class="flex items-start">
                                        <span class="flex-shrink-0 w-8 h-8 bg-primary bg-opacity-10 rounded-full flex items-center justify-center mr-3 mt-1">
                                            <i class="fas fa-tachometer-alt text-primary"></i>
                                        </span>
                                        <div>
                                            <h4 class="font-bold text-gray-800 mb-2">Does this tool slow down the website I'm analyzing?</h4>
                                            <p class="text-gray-600 leading-relaxed">Not at all! The analysis happens externally, so it doesn't impact the performance of the website you're analyzing.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-white rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-100">
                                    <div class="flex items-start">
                                        <span class="flex-shrink-0 w-8 h-8 bg-primary bg-opacity-10 rounded-full flex items-center justify-center mr-3 mt-1">
                                            <i class="fas fa-headset text-primary"></i>
                                        </span>
                                        <div>
                                            <h4 class="font-bold text-gray-800 mb-2">Can I get support if I need help with a detected theme or plugin?</h4>
                                            <p class="text-gray-600 leading-relaxed">Absolutely! If you're interested in customizing or using a detected theme or plugin, feel free to explore our WordPress Services for expert assistance.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php endif; ?>

            </div>

        </div>

    </div>


<footer class="bg-white dark:bg-gray-900">
    <div class="mx-auto w-full">
    <div class="mx-auto w-full max-w-screen-xl">
      <div class="grid grid-cols-2 gap-8 px-4 py-6 lg:py-8 md:grid-cols-4">
        <div>
            <h2 class="mb-6 text-base font-semibold text-gray-900 uppercase dark:text-white">About</h2>
            <ul class="text-gray-500 dark:text-gray-400 font-medium">
                <li class="mb-4">
                    <a href="https://yeasin.me/about/" target="_blank" rel="noopener" class="hover:underline hover:text-primary">About Us</a>
                </li>
                <li class="mb-4">
                    <a href="https://yeasin.me/testimonials/" target="_blank" rel="noopener"  class="hover:underline hover:text-primary">Wall of Love</a>
                </li>
                <li class="mb-4">
                    <a href="https://yeasin.me/services/" target="_blank" rel="noopener" class="hover:underline hover:text-primary">Our Services</a>
                    <span class="ml-2 text-xs bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300 px-2 py-1 rounded-full"><i class="fas fa-crown mr-1"></i>Premium</span>
                </li>
                <li class="mb-4">
                    <a href="https://yeasin.me/contact/" target="_blank" rel="noopener" class="hover:underline hover:text-primary">Contact</a>
                </li>
            </ul>
        </div>
        <div>
            <h2 class="mb-6 text-base font-semibold text-gray-900 uppercase dark:text-white">Legal</h2>
            <ul class="text-gray-500 dark:text-gray-400 font-medium">
                <li class="mb-4">
                    <a href="https://yeasin.me/privacy-policy/" target="_blank" rel="noopener" class="hover:underline hover:text-primary">Privacy Policy</a>
                </li>
                <li class="mb-4">
                    <a href="https://yeasin.me/terms/" target="_blank" rel="noopener" class="hover:underline hover:text-primary">Terms & Conditions</a>
                </li>
                <li class="mb-4">
                    <a href="https://yeasin.me/cookie-policy/" target="_blank" rel="noopener" class="hover:underline hover:text-primary">Cookie Policy</a>
                </li>
                <li class="mb-4">
                    <a href="https://yeasin.me/disclosure/" target="_blank" rel="noopener" class="hover:underline hover:text-primary">Disclosure</a>
                </li>
            </ul>
        </div>
        <div>
            <h2 class="mb-6 text-base font-semibold text-gray-900 uppercase dark:text-white">More Tools <span class="text-xs bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300 px-2 py-1 rounded-full">Free</span></h2>
            <ul class="text-gray-500 dark:text-gray-400 font-medium">
                <li class="mb-4">
                    <a href="#" class="hover:underline hover:text-primary">WP Security Scanner</a>
                    <span class="ml-2 text-xs bg-primary bg-opacity-10 text-primary px-2 py-1 rounded-full"><i class="fas fa-rocket mr-1"></i>Soon</span>
                </li>
                <li class="mb-4">
                    <a href="#" class="hover:underline hover:text-primary">Performance Checker</a>
                    <span class="ml-2 text-xs bg-primary bg-opacity-10 text-primary px-2 py-1 rounded-full"><i class="fas fa-rocket mr-1"></i>Soon</span>
                </li>
                <li class="mb-4">
                    <a href="#" class="hover:underline hover:text-primary">Site Audit</a>
                    <span class="ml-2 text-xs bg-primary bg-opacity-10 text-primary px-2 py-1 rounded-full"><i class="fas fa-rocket mr-1"></i>Soon</span>
                </li>
                <li class="mb-4">
                    <a href="#" class="hover:underline hover:text-primary">SEO Analyzer</a>
                    <span class="ml-2 text-xs bg-primary bg-opacity-10 text-primary px-2 py-1 rounded-full"><i class="fas fa-rocket mr-1"></i>Soon</span>
                </li>
            </ul>
        </div>
        <div>
            <h2 class="mb-6 text-base font-semibold text-gray-900 uppercase dark:text-white">Our Brands</h2>
            <ul class="text-gray-500 dark:text-gray-400 font-medium">
                <li class="mb-4">
                    <a href="#" target="_blank" rel="noopener">
                        eComXpertsPro
                    </a>
                </li>
            </ul>
        </div>
    </div>
    </div>
    <div class="px-4 py-6 bg-gray-100 dark:bg-gray-700">
    <div class="mx-auto w-full max-w-screen-xl md:flex md:items-center md:justify-between">
        <div class="flex justify-center">
            <span class="text-sm text-gray-500 dark:text-gray-300 sm:text-center"> 2024 <a href="https://yeasin.me/wordpress-theme-plugin-detector/" class="hover:underline hover:text-primary">WordPress Theme & Plugin Detector</a>. Made with <i class="fas fa-heart text-primary"></i> for <a href="https://wordpress.org/" target="_blank" rel="noopener"><i class="fab fa-wordpress text-primary"></i></a>
            </span>
        </div>
        <div class="flex mt-4 justify-center md:mt-0 space-x-5 rtl:space-x-reverse">
            <a href="https://github.com/yeasinhossain" target="_blank" rel="noopener" class="text-gray-400 hover:text-primary">
                <i class="fab fa-github text-xl"></i>
                <span class="sr-only">GitHub page</span>
            </a>
            <a href="https://x.com/meetyeasin" target="_blank" rel="noopener" class="text-gray-400 hover:text-primary">
                <i class="fab fa-x text-xl"></i>
                <span class="sr-only">X page</span>
            </a>
            <a href="https://www.youtube.com/@TheYeasinHossain?sub_confirmation=1n" target="_blank" rel="noopener" class="text-gray-400 hover:text-primary">
                <i class="fab fa-youtube text-xl"></i>
                <span class="sr-only">YouTube channel</span>
            </a>
            <a href="https://www.linkedin.com/in/myeasinhossain" target="_blank" rel="noopener" class="text-gray-400 hover:text-primary">
                <i class="fab fa-linkedin text-xl"></i>
                <span class="sr-only">LinkedIn profile</span>
            </a>
            <a href="https://dribbble.com/yeasinhossain" target="_blank" rel="noopener" class="text-gray-400 hover:text-primary">
                <i class="fab fa-dribbble text-xl"></i>
                <span class="sr-only">Dribbble profile</span>
            </a>
        </div>
        </div>
      </div>
    </div>
</footer>
<!-- Go to Top Button -->
<button id="goToTop" class="fixed bottom-4 right-4 bg-primary text-white p-2 rounded-full shadow-lg cursor-pointer hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-primary-300 transition-opacity duration-300 opacity-0 invisible" aria-label="Go to top">
    <i class="fas fa-arrow-up text-xl"></i>
</button>

<script>
    // Go to Top Button functionality
    const goToTopButton = document.getElementById('goToTop');
    
    window.addEventListener('scroll', () => {
        if (window.scrollY > 300) {
            goToTopButton.classList.remove('opacity-0', 'invisible');
            goToTopButton.classList.add('opacity-100');
        } else {
            goToTopButton.classList.add('opacity-0', 'invisible');
            goToTopButton.classList.remove('opacity-100');
        }
    });

    goToTopButton.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
</script>
    <script>
    document.getElementById('analyzeForm').addEventListener('submit', function() {
        document.getElementById('loadingOverlay').style.display = 'flex';
    });
    </script>
    <script>
        document.getElementById('mobile-menu-button').addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });
    </script>
</body>
</html>