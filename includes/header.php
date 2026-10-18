<?php
// includes/header.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'MUKIZA FRED - Full Stack Developer'; ?></title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    
    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <!-- Icons (Lucide) -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- Brand icons for social platforms -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" referrerpolicy="no-referrer">
</head>
<body class="loaded">

<!-- Navbar -->
<header class="navbar">
    <div class="container container-wide">
        <a href="#home" class="nav-brand" aria-label="Mukiza Fred Home">
            FRED<span>.</span>
        </a>
        
        <!-- Desktop Nav -->
        <nav class="desktop-nav" aria-label="Main Navigation">
            <a href="#home" class="nav-link">Home</a>
            <a href="#about" class="nav-link">About</a>
            <a href="#skills" class="nav-link">Skills</a>
            <a href="#projects" class="nav-link">Projects</a>
            <a href="#experience" class="nav-link">Experience</a>
            <a href="#contact" class="btn btn-secondary" style="padding: 8px 16px;">Contact</a>
            <div class="nav-indicator" aria-hidden="true"></div>
        </nav>

        <!-- Mobile Toggle -->
        <button class="mobile-toggle" aria-expanded="false" aria-label="Toggle menu">
            <span></span>
            <span></span>
        </button>
    </div>
</header>

<!-- Mobile Menu Overlay -->
<div class="mobile-menu" aria-hidden="true">
    <a href="#home" class="nav-link">Home</a>
    <a href="#about" class="nav-link">About</a>
    <a href="#skills" class="nav-link">Skills</a>
    <a href="#projects" class="nav-link">Projects</a>
    <a href="#experience" class="nav-link">Experience</a>
    <a href="#contact" class="nav-link">Contact</a>
</div>
