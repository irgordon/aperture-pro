<?php
/** @var array $schemaIssues */
?>
<div class="wrap ap-wrap ap-health">
    <h1>Health</h1>

    <div class="ap-health-grid">
        <div class="ap-health-card">
            <h2>Schema</h2>
            <?php if (empty($schemaIssues)) : ?>
                <p class="ap-health-ok">All required tables and indexes are present.</p>
            <?php else : ?>
                <ul class="ap-health-issues">
                    <?php foreach ($schemaIssues as $issue) : ?>
                        <li><?php echo esc_html($issue); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <!-- You can add more cards: Jobs, Tokens, Cron, etc. -->
    </div>
</div>
