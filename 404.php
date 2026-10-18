<?php
http_response_code(404);
$page_title = "404 Not Found - MUKIZA FRED";
include 'includes/header.php';
?>

<main style="min-height: 80svh; display: flex; align-items: center; justify-content: center; text-align: center;">
    <div class="container container-narrow">
        <h1 class="display" style="margin-bottom: var(--space-4);">404</h1>
        <p class="lead" style="margin: 0 auto var(--space-6);">That page doesn't exist.</p>
        <div style="display: flex; gap: var(--space-4); justify-content: center;">
            <a href="index.php" class="btn btn-primary">Back to home</a>
            <a href="index.php#projects" class="btn btn-secondary">View projects</a>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>

