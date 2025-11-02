<?php
$title = '404 - ページが見つかりません';
ob_start();
?>

<div class="text-center py-5">
    <i class="bi bi-exclamation-triangle-fill text-warning" style="font-size: 5rem;"></i>
    <h1 class="mt-4">404 - ページが見つかりません</h1>
    <p class="lead text-muted">お探しのページは存在しないか、移動された可能性があります。</p>
    <div class="mt-4">
        <a href="<?= UrlHelper::url('dashboard') ?>" class="btn btn-primary me-2">
            <i class="bi bi-house-door me-2"></i>
            ダッシュボードに戻る
        </a>
        <a href="javascript:history.back()" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>
            前のページに戻る
        </a>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>