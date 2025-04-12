# PHP Blog System

Modern, responsive blog and portfolio system built with PHP and MySQL.

## Features

### Frontend
- Responsive design with Bootstrap 5
- Mobile-friendly navigation with hamburger menu
- Blog posts with categories and search functionality
- Modern card-based layouts with placeholder images
- Accordion components for About page
- User authentication and authorization

### Admin Panel
- Dashboard with analytics overview
- Post management (create, edit, delete)
- Category management
- Comment moderation
- User management with admin privileges
- File uploads for post images

### Technical Features
- Secure user authentication system
- SEO-friendly URLs with .htaccess
- Input sanitization and validation
- Responsive image handling
- Advanced search capabilities
- 404 error page
- Custom JavaScript functionality

## File Structure

```
blog/
├── admin/                  # Admin panel
│   ├── includes/           # Admin-specific include files
│   ├── index.php           # Admin dashboard
│   ├── posts.php           # Manage posts
│   ├── categories.php      # Manage categories
│   └── ...
├── assets/                 # Static assets
│   ├── css/                # CSS files
│   ├── js/                 # JavaScript files
│   └── images/             # Uploaded images
├── includes/               # Core include files
│   ├── config.php          # Database configuration
│   ├── functions.php       # Helper functions
│   ├── header.php          # Common header template
│   └── footer.php          # Common footer template
├── index.php               # Homepage
├── blog.php                # Blog listing page
├── search.php              # Search results page
├── about.php               # About page
├── contact.php             # Contact page
└── post.php                # Single post display
```

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache web server with mod_rewrite enabled

## Installation

1. Import the database schema from `database.sql`
2. Configure database connection in `includes/config.php`
3. Set up virtual host or place files in web directory
4. Make sure `assets/images` directory is writable
5. Access the admin panel at `/admin`
6. Default admin credentials: admin/password (change immediately after login)

## Credits

- Bootstrap 5 - https://getbootstrap.com/
- Font Awesome - https://fontawesome.com/
- Unsplash - https://unsplash.com/ (for placeholder images)
- TinyMCE - https://www.tiny.cloud/ (for WYSIWYG editor)

## License

This project is licensed under the MIT License.

## Security Notes

- The default admin password should be changed immediately after installation.
- This is a simple blog system for educational purposes. For production environments, additional security measures should be implemented. 