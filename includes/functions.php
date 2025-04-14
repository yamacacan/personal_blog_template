<?php
session_start();
require_once('config.php');

// Function to sanitize user inputs
function sanitize($data) {
    global $conn;
    return mysqli_real_escape_string($conn, htmlspecialchars(trim($data)));
}

// Function to generate slug from title
function generateSlug($text) {
    // Replace non letter or digits by -
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    // Transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    // Remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);
    // Trim
    $text = trim($text, '-');
    // Remove duplicate -
    $text = preg_replace('~-+~', '-', $text);
    // Lowercase
    $text = strtolower($text);
    
    if (empty($text)) {
        return 'n-a';
    }
    
    return $text;
}
function deleteMessage($message_id) {
    global $conn;
    $delete_query = "DELETE FROM messages WHERE id = ?";
    $stmt = mysqli_prepare($conn, $delete_query);
    mysqli_stmt_bind_param($stmt, "i", $message_id);
    
    if (mysqli_stmt_execute($stmt)) {
        return true; // Deletion successful
    } else {
        return false; // Deletion failed
    }
}

// Function to mark a message as read
function markMessageAsRead($message_id) {
    global $conn;
    $update_query = "UPDATE messages SET is_read = 1 WHERE id = ?";
    $stmt = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($stmt, "i", $message_id);
    
    if (mysqli_stmt_execute($stmt)) {
        return true; // Update successful
    } else {
        return false; // Update failed
    }
}
// Function to validate user login
function validateLogin($username, $password) {
    global $conn;
    $username = sanitize($username);
    
    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            return $user;
        }
    }
    return false;
}

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Function to check if user is admin
function isAdmin() {
    return (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1);
}

// Function to redirect to a URL
function redirect($url) {
    header("Location: $url");
    exit();
}

// Function to get all posts with pagination
function getPosts($limit = 10, $offset = 0, $published_only = true) {
    global $conn;
    $condition = $published_only ? "WHERE published = 1" : "";
    $query = "SELECT p.*, u.username 
              FROM posts p
              JOIN users u ON p.user_id = u.id
              $condition
              ORDER BY created_at DESC
              LIMIT $limit OFFSET $offset";
    $result = mysqli_query($conn, $query);
    
    $posts = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $posts[] = $row;
    }
    
    return $posts;
}

// Function to get a single post by ID or slug
function getPost($identifier, $by_slug = false, $admin_view = false) {
    global $conn;
    
    // Check if we want to get posts regardless of published status (for admin)
    $published_condition = $admin_view ? "" : "AND p.published = 1";
    
    // If we are looking by slug
    if ($by_slug) {
        $identifier = sanitize($identifier);  // Sanitize the slug to prevent SQL injection
        $query = "SELECT p.*, u.username 
                  FROM posts p
                  JOIN users u ON p.user_id = u.id
                  WHERE p.slug = '$identifier' $published_condition
                  LIMIT 1";
    } else {
        // If we are looking by ID
        $identifier = (int) $identifier;  // Ensure the identifier is an integer
        $query = "SELECT p.*, u.username 
                  FROM posts p
                  JOIN users u ON p.user_id = u.id
                  WHERE p.id = $identifier $published_condition
                  LIMIT 1";
    }
    
    // Execute the query
    $result = mysqli_query($conn, $query);
    
    // Check if we found a post
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);  // Return the post as an associative array
    }
    
    return null;  // If no post is found, return null
}

// Function to get categories
function getCategories() {
    global $conn;
    $query = "SELECT * FROM categories ORDER BY name";
    $result = mysqli_query($conn, $query);
    
    $categories = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row;
    }
    
    return $categories;
}

// Function to get categories for a post
function getPostCategories($post_id) {
    global $conn;
    $post_id = (int) $post_id;
    $query = "SELECT c.* 
              FROM categories c
              JOIN post_category pc ON c.id = pc.category_id
              WHERE pc.post_id = $post_id
              ORDER BY c.name";
    $result = mysqli_query($conn, $query);
    
    $categories = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row;
    }
    
    return $categories;
}

// Function to get comments for a post


// Function to upload image
function uploadImage($file) {
    $target_dir = "../assets/images/";
    $timestamp = time();
    $filename = $timestamp . '_' . basename($file['name']);
    $target_file = $target_dir . $filename;
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
    // Check if image file is a actual image or fake image
    $check = getimagesize($file['tmp_name']);
    if ($check === false) {
        return [
            'success' => false,
            'message' => 'File is not an image.'
        ];
    }
    
    // Check file size (limit to 2MB)
    if ($file['size'] > 2000000) {
        return [
            'success' => false,
            'message' => 'Sorry, your file is too large. Maximum size is 2MB.'
        ];
    }
    
    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        return [
            'success' => false,
            'message' => 'Sorry, only JPG, JPEG, PNG & GIF files are allowed.'
        ];
    }
    
    // Try to upload file
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        return [
            'success' => true,
            'filename' => $filename
        ];
    } else {
        return [
            'success' => false,
            'message' => 'Sorry, there was an error uploading your file.'
        ];
    }
}

// Function to display flash messages
function flashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = isset($_SESSION['flash_type']) ? $_SESSION['flash_type'] : 'info';
        
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
        
        return "<div class='alert alert-$type'>$message</div>";
    }
    return '';
}

// Function to set flash message
function setFlashMessage($message, $type = 'info') {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
}

