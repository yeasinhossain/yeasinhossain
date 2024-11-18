# WordPress Theme & Plugin Detector

A powerful web application that helps users identify WordPress themes and plugins used on any WordPress website. Built with PHP and modern JavaScript, featuring a clean and responsive UI with Tailwind CSS.

## 🚀 Features

- **Theme Detection**: Identify WordPress themes used on any website
- **Plugin Detection**: Discover active plugins on WordPress sites
- **Real-time Validation**: Smart URL validation with instant feedback
- **Responsive Design**: Beautiful UI that works on all devices
- **User-friendly Interface**: Clean and intuitive design with clear feedback
- **Fast Analysis**: Quick and efficient WordPress site scanning
- **Accurate Results**: Reliable detection of themes and plugins
- **No Registration Required**: 100% free to use without any signup

## 🛠️ Technologies Used

- **Backend**: PHP
- **Frontend**: JavaScript, HTML5
- **Styling**: Tailwind CSS
- **Icons**: Font Awesome
- **Server**: Apache (XAMPP)

## 📋 Requirements

- PHP 7.4 or higher
- Apache Web Server
- mod_rewrite enabled
- XAMPP (recommended) or similar PHP development environment

## 🔧 Installation

1. Clone the repository to your XAMPP htdocs directory:
```bash
git clone [repository-url] /path/to/xampp/htdocs/wpd
```

2. Navigate to the project directory:
```bash
cd /path/to/xampp/htdocs/wpd
```

3. Ensure proper permissions:
```bash
chmod 755 -R /path/to/xampp/htdocs/wpd
```

4. Access the application through your web browser:
```
http://localhost/wpd
```

## 📦 Project Structure

```
wpd/
├── assets/
│   ├── css/
│   │   └── styles.css
│   └── js/
│       └── main.js
├── includes/
│   └── functions.php
├── index.php
├── check_wordpress.php
└── README.md
```

## 🔍 How It Works

1. User enters a WordPress website URL
2. Application validates the URL format
3. System analyzes the website's source code
4. Detects and displays the active theme
5. Identifies installed plugins
6. Shows results in a clean, organized interface

## 🔐 Security Features

- Input sanitization
- URL validation
- XSS protection
- Error handling
- Secure HTTP requests

## 🌟 Key Components

- **URL Validation**: Real-time URL format checking
- **Theme Detection**: Identifies WordPress themes
- **Plugin Detection**: Discovers active plugins
- **Error Handling**: User-friendly error messages
- **Responsive UI**: Mobile-first design approach

## 💡 Usage Tips

1. Enter any WordPress website URL
2. Wait for the analysis to complete
3. View detected theme and plugins
4. Use the information for research or inspiration
5. Try multiple websites as needed

## ⚙️ Configuration

No additional configuration required. The application works out of the box with XAMPP's default settings.

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 🙏 Acknowledgments

- WordPress.org for documentation
- Tailwind CSS for the UI framework
- Font Awesome for icons
- XAMPP for the development environment
