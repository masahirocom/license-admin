<?php
$title = 'ダッシュボード - License Admin Panel';
ob_start();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-house-door me-2"></i>
        ダッシュボード
    </h1>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card card-stats border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-muted mb-2">総ライセンス数</h6>
                        <h3 class="mb-0"><?= number_format($stats['total_licenses']) ?></h3>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-key text-primary fs-2"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card card-stats border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-muted mb-2">有効ライセンス</h6>
                        <h3 class="mb-0 text-success"><?= number_format($stats['active_licenses']) ?></h3>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-check-circle text-success fs-2"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card card-stats border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-muted mb-2">期限切れライセンス</h6>
                        <h3 class="mb-0 text-danger"><?= number_format($stats['expired_licenses']) ?></h3>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-x-circle text-danger fs-2"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card card-stats border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="card-title text-muted mb-2">総デバイス数</h6>
                        <h3 class="mb-0"><?= number_format($stats['total_devices']) ?></h3>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-laptop text-info fs-2"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-lightning me-2"></i>
                    クイックアクション
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <a href="<?= UrlHelper::url('licenses/create') ?>" class="btn btn-primary w-100">
                            <i class="bi bi-plus-circle me-2"></i>
                            新規ライセンス発行
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="<?= UrlHelper::url('licenses') ?>" class="btn btn-outline-primary w-100">
                            <i class="bi bi-list me-2"></i>
                            ライセンス一覧
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="<?= UrlHelper::url('devices') ?>" class="btn btn-outline-info w-100">
                            <i class="bi bi-laptop me-2"></i>
                            デバイス管理
                        </a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <a href="<?= UrlHelper::url('licenses?expired=1') ?>" class="btn btn-outline-danger w-100">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            期限切れライセンス
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- System Information -->
<div class="row">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    システム情報
                </h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <td class="text-muted">PHP バージョン</td>
                        <td><?= PHP_VERSION ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">アプリケーション</td>
                        <td><?= config('name') ?></td>
                    </tr>
                    <tr>
                        <td class="text-muted">環境</td>
                        <td>
                            <span class="badge bg-<?= config('env') === 'production' ? 'success' : 'warning' ?>">
                                <?= strtoupper(config('env')) ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">ログイン時刻</td>
                        <td><?= date('Y-m-d H:i:s', $_SESSION['login_time']) ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-graph-up me-2"></i>
                    ライセンス統計
                </h5>
            </div>
            <div class="card-body">
                <?php 
                $activeRate = $stats['total_licenses'] > 0 ? ($stats['active_licenses'] / $stats['total_licenses']) * 100 : 0;
                $expiredRate = $stats['total_licenses'] > 0 ? ($stats['expired_licenses'] / $stats['total_licenses']) * 100 : 0;
                ?>
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <small class="text-muted">有効ライセンス率</small>
                        <small class="text-muted"><?= number_format($activeRate, 1) ?>%</small>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-success" style="width: <?= $activeRate ?>%"></div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <small class="text-muted">期限切れライセンス率</small>
                        <small class="text-muted"><?= number_format($expiredRate, 1) ?>%</small>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-danger" style="width: <?= $expiredRate ?>%"></div>
                    </div>
                </div>
                
                <div class="text-center pt-2">
                    <small class="text-muted">
                        最終更新: <?= date('Y-m-d H:i:s') ?>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>