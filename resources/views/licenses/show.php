<?php
$title = 'ライセンス詳細 - License Admin Panel';
ob_start();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-file-text me-2"></i>
        ライセンス詳細
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?= UrlHelper::url('licenses') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>
            ライセンス一覧に戻る
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">ライセンス情報</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tbody>
                        <tr>
                            <th style="width: 200px;">ライセンスキー</th>
                            <td><code><?= htmlspecialchars($license['license_key']) ?></code></td>
                        </tr>
                        <tr>
                            <th>メールアドレス</th>
                            <td><?= htmlspecialchars($license['metadata']['email'] ?? 'N/A') ?></td>
                        </tr>
                        <tr>
                            <th>ライセンスタイプ</th>
                            <td>
                                <span class="badge bg-<?= $license['license_type'] === 'paid' ? 'success' : 'info' ?>">
                                    <?= $license['license_type'] === 'paid' ? 'Paid (有償版)' : 'Trial (試用版)' ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>プロダクト</th>
                            <td><?= htmlspecialchars($license['product_id'] ?? 'N/A') ?></td>
                        </tr>
                        <tr>
                            <th>許可バージョン</th>
                            <td>
                                <?php if (!empty($license['allowed_versions'])): ?>
                                    <?php foreach ($license['allowed_versions'] as $ver): ?>
                                        <span class="badge bg-secondary"><?= htmlspecialchars($ver) ?></span>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <span class="text-muted">All</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>有効期限</th>
                            <td>
                                <?php if ($license['expires_at']): ?>
                                    <?= date('Y-m-d H:i', strtotime($license['expires_at'])) ?>
                                    <?php
                                    $isExpired = strtotime($license['expires_at']) < time();
                                    if ($isExpired): ?>
                                        <br><span class="badge bg-danger">期限切れ</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-muted">無期限</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>最大デバイス数</th>
                            <td><?= htmlspecialchars($license['max_devices']) ?></td>
                        </tr>
                        <tr>
                            <th>ステータス</th>
                            <td>
                                <?php if ($license['is_active']): ?>
                                    <span class="badge bg-success">有効</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">無効</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th>作成日時</th>
                            <td><?= $license['created_at'] ? date('Y-m-d H:i', strtotime($license['created_at'])) : 'N/A' ?></td>
                        </tr>
                    </tbody>
                </table>

                <div class="mt-4">
                    <a href="<?= UrlHelper::url('licenses/' . $license['id'] . '/edit') ?>" class="btn btn-primary">
                        <i class="bi bi-pencil me-2"></i>
                        編集
                    </a>
                    <?php if ($license['is_active']): ?>
                        <form method="POST" action="<?= UrlHelper::url('licenses/' . $license['id'] . '/deactivate') ?>" class="d-inline">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-pause-circle me-2"></i>
                                無効化
                            </button>
                        </form>
                    <?php else: ?>
                        <form method="POST" action="<?= UrlHelper::url('licenses/' . $license['id'] . '/activate') ?>" class="d-inline">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-play-circle me-2"></i>
                                有効化
                            </button>
                        </form>
                    <?php endif; ?>
                    <form method="POST" action="<?= UrlHelper::url('licenses/' . $license['id'] . '/delete') ?>" class="d-inline">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-danger confirm-delete">
                            <i class="bi bi-trash me-2"></i>
                            削除
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">登録デバイス (<?= count($devices) ?>/<?= $license['max_devices'] ?>)</h5>
            </div>
            <div class="card-body">
                <?php if (empty($devices)): ?>
                    <p class="text-muted">登録されているデバイスはありません</p>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($devices as $device): ?>
                            <div class="list-group-item px-0">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1"><?= htmlspecialchars($device['device_name'] ?? $device['device_id']) ?></h6>
                                        <small class="text-muted"><?= htmlspecialchars($device['device_type'] ?? 'Unknown') ?></small>
                                    </div>
                                    <?php if ($device['is_active']): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inactive</span>
                                    <?php endif; ?>
                                </div>
                                <?php if (isset($device['last_seen_at'])): ?>
                                    <small class="text-muted">
                                        最終接続: <?= date('Y-m-d H:i', strtotime($device['last_seen_at'])) ?>
                                    </small>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
