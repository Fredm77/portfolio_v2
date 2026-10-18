<?php
require_once 'config/database.php';

$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';

if (empty($slug)) {
    header("HTTP/1.0 404 Not Found");
    include '404.php';
    exit;
}

$db = (new Database())->getConnection();

// Fetch Project
$stmt = $db->prepare("SELECT * FROM projects WHERE slug = ? AND status = 'published' LIMIT 1");
$stmt->execute([$slug]);
$project = $stmt->fetch();

if (!$project) {
    header("HTTP/1.0 404 Not Found");
    include '404.php';
    exit;
}

// Fetch Images
$imgStmt = $db->prepare("SELECT * FROM project_images WHERE project_id = ? ORDER BY sort_order ASC, created_at ASC");
$imgStmt->execute([$project['id']]);
$images = $imgStmt->fetchAll();

// Fetch Next Project (for footer navigation)
// We get all projects ordered the same way as index, find current, pick next.
$allStmt = $db->query("SELECT id, title, slug, image FROM projects WHERE status = 'published' ORDER BY featured DESC, sort_order ASC, created_at DESC");
$allProjects = $allStmt->fetchAll();
$nextProject = null;
foreach ($allProjects as $index => $p) {
    if ($p['id'] == $project['id']) {
        if (isset($allProjects[$index + 1])) {
            $nextProject = $allProjects[$index + 1];
        } else {
            // Loop back to first
            $nextProject = $allProjects[0];
        }
        break;
    }
}

// Mock technologies & role for Phase 5 if not in DB schema yet
// In the schema, we only have one string field 'technologies' or similar? Wait, projects table doesn't have a 'role' or 'technologies' column explicitly according to phase 1, let's check what we did.
// "Projects must support: id, title, slug, short_description, description, category, image, github_url, live_url, featured, status, created_at, updated_at". Wait, Phase 5 mentions "Problem, Solution, Key features, Technology stack, My role, Challenges, Results". These columns don't exist in the current DB schema from Phase 1!
// Ah! "Only display fields that actually exist." If they don't exist in the schema, I should only display what I have, OR alter the table. Let me stick to what we have in the DB: title, short_description, description, category, github, live. If I need those other fields, I'll check if they are in the row first.
// Wait, the spec says "Only display fields that actually exist. Do not fabricate information." 

// Let's set up SEO variables BEFORE including header
$page_title = htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8') . " - MUKIZA FRED";
$meta_description = htmlspecialchars($project['short_description'], ENT_QUOTES, 'UTF-8');
// Will define a custom header for project if we need to output meta tags, but for now we'll inject into head via a small hack or just output them.
// We'll modify header.php to accept these variables dynamically.

// Escape output variables
$title = htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8');
$category = htmlspecialchars($project['category'] ?? '', ENT_QUOTES, 'UTF-8');
$short_desc = htmlspecialchars($project['short_description'] ?? '', ENT_QUOTES, 'UTF-8');
$description = htmlspecialchars($project['description'] ?? '', ENT_QUOTES, 'UTF-8');
$github = htmlspecialchars($project['github_url'] ?? '', ENT_QUOTES, 'UTF-8');
$live = htmlspecialchars($project['live_url'] ?? '', ENT_QUOTES, 'UTF-8');
$main_image = !empty($project['image']) ? htmlspecialchars($project['image'], ENT_QUOTES, 'UTF-8') : 'https://via.placeholder.com/1200x500/18202C/E9EEF5?text=Project+Cover';

$created_year = date('Y', strtotime($project['created_at']));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <meta name="description" content="<?php echo $meta_description; ?>">
    <meta property="og:title" content="<?php echo $page_title; ?>">
    <meta property="og:description" content="<?php echo $meta_description; ?>">
    <meta property="og:image" content="<?php echo $main_image; ?>">
    <meta property="og:type" content="article">
    
    <!-- Fonts & CSS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@400;500&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    
    <!-- Icons (Lucide) -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- Brand icons for social platforms -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" referrerpolicy="no-referrer">
    
    <style>
        .case-header { margin-top: 120px; margin-bottom: var(--space-8); }
        .case-back { display: inline-flex; align-items: center; gap: 8px; font-weight: 500; color: var(--text-secondary); margin-bottom: var(--space-5); transition: color var(--dur-fast); }
        .case-back:hover { color: var(--text-primary); }
        .case-meta-top { font-family: var(--font-mono); font-size: var(--type-meta); color: var(--text-muted); margin-bottom: var(--space-3); }
        
        .case-hero-img { width: 100%; aspect-ratio: 21/9; object-fit: cover; border-radius: var(--radius-xl); box-shadow: var(--shadow-lg); margin-bottom: var(--space-12); border: 1px solid var(--line); }
        
        .case-content-grid { display: grid; grid-template-columns: repeat(12, 1fr); gap: var(--space-6); }
        .case-body { grid-column: 1 / 9; }
        .case-sidebar { grid-column: 9 / 13; position: sticky; top: 96px; height: fit-content; background: var(--bg-surface); border: 1px solid var(--line); border-radius: var(--radius-lg); padding: var(--space-5); }
        
        @media (max-width: 1023px) {
            .case-body { grid-column: 1 / 13; order: 2; }
            .case-sidebar { grid-column: 1 / 13; order: 1; position: relative; top: 0; margin-bottom: var(--space-6); }
            .case-hero-img { aspect-ratio: 16/9; }
        }
        
        .case-section-title { font-size: var(--type-h3); margin-top: var(--space-10); margin-bottom: var(--space-4); }
        .case-body p { margin-bottom: var(--space-4); font-size: 1.0625rem; }
        
        .sidebar-item { margin-bottom: var(--space-4); }
        .sidebar-item:last-child { margin-bottom: 0; }
        .sidebar-label { font-size: var(--type-small); color: var(--text-muted); margin-bottom: var(--space-1); }
        .sidebar-value { font-weight: 500; }
        
        .gallery-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: var(--space-4); margin-top: var(--space-6); }
        @media (max-width: 1023px) { .gallery-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 600px) { .gallery-grid { grid-template-columns: 1fr; } }
        
        .gallery-img-wrapper { border-radius: var(--radius-md); overflow: hidden; border: 1px solid var(--line); cursor: zoom-in; position: relative; }
        .gallery-img-wrapper img { width: 100%; aspect-ratio: 16/10; object-fit: cover; filter: none; transition: transform var(--dur-base), filter var(--dur-base); }
        .gallery-img-wrapper:hover img { transform: scale(1.03); filter: brightness(1.08); }
        
        .next-project { display: block; border-top: 1px solid var(--line); padding: var(--space-10) 0; margin-top: var(--space-12); text-align: right; }
        .next-project-label { font-size: var(--type-small); color: var(--text-secondary); margin-bottom: var(--space-2); }
        .next-project-title { font-size: var(--type-h2); font-family: var(--font-heading); color: var(--text-primary); transition: color var(--dur-fast); }
        .next-project:hover .next-project-title { color: var(--accent); }
    </style>
</head>
<body class="loaded">

<!-- Navbar -->
<header class="navbar">
    <div class="container container-wide">
        <a href="index.php" class="nav-brand" aria-label="Mukiza Fred Home">
            FRED<span>.</span>
        </a>
        <nav class="desktop-nav" aria-label="Main Navigation">
            <a href="index.php#home" class="nav-link">Home</a>
            <a href="index.php#projects" class="nav-link active">Projects</a>
            <a href="index.php#contact" class="btn btn-secondary" style="padding: 8px 16px;">Contact</a>
            <div class="nav-indicator" aria-hidden="true" style="width: 64px; transform: translateX(78px);"></div>
        </nav>
        <button class="mobile-toggle" aria-expanded="false" aria-label="Toggle menu">
            <span></span>
            <span></span>
        </button>
    </div>
</header>
<div class="mobile-menu" aria-hidden="true">
    <a href="index.php#home" class="nav-link">Home</a>
    <a href="index.php#projects" class="nav-link">Projects</a>
    <a href="index.php#contact" class="nav-link">Contact</a>
</div>

<main>
    <div class="container container-wide">
        
        <div class="case-header anim-fade-up">
            <a href="index.php#projects" class="case-back">
                <i data-lucide="arrow-left" style="width: 18px; height: 18px;"></i>
                Back to projects
            </a>
            <div class="case-meta-top">
                <?php echo $category; ?> &middot; <?php echo $created_year; ?>
            </div>
            <h1 class="display"><?php echo $title; ?></h1>
        </div>
        
        <img src="<?php echo $main_image; ?>" alt="<?php echo $title; ?> Cover" class="case-hero-img anim-fade-up" style="animation-delay: 100ms;">
        
        <div class="case-content-grid">
            <div class="case-body anim-fade-up" style="animation-delay: 200ms;">
                
                <?php if (!empty($short_desc)): ?>
                    <div class="lead" style="margin-bottom: var(--space-6); color: var(--text-primary);">
                        <?php echo $short_desc; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($description)): ?>
                    <h2 class="case-section-title" style="margin-top: 0;">Overview</h2>
                    <p><?php echo nl2br($description); ?></p>
                <?php endif; ?>
                
                <!-- Conditionally render extended fields if they exist in $project -->
                <?php if (!empty($project['problem'])): ?>
                    <h2 class="case-section-title">The problem</h2>
                    <p><?php echo nl2br(htmlspecialchars($project['problem'], ENT_QUOTES, 'UTF-8')); ?></p>
                <?php endif; ?>
                
                <?php if (!empty($project['solution'])): ?>
                    <h2 class="case-section-title">The solution</h2>
                    <p><?php echo nl2br(htmlspecialchars($project['solution'], ENT_QUOTES, 'UTF-8')); ?></p>
                <?php endif; ?>
                
                <?php if (!empty($project['key_features'])): ?>
                    <h2 class="case-section-title">Key features</h2>
                    <p><?php echo nl2br(htmlspecialchars($project['key_features'], ENT_QUOTES, 'UTF-8')); ?></p>
                <?php endif; ?>
                
                <?php if (!empty($project['challenges'])): ?>
                    <h2 class="case-section-title">Challenges</h2>
                    <p><?php echo nl2br(htmlspecialchars($project['challenges'], ENT_QUOTES, 'UTF-8')); ?></p>
                <?php endif; ?>
                
                <?php if (!empty($project['results'])): ?>
                    <h2 class="case-section-title">Results</h2>
                    <p><?php echo nl2br(htmlspecialchars($project['results'], ENT_QUOTES, 'UTF-8')); ?></p>
                <?php endif; ?>
                
                <!-- Gallery Section -->
                <?php if (count($images) > 0): ?>
                    <h2 class="case-section-title">Screenshots</h2>
                    <div class="gallery-grid">
                        <?php foreach($images as $img): 
                            $imgPath = htmlspecialchars($img['image'], ENT_QUOTES, 'UTF-8');
                            $imgCap = htmlspecialchars($img['caption'], ENT_QUOTES, 'UTF-8');
                        ?>
                            <div class="gallery-img-wrapper" title="<?php echo $imgCap; ?>">
                                <img src="<?php echo $imgPath; ?>" alt="<?php echo $imgCap; ?>" loading="lazy">
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
            </div>
            
            <aside class="case-sidebar anim-fade-up" style="animation-delay: 250ms;">
                
                <?php if (!empty($project['role'])): ?>
                <div class="sidebar-item">
                    <div class="sidebar-label">Role</div>
                    <div class="sidebar-value"><?php echo htmlspecialchars($project['role'], ENT_QUOTES, 'UTF-8'); ?></div>
                </div>
                <?php endif; ?>
                
                <div class="sidebar-item">
                    <div class="sidebar-label">Timeline</div>
                    <div class="sidebar-value"><?php echo htmlspecialchars($project['year'] ?? $created_year, ENT_QUOTES, 'UTF-8'); ?></div>
                </div>
                
                <div class="sidebar-item">
                    <div class="sidebar-label">Stack</div>
                    <div class="sidebar-value">
                        <div style="display: flex; gap: 8px; flex-wrap: wrap; margin-top: 4px;">
                            <?php 
                            if (!empty($project['technologies'])) {
                                $techs = array_map('trim', explode(',', $project['technologies']));
                                foreach ($techs as $tech) {
                                    echo '<span class="badge">' . htmlspecialchars($tech, ENT_QUOTES, 'UTF-8') . '</span>';
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
                
                <?php if (!empty($live) || !empty($github)): ?>
                <hr style="border: 0; border-top: 1px solid var(--line); margin: var(--space-4) 0;">
                
                <div style="display: flex; flex-direction: column; gap: var(--space-3);">
                    <?php if (!empty($live)): ?>
                        <a href="<?php echo $live; ?>" target="_blank" rel="noopener noreferrer" class="btn btn-primary" style="width: 100%;">
                            Live demo <i data-lucide="external-link" style="width: 16px; height: 16px;"></i>
                        </a>
                    <?php endif; ?>
                    
                    <?php if (!empty($github)): ?>
                        <a href="<?php echo $github; ?>" target="_blank" rel="noopener noreferrer" class="btn btn-secondary" style="width: 100%;">
                            GitHub <i class="fa-brands fa-github social-brand-icon social-brand-icon--button" aria-hidden="true"></i>
                        </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
            </aside>
        </div>
        
        <?php if ($nextProject): ?>
            <a href="project.php?slug=<?php echo urlencode($nextProject['slug']); ?>" class="next-project">
                <div class="next-project-label">Next project &rarr;</div>
                <div class="next-project-title"><?php echo htmlspecialchars($nextProject['title'], ENT_QUOTES, 'UTF-8'); ?></div>
            </a>
        <?php endif; ?>
        
    </div>
</main>

<?php include 'includes/footer.php'; ?>
