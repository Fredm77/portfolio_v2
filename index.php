<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/database.php';
$db = (new Database())->getConnection();

// Fetch Projects
$projects = [];
$categories = [];
if ($db) {
    // Fetch unique categories for filter
    $catStmt = $db->query("SELECT DISTINCT category FROM projects WHERE status = 'published' AND category IS NOT NULL AND category != ''");
    while ($row = $catStmt->fetch()) {
        $categories[] = $row['category'];
    }
    
    // Fetch projects
    $projStmt = $db->query("SELECT * FROM projects WHERE status = 'published' ORDER BY featured DESC, sort_order ASC, created_at DESC");
    while ($row = $projStmt->fetch()) {
        $row['tags'] = !empty($row['technologies']) ? array_map('trim', explode(',', $row['technologies'])) : [];
        $projects[] = $row;
    }
}

// Fetch Skills
$skillsByCategory = [];
if ($db) {
    $skillStmt = $db->query("SELECT s.name, s.icon_reference, c.name as category_name 
                             FROM skills s 
                             JOIN skill_categories c ON s.category_id = c.id 
                             ORDER BY c.sort_order ASC, s.sort_order ASC");
    while ($row = $skillStmt->fetch()) {
        $skillsByCategory[$row['category_name']][] = $row;
    }
}

// Fetch Experiences
$experiences = [];
if ($db) {
    $expStmt = $db->query("SELECT * FROM experiences ORDER BY sort_order ASC, start_date DESC");
    $experiences = $expStmt->fetchAll();
}

// Start session for CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Fetch Education
$education = [];
if ($db) {
    $eduStmt = $db->query("SELECT * FROM education ORDER BY sort_order ASC, start_year DESC");
    $education = $eduStmt->fetchAll();
}

// Fetch Social Links
$socialLinks = [];
if ($db) {
    $socialStmt = $db->query("SELECT platform, url FROM social_links WHERE is_visible = 1 AND url != '' AND url != '#' ORDER BY sort_order ASC");
    while ($row = $socialStmt->fetch()) {
        $socialLinks[$row['platform']] = $row['url'];
    }
}

// Fetch Site Settings
$settings = [];
if ($db) {
    $setStmt = $db->query("SELECT setting_key, setting_value FROM site_settings");
    while ($row = $setStmt->fetch()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
}

// Fallbacks for specific Phase 7 requirements if not in settings DB yet
$contactEmail = !empty($settings['email']) ? $settings['email'] : 'mukizafred7@gmail.com';
$contactPhone = !empty($settings['phone']) ? $settings['phone'] : '+250 792 982 882';
$youtubeSubText = !empty($settings['youtube_subscriber_text']) ? $settings['youtube_subscriber_text'] : 'More than 5,000 people subscribe to my channel, where I cover development topics and tools.';
$heroImage = !empty($settings['profile_image']) ? $settings['profile_image'] : 'https://via.placeholder.com/440x550/111721/E9EEF5?text=Profile+Image';
$heroImage = filter_var($heroImage, FILTER_VALIDATE_URL) ? $heroImage : ltrim($heroImage, '/');
$heroCv = !empty($settings['cv_file']) ? ltrim($settings['cv_file'], '/') : '';

?>
<?php include 'includes/header.php'; ?>

<main>
    <!-- HERO SECTION -->
    <section id="home" class="hero">
        <div class="hero-bg"></div>
        <div class="container container-wide grid-12 hero-layout">
            
            <div class="hero-content">
                <div class="seq-1">
                    <span class="status-dot"></span>
                    <span class="text-meta text-muted">Rwanda · Available for work</span>
                </div>
                
                <h1 class="display" style="margin-top: var(--space-4);">
                    <div class="seq-2" style="transform: translateY(24px);">MUKIZA</div>
                    <div class="seq-3" style="transform: translateY(24px);">FRED</div>
                </h1>
                
                <h3 class="seq-4 text-secondary" style="margin-top: var(--space-2); transform: translateY(16px);">Full Stack Developer</h3>
                
                <p class="seq-5 lead" style="margin-top: var(--space-4); transform: translateY(16px);">
                    I build practical digital solutions through modern web technologies, backend systems and data-driven applications.
                </p>
                
                <div class="hero-actions seq-6" style="transform: translateY(16px);">
                    <a href="#projects" class="btn btn-primary">View my projects</a>
                    <a href="#contact" class="btn btn-secondary">Let's work together</a>
                 
                </div>
            </div>

            <div class="hero-image-wrapper">
                <div style="display: flex; flex-direction: column; align-items: center; gap: var(--space-4);">
                    <img src="<?php echo htmlspecialchars($heroImage, ENT_QUOTES, 'UTF-8'); ?>" alt="Mukiza Fred" class="hero-portrait" width="440" height="550" loading="eager" fetchpriority="high">
                    
                    <div class="tech-strip">
                        <div class="tech-item"><svg viewBox="0 0 24 24"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10zm0-2c-4.418 0-8-3.582-8-8s3.582-8 8-8 8 3.582 8 8-3.582 8-8 8zm0-3.5a4.5 4.5 0 1 1 0-9 4.5 4.5 0 0 1 0 9zm0-2a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z"/></svg>React</div>
                        <div class="tech-item"><svg viewBox="0 0 24 24"><path d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10zm-1-11v4h2v-4h-2zm-4 0v4h2v-4H7zm8 0v4h2v-4h-2z"/></svg>PHP</div>
                        <div class="tech-item"><svg viewBox="0 0 24 24"><path d="M12 22l-8-4-2-6 3-7 7-3 7 3 3 7-2 6-8 4zm0-2.3l6.5-3.3 1.6-4.9-2.5-5.7L12 3.7l-5.6 2.4L3.9 11.8l1.6 4.9L12 19.7z"/></svg>Node.js</div>
                        <div class="tech-item"><svg viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>Python</div>
                        <div class="tech-item"><svg viewBox="0 0 24 24"><ellipse cx="12" cy="6" rx="8" ry="3"/><path d="M4 6v6c0 1.657 3.582 3 8 3s8-1.343 8-3V6M4 12v6c0 1.657 3.582 3 8 3s8-1.343 8-3v-6"/></svg>MySQL</div>
                    </div>
                </div>
            </div>
            
        </div>
    </section>

    <!-- ABOUT SECTION -->
    <section id="about" class="section">
        <div class="container container-wide">
            <h2 class="section-header">About</h2>
            <div class="about-grid">
                <div class="about-copy">
                    <p class="about-lead">I'm a software developer from Rwanda focused on building practical web systems that make everyday work clearer and more reliable.</p>
                    <p>I work across the interface, backend logic, and database structure underneath. My projects have included an agricultural marketplace, reporting tools, and registration systems where a thoughtful data model matters as much as the screen.</p>
                    <p>I'm continuing to deepen my craft through hands-on work with modern web technologies, learning from each project and keeping the solutions useful, maintainable, and grounded in a real need.</p>
                </div>
                <aside class="about-panel" aria-label="Development focus">
                    <div class="about-panel__block">
                        <span class="text-meta text-muted">Development focus</span>
                        <strong>Software and web applications</strong>
                    </div>
                    <div class="about-panel__block">
                        <span class="text-meta text-muted">Based in</span>
                        <strong>Rwanda</strong>
                    </div>
                    <div class="about-panel__block">
                        <span class="text-meta text-muted">Working with</span>
                        <div class="about-tech-list">
                            <?php
                            $aboutTechnologies = [
                                ['name' => 'HTML', 'icon' => 'html5'], ['name' => 'CSS', 'icon' => 'css3'],
                                ['name' => 'JavaScript', 'icon' => 'javascript'], ['name' => 'PHP', 'icon' => 'php'],
                                ['name' => 'MySQL', 'icon' => 'mysql'], ['name' => 'SQL', 'icon' => 'sqlite'],
                                ['name' => 'React', 'icon' => 'react'], ['name' => 'Laravel', 'icon' => 'laravel']
                            ];
                            foreach ($aboutTechnologies as $technology):
                            ?>
                                <span class="about-tech-item"><img src="https://cdn.simpleicons.org/<?php echo $technology['icon']; ?>/9AA7B8" alt="" aria-hidden="true" loading="lazy"> <?php echo $technology['name']; ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    <!-- SKILLS SECTION -->
    <section id="skills" class="section">
        <div class="container container-wide">
            <h2 class="section-header anim-fade-up">Skills</h2>
            
            <?php if (!empty($skillsByCategory)): ?>
            <div class="grid-12">
                <?php 
                $delay = 0;
                foreach ($skillsByCategory as $category => $items): 
                    $colSpan = (count($items) > 3) ? 3 : 3; // For responsive layout, they fit 4 in a row on desktop. Grid is 12. 3x4 = 12.
                ?>
                    <div class="skills-panel anim-fade-in" style="grid-column: span 3; animation-delay: <?php echo $delay; ?>ms;">
                        <h3 class="skills-category-title"><?php echo htmlspecialchars($category, ENT_QUOTES, 'UTF-8'); ?></h3>
                        <div>
                            <?php foreach ($items as $skill): ?>
                                <div class="skill-item">
                                    <?php $skillIcon = ['css3' => 'css', 'java' => 'openjdk'][$skill['icon_reference']] ?? $skill['icon_reference']; ?>
                                    <img src="https://cdn.simpleicons.org/<?php echo urlencode($skillIcon); ?>/9AA7B8" alt="" class="skill-icon" aria-hidden="true" loading="lazy">
                                    <span class="skill-name"><?php echo htmlspecialchars($skill['name'], ENT_QUOTES, 'UTF-8'); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php 
                    $delay += 100;
                endforeach; ?>
            </div>
            <?php else: ?>
                <div class="empty-state">
                    <i data-lucide="folder-open" class="empty-state-icon" style="width:40px;height:40px;"></i>
                    <h4>No skills added yet</h4>
                </div>
            <?php endif; ?>
            
        </div>
    </section>

    <!-- PROJECTS SECTION -->
    <section id="projects" class="section">
        <div class="container container-wide">
            <h2 class="section-header">Projects</h2>
            
            <?php if (!empty($projects)): ?>
                
                <?php if (count($categories) > 0): ?>
                <div class="filter-bar">
                    <button class="filter-pill active" data-filter="all">All</button>
                    <?php foreach ($categories as $cat): ?>
                        <button class="filter-pill" data-filter="<?php echo htmlspecialchars($cat, ENT_QUOTES, 'UTF-8'); ?>">
                            <?php echo htmlspecialchars($cat, ENT_QUOTES, 'UTF-8'); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <div class="grid-12" id="project-grid">
                    <?php foreach ($projects as $index => $project): 
                        $isFeatured = ($project['featured'] == 1 && $index === 0);
                        $cardClass = $isFeatured ? 'project-card project-featured' : 'project-card';
                        
                        $title = htmlspecialchars($project['title'] ?? '', ENT_QUOTES, 'UTF-8');
                        $desc = htmlspecialchars($project['short_description'] ?? '', ENT_QUOTES, 'UTF-8');
                        $cat = htmlspecialchars($project['category'] ?? '', ENT_QUOTES, 'UTF-8');
                        
                        $imageValue = !empty($project['image']) ? $project['image'] : '';
                        $image = $imageValue !== '' ? htmlspecialchars(filter_var($imageValue, FILTER_VALIDATE_URL) ? $imageValue : ltrim($imageValue, '/'), ENT_QUOTES, 'UTF-8') : '';
                        $github = !empty($project['github_url']) ? htmlspecialchars($project['github_url'], ENT_QUOTES, 'UTF-8') : null;
                        $live = !empty($project['live_url']) ? htmlspecialchars($project['live_url'], ENT_QUOTES, 'UTF-8') : null;
                        
                        $detailsUrl = "project.php?slug=" . urlencode($project['slug']);
                    ?>
                        <article class="<?php echo $cardClass; ?> anim-fade-up" style="animation-delay: <?php echo min($index * 60, 360); ?>ms" data-category="<?php echo $cat; ?>">
                            <a href="<?php echo $detailsUrl; ?>" class="project-img-wrapper" aria-hidden="true" tabindex="-1">
                                <?php if ($image): ?>
                                    <img src="<?php echo $image; ?>" alt="<?php echo $title; ?>" class="project-card-img" loading="lazy">
                                <?php else: ?>
                                    <span class="project-card-img project-card-img--empty" role="img" aria-label="<?php echo $title; ?> image unavailable"><span><?php echo $title; ?></span></span>
                                <?php endif; ?>
                                <div class="project-img-overlay"></div>
                            </a>
                            
                            <div class="project-content">
                                <div class="project-meta">
                                    <?php if ($isFeatured): ?>
                                        <span class="project-featured-badge">Featured</span>
                                    <?php endif; ?>
                                    <span class="project-category"><?php echo $cat; ?></span>
                                    <?php if ($live): ?>
                                        <span class="project-status-dot live" title="Live"></span>
                                    <?php else: ?>
                                        <span class="project-status-dot" title="Archived or Development"></span>
                                    <?php endif; ?>
                                </div>
                                
                                <h3 class="project-title"><?php echo $title; ?></h3>
                                <p class="project-desc"><?php echo $desc; ?></p>
                                
                                <div class="project-tags">
                                    <?php 
                                    $tagCount = 0;
                                    foreach ($project['tags'] as $tag): 
                                        if ($tagCount >= 3) break;
                                    ?>
                                        <span class="badge"><?php echo htmlspecialchars($tag, ENT_QUOTES, 'UTF-8'); ?></span>
                                    <?php 
                                        $tagCount++;
                                    endforeach; 
                                    if (count($project['tags']) > 3):
                                    ?>
                                        <span class="badge">+<?php echo count($project['tags']) - 3; ?></span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="project-actions">
                                    <a href="<?php echo $detailsUrl; ?>" class="project-details-link">
                                        View details <i data-lucide="arrow-right" style="width:16px;height:16px;"></i>
                                    </a>
                                    <div class="project-icon-links">
                                        <?php if ($github): ?>
                                            <a href="<?php echo $github; ?>" target="_blank" rel="noopener noreferrer" class="project-action-icon" aria-label="GitHub Repository">
                                                <i class="fa-brands fa-github social-brand-icon social-brand-icon--project" aria-hidden="true"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($live): ?>
                                            <a href="<?php echo $live; ?>" target="_blank" rel="noopener noreferrer" class="project-action-icon" aria-label="Live Demo">
                                                <i data-lucide="external-link" style="width:18px;height:18px;"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
                
                <div id="no-projects-msg" class="empty-state" style="display: none;">
                    <i data-lucide="folder-open" class="empty-state-icon" style="width:40px;height:40px;"></i>
                    <h4>No projects in this category</h4>
                    <button class="btn btn-ghost" onclick="resetFilter()" style="margin-top: 8px;">Show all projects</button>
                </div>

            <?php else: ?>
                <div class="empty-state">
                    <i data-lucide="folder-open" class="empty-state-icon" style="width:40px;height:40px;"></i>
                    <h4>No projects yet</h4>
                    <p class="text-small">Projects added from the dashboard appear here.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- EXPERIENCE SECTION -->
    <section id="experience" class="section">
        <div class="container container-narrow">
            <h2 class="section-header anim-fade-up">Experience</h2>
            
            <?php if (!empty($experiences)): ?>
                <ol class="timeline">
                    <div class="timeline-end-fade"></div>
                    <?php foreach ($experiences as $index => $exp): 
                        $isCurrent = ($index === 0);
                        $org = htmlspecialchars($exp['organization'], ENT_QUOTES, 'UTF-8');
                        $pos = htmlspecialchars($exp['position'], ENT_QUOTES, 'UTF-8');
                        $desc = !empty($exp['description']) ? htmlspecialchars($exp['description'], ENT_QUOTES, 'UTF-8') : null;
                        
                        $startYear = !empty($exp['start_date']) ? date('Y', strtotime($exp['start_date'])) : null;
                        $endYear = !empty($exp['end_date']) ? date('Y', strtotime($exp['end_date'])) : 'Present';
                        
                        $dateStr = '';
                        if ($startYear) {
                            $dateStr = $startYear . ' &mdash; ' . $endYear;
                        } else {
                            // If dates are null as per prompt "Do not invent dates" just show current year for first, or nothing. 
                            // The prompt actually didn't want invented dates, so I'll only show if they exist.
                        }
                    ?>
                        <li class="timeline-item <?php echo $isCurrent ? 'is-current' : ''; ?> anim-fade-up">
                            <div class="timeline-node"></div>
                            <div class="timeline-content">
                                <div class="timeline-header-row">
                                    <h3 class="timeline-role"><?php echo $pos; ?></h3>
                                    <?php if ($dateStr): ?>
                                        <div class="timeline-dates"><?php echo $dateStr; ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="timeline-org"><?php echo $org; ?></div>
                                
                                <?php if ($desc): ?>
                                    <p class="timeline-desc"><?php echo nl2br($desc); ?></p>
                                <?php endif; ?>
                                
                                <?php if (!empty($exp['technologies'])): ?>
                                    <div class="project-tags" style="margin-top: var(--space-3); margin-bottom: 0;">
                                        <?php 
                                        $techs = explode(',', $exp['technologies']);
                                        foreach ($techs as $tech): ?>
                                            <span class="badge"><?php echo htmlspecialchars(trim($tech), ENT_QUOTES, 'UTF-8'); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ol>
            <?php else: ?>
                <div class="empty-state">
                    <i data-lucide="briefcase" class="empty-state-icon" style="width:40px;height:40px;"></i>
                    <h4>No experience added yet</h4>
                </div>
            <?php endif; ?>
        </div>
    </section>
    
    <!-- EDUCATION SECTION -->
    <section id="education" class="section" style="padding-top: 0;">
        <div class="container container-narrow">
            <h2 class="section-header anim-fade-up" style="margin-bottom: var(--space-4);">Education</h2>
            
            <?php if (!empty($education)): ?>
                <div>
                    <?php foreach ($education as $edu): 
                        $inst = htmlspecialchars($edu['institution'], ENT_QUOTES, 'UTF-8');
                        $qual = htmlspecialchars($edu['qualification'], ENT_QUOTES, 'UTF-8');
                    ?>
                        <div class="edu-item anim-fade-up">
                            <div class="edu-institution"><?php echo $inst; ?></div>
                            <div class="edu-qualification"><?php echo $qual; ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state" style="padding: var(--space-4) 0;">
                    <p class="text-small">No education entries added.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- YOUTUBE BAND -->
    <?php if (isset($socialLinks['YouTube']) && !empty($socialLinks['YouTube'])): ?>
    <section class="section" style="padding: var(--space-12) 0;">
        <div class="container container-wide">
            <div style="background: var(--bg-surface); border-radius: var(--radius-lg); border: 1px solid var(--line); padding: var(--space-10); display: flex; flex-direction: column; align-items: flex-start; max-width: 900px; margin: 0 auto;">
                <div style="display: flex; align-items: center; gap: var(--space-3); color: var(--text-secondary); margin-bottom: var(--space-3);">
                        <i class="fa-brands fa-youtube social-brand-icon social-brand-icon--youtube" aria-hidden="true"></i>
                    <span class="text-meta">BEYOND CODE</span>
                </div>
                <h3 style="font-size: var(--type-h3); margin-bottom: var(--space-4);">I also make technology content</h3>
                <p class="text-secondary" style="font-size: var(--type-body); margin-bottom: var(--space-6); max-width: 65ch;">
                    <?php echo htmlspecialchars($youtubeSubText, ENT_QUOTES, 'UTF-8'); ?>
                </p>
                <a href="<?php echo htmlspecialchars($socialLinks['YouTube'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-secondary">
                    Visit the channel <i data-lucide="arrow-up-right" style="width: 16px; height: 16px;"></i>
                </a>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- CONTACT SECTION -->
    <section id="contact" class="section">
        <div class="container container-wide">
            <div class="grid-12">
                <!-- Left Column -->
                <div style="grid-column: span 5; padding-right: var(--space-6);">
                    <h2 class="display" style="font-size: var(--type-h2); margin-bottom: var(--space-4);">Let's work together</h2>
                    <p class="lead" style="margin-bottom: var(--space-8);">I'm open to freelance work, collaborations and full-time roles.</p>
                    
                    <div style="display: flex; flex-direction: column; gap: var(--space-4); margin-bottom: var(--space-8);">
                        <div style="display: flex; align-items: center; gap: var(--space-3);">
                            <i data-lucide="mail" style="width: 20px; height: 20px; color: var(--text-muted);"></i>
                            <a href="mailto:<?php echo htmlspecialchars($contactEmail, ENT_QUOTES, 'UTF-8'); ?>" class="link-underline" style="font-weight: 500;"><?php echo htmlspecialchars($contactEmail, ENT_QUOTES, 'UTF-8'); ?></a>
                            <button class="btn-ghost" onclick="navigator.clipboard.writeText('<?php echo htmlspecialchars($contactEmail, ENT_QUOTES, 'UTF-8'); ?>'); alert('Email copied!');" aria-label="Copy email" style="padding: 4px;">
                                <i data-lucide="copy" style="width: 16px; height: 16px; color: var(--text-muted);"></i>
                            </button>
                        </div>
                        <div style="display: flex; align-items: center; gap: var(--space-3);">
                            <i data-lucide="phone" style="width: 20px; height: 20px; color: var(--text-muted);"></i>
                            <a href="tel:<?php echo htmlspecialchars(str_replace(' ', '', $contactPhone), ENT_QUOTES, 'UTF-8'); ?>" class="link-underline" style="font-weight: 500;"><?php echo htmlspecialchars($contactPhone, ENT_QUOTES, 'UTF-8'); ?></a>
                        </div>
                    </div>
                    
                    <hr style="border: 0; border-top: 1px solid var(--line); margin-bottom: var(--space-6);">
                    
                    <div style="display: flex; gap: var(--space-3);">
                        <?php 
                        $socialIconMap = [
                            'GitHub' => 'fa-brands fa-github',
                            'LinkedIn' => 'fa-brands fa-linkedin',
                            'Instagram' => 'fa-brands fa-instagram',
                            'Facebook' => 'fa-brands fa-facebook',
                            'X (Twitter)' => 'fa-brands fa-x-twitter',
                            'Twitter' => 'fa-brands fa-x-twitter',
                            'YouTube' => 'fa-brands fa-youtube',
                            'Telegram' => 'fa-brands fa-telegram',
                            'WhatsApp' => 'fa-brands fa-whatsapp',
                        ];
                        foreach ($socialLinks as $platform => $url): 
                            if ($platform === 'YouTube' || $platform === 'Email') continue; // Handled separately
                            $icon = isset($socialIconMap[$platform]) ? $socialIconMap[$platform] : 'fa-solid fa-arrow-up-right-from-square';
                        ?>
                            <a href="<?php echo htmlspecialchars($url, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer" class="social-icon-btn" aria-label="<?php echo htmlspecialchars($platform, ENT_QUOTES, 'UTF-8'); ?>">
                                <i class="<?php echo $icon; ?> social-brand-icon" aria-hidden="true"></i>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Right Column (Form) -->
                <div style="grid-column: span 7;">
                    <div id="form-message-container" style="display: none; padding: var(--space-4); margin-bottom: var(--space-5); border-radius: var(--radius-md);">
                        <div style="display: flex; align-items: flex-start; gap: var(--space-3);">
                            <i id="form-message-icon" data-lucide="info" style="width: 20px; height: 20px; margin-top: 2px;"></i>
                            <div>
                                <strong id="form-message-title" style="display: block; margin-bottom: 4px;"></strong>
                                <span id="form-message-body" class="text-small"></span>
                            </div>
                        </div>
                    </div>

                    <form id="contact-form" action="process_contact.php" method="POST" novalidate>
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <!-- Honeypot -->
                        <div style="display:none;" aria-hidden="true">
                            <label for="website">Website</label>
                            <input type="text" name="website" id="website" tabindex="-1" autocomplete="off">
                        </div>
                        
                        <div class="form-group">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" id="name" name="name" class="form-control" placeholder="Your name" required autocomplete="name">
                            <span class="error-msg text-small" style="color: var(--danger); display: none; margin-top: 4px;"></span>
                        </div>
                        
                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email" class="form-control" placeholder="your@email.com" required autocomplete="email">
                            <span class="error-msg text-small" style="color: var(--danger); display: none; margin-top: 4px;"></span>
                        </div>
                        
                        <div class="form-group">
                            <label for="subject" class="form-label">Subject</label>
                            <input type="text" id="subject" name="subject" class="form-control" placeholder="What is this regarding?">
                        </div>
                        
                        <div class="form-group">
                            <label for="message" class="form-label">Message</label>
                            <textarea id="message" name="message" class="form-control" rows="5" placeholder="Your message here..." required style="resize: vertical;"></textarea>
                            <span class="error-msg text-small" style="color: var(--danger); display: none; margin-top: 4px;"></span>
                        </div>
                        
                        <div style="display: flex; justify-content: flex-end; margin-top: var(--space-6);">
                            <button type="submit" id="submit-btn" class="btn btn-primary">
                                <span>Send message</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

</main>

<?php include 'includes/footer.php'; ?>
