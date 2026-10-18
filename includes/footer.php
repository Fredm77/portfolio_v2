<?php
// includes/footer.php
require_once __DIR__ . '/../config/database.php';

$footerDb = (new Database())->getConnection();

$footerSocialLinks = [];
$footerSettings = [];

if ($footerDb) {
    // Fetch Social Links
    $fsStmt = $footerDb->query("SELECT platform, url FROM social_links WHERE is_visible = 1 AND url != '' AND url != '#' ORDER BY sort_order ASC");
    while ($row = $fsStmt->fetch()) {
        $footerSocialLinks[$row['platform']] = $row['url'];
    }

    // Fetch Settings
    $fsSetStmt = $footerDb->query("SELECT setting_key, setting_value FROM site_settings");
    while ($row = $fsSetStmt->fetch()) {
        $footerSettings[$row['setting_key']] = $row['setting_value'];
    }
}

$footerEmail = !empty($footerSettings['email']) ? $footerSettings['email'] : 'mukizafred7@gmail.com';
$footerPhone = !empty($footerSettings['phone']) ? $footerSettings['phone'] : '+250 792 982 882';

$footerSocialIconMap = [
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
?>
<footer class="section footer"
    style="background-color: var(--bg-inset); border-top: 1px solid var(--line); padding-top: var(--space-16); padding-bottom: var(--space-8);">
    <div class="container container-wide">
        <div class="footer-grid"
            style="display: grid; grid-template-columns: repeat(12, 1fr); gap: var(--space-6); margin-bottom: var(--space-10);">

            <!-- Column 1: Brand -->
            <div class="footer-col-brand" style="grid-column: span 4;">
                <h4 style="font-family: var(--font-heading); font-size: var(--type-h4); margin-bottom: 2px;">
                    MUKIZA FRED<span style="color: var(--accent);">.</span>
                </h4>
                <div class="text-muted text-small" style="margin-bottom: var(--space-4);">Full Stack Developer</div>
                <p class="text-secondary text-small" style="max-width: 35ch;">
                    Building practical digital solutions through modern web technologies, backend systems and
                    data-driven applications.
                </p>
            </div>

            <!-- Spacer for desktop -->
            <div class="footer-col-spacer" style="grid-column: span 2;"></div>

            <!-- Column 2: Navigation -->
            <div class="footer-col-nav" style="grid-column: span 2;">
                <h5
                    style="font-size: var(--type-small); font-weight: 600; color: var(--text-primary); margin-bottom: var(--space-4);">
                    Navigate</h5>
                <nav style="display: flex; flex-direction: column; gap: var(--space-3);">
                    <a href="index.php#home" class="footer-link">Home</a>
                    <a href="index.php#about" class="footer-link">About</a>
                    <a href="index.php#skills" class="footer-link">Skills</a>
                    <a href="index.php#projects" class="footer-link">Projects</a>
                    <a href="index.php#experience" class="footer-link">Experience</a>
                    <a href="index.php#contact" class="footer-link">Contact</a>
                </nav>
            </div>

            <!-- Column 3: Contact -->
            <div class="footer-col-contact" style="grid-column: span 2;">
                <h5
                    style="font-size: var(--type-small); font-weight: 600; color: var(--text-primary); margin-bottom: var(--space-4);">
                    Contact</h5>
                <nav style="display: flex; flex-direction: column; gap: var(--space-3);">
                    <a href="mailto:<?php echo htmlspecialchars($footerEmail, ENT_QUOTES, 'UTF-8'); ?>"
                        class="footer-link" style="display: flex; align-items: center; gap: 8px;">
                        <i data-lucide="mail" style="width: 14px; height: 14px;"></i>
                        <span
                            style="word-break: break-all;"><?php echo htmlspecialchars($footerEmail, ENT_QUOTES, 'UTF-8'); ?></span>
                    </a>
                    <a href="tel:<?php echo htmlspecialchars(str_replace(' ', '', $footerPhone), ENT_QUOTES, 'UTF-8'); ?>"
                        class="footer-link" style="display: flex; align-items: center; gap: 8px;">
                        <i data-lucide="phone" style="width: 14px; height: 14px;"></i>
                        <span><?php echo htmlspecialchars($footerPhone, ENT_QUOTES, 'UTF-8'); ?></span>
                    </a>
                </nav>
            </div>

            <!-- Column 4: Connect -->
            <div class="footer-col-social" style="grid-column: span 2;">
                <h5
                    style="font-size: var(--type-small); font-weight: 600; color: var(--text-primary); margin-bottom: var(--space-4);">
                    Connect</h5>
                <nav style="display: flex; flex-direction: column; gap: var(--space-3);">
                    <?php foreach ($footerSocialLinks as $platform => $url):
                        if ($platform === 'Email')
                            continue; // Handled in Contact column
                        $icon = isset($footerSocialIconMap[$platform]) ? $footerSocialIconMap[$platform] : 'fa-solid fa-arrow-up-right-from-square';
                        ?>
                        <a href="<?php echo htmlspecialchars($url, ENT_QUOTES, 'UTF-8'); ?>" target="_blank"
                            rel="noopener noreferrer" class="footer-link"
                            style="display: flex; align-items: center; gap: 8px;"
                            aria-label="<?php echo htmlspecialchars($platform, ENT_QUOTES, 'UTF-8'); ?>">
                            <i class="<?php echo $icon; ?> social-brand-icon social-brand-icon--footer" aria-hidden="true"></i>
                            <span><?php echo htmlspecialchars($platform, ENT_QUOTES, 'UTF-8'); ?></span>
                        </a>
                    <?php endforeach; ?>
                </nav>
            </div>

        </div>

        <hr style="border: 0; border-top: 1px solid var(--line); margin-bottom: var(--space-5);">

        <div class="footer-bottom"
            style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: var(--space-4);">
            <div class="text-small text-muted">
                &copy; <?php echo date('Y'); ?> MUKIZA FRED. All rights reserved.
            </div>

            <!-- Built with tech credit -->
            <div class="text-small text-muted" style="display: flex; align-items: center; gap: 6px;">
                Built with
                <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor" style="opacity: 0.7;">
                    <path
                        d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10zm-1-11v4h2v-4h-2zm-4 0v4h2v-4H7zm8 0v4h2v-4h-2z" />
                </svg>
                PHP and
                <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor" style="opacity: 0.7;">
                    <ellipse cx="12" cy="6" rx="8" ry="3" />
                    <path d="M4 6v6c0 1.657 3.582 3 8 3s8-1.343 8-3V6M4 12v6c0 1.657 3.582 3 8 3s8-1.343 8-3v-6" />
                </svg>
                MySQL
            </div>

            <!-- Back to top -->
            <button onclick="window.scrollTo({top: 0, behavior: 'smooth'})" class="btn-ghost back-to-top text-small"
                style="padding: 4px 12px; margin: 0; display: inline-flex; gap: 6px;" aria-label="Back to top">
                Back to top <i data-lucide="arrow-up"
                    style="width: 14px; height: 14px; transition: transform var(--dur-fast);"></i>
            </button>
        </div>
    </div>
</footer>

<style>
    .footer-link {
        font-size: var(--type-small);
        color: var(--text-secondary);
        text-decoration: none;
        transition: color var(--dur-fast);
        display: inline-flex;
        width: fit-content;
    }

    .footer-link:hover {
        color: var(--text-primary);
        text-decoration: underline;
        text-decoration-color: var(--accent);
        text-underline-offset: 4px;
    }

    .back-to-top:hover i {
        transform: translateY(-2px);
        color: var(--text-primary);
    }

    /* Responsive Footer */
    @media (max-width: 1023px) {
        .footer-grid {
            grid-template-columns: repeat(6, 1fr) !important;
        }

        .footer-col-brand {
            grid-column: span 6 !important;
            margin-bottom: var(--space-4);
        }

        .footer-col-spacer {
            display: none;
        }

        .footer-col-nav,
        .footer-col-contact,
        .footer-col-social {
            grid-column: span 2 !important;
        }
    }

    @media (max-width: 639px) {
        .footer-grid {
            grid-template-columns: 1fr !important;
        }

        .footer-col-brand,
        .footer-col-nav,
        .footer-col-contact,
        .footer-col-social {
            grid-column: span 1 !important;
            margin-bottom: var(--space-4);
        }

        .footer-col-brand {
            margin-bottom: var(--space-6);
        }

        .footer-bottom {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

<!-- JavaScript -->
<script src="assets/js/main.js"></script>
</body>

</html>
