<?php
$title = 'ライセンス一覧 - License Admin Panel';
ob_start();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-key me-2"></i>
        ライセンス管理
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="<?= UrlHelper::url('licenses/create') ?>" class="btn btn-primary">
            <i class="bi bi-plus-circle me-2"></i>
            新規ライセンス発行
        </a>
    </div>
</div>

<!-- Search and Filter -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="<?= UrlHelper::url('licenses') ?>" class="row g-3">
            <div class="col-md-3">
                <label for="search" class="form-label">検索</label>
                <input type="text" 
                       class="form-control" 
                       id="search" 
                       name="search" 
                       value="<?= htmlspecialchars($filters['search'] ?? '') ?>"
                       placeholder="メール、ライセンスキー、プロダクト名">
            </div>
            <div class="col-md-2">
                <label for="product" class="form-label">プロダクト</label>
                <input type="text" 
                       class="form-control" 
                       id="product" 
                       name="product" 
                       value="<?= htmlspecialchars($filters['product'] ?? '') ?>"
                       placeholder="プロダクト名">
            </div>
            <div class="col-md-2">
                <label for="version" class="form-label">バージョン</label>
                <input type="text" 
                       class="form-control" 
                       id="version" 
                       name="version" 
                       value="<?= htmlspecialchars($filters['version'] ?? '') ?>"
                       placeholder="バージョン">
            </div>
            <div class="col-md-2">
                <label for="is_active" class="form-label">ステータス</label>
                <select class="form-select" id="is_active" name="is_active">
                    <option value="">すべて</option>
                    <option value="1" <?= (isset($filters['is_active']) && $filters['is_active'] === '1') ? 'selected' : '' ?>>有効</option>
                    <option value="0" <?= (isset($filters['is_active']) && $filters['is_active'] === '0') ? 'selected' : '' ?>>無効</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="expired" class="form-label">期限</label>
                <select class="form-select" id="expired" name="expired">
                    <option value="">すべて</option>
                    <option value="0" <?= (isset($filters['expired']) && $filters['expired'] === '0') ? 'selected' : '' ?>>有効期限内</option>
                    <option value="1" <?= (isset($filters['expired']) && $filters['expired'] === '1') ? 'selected' : '' ?>>期限切れ</option>
                </select>
            </div>
            <div class="col-md-1">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- License Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">ライセンス一覧 (<?= count($licenses) ?>件)</h5>
        <?php if (!empty($filters)): ?>
            <a href="<?= UrlHelper::url('licenses') ?>" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-x-circle me-1"></i>
                フィルタクリア
            </a>
        <?php endif; ?>
    </div>
    <div class="card-body p-0">
        <?php if (empty($licenses)): ?>
            <div class="text-center py-5">
                <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                <h5 class="text-muted mt-3">ライセンスが見つかりません</h5>
                <p class="text-muted">新しいライセンスを発行するか、検索条件を変更してください。</p>
                <a href="<?= UrlHelper::url('licenses/create') ?>" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>
                    新規ライセンス発行
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>ライセンスキー</th>
                            <th>メールアドレス</th>
                            <th>プロダクト</th>
                            <th>バージョン</th>
                            <th>有効期限</th>
                            <th>デバイス数</th>
                            <th>ステータス</th>
                            <th class="text-end">アクション</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($licenses as $license): ?>
                            <?php
                            $isExpired = $license['expires_at'] && strtotime($license['expires_at']) < time();
                            $statusClass = $license['is_active'] ? ($isExpired ? 'badge-expired' : 'badge-active') : 'badge-inactive';
                            $statusText = $license['is_active'] ? ($isExpired ? '期限切れ' : '有効') : '無効';
                            ?>
                            <tr>
                                <td>
                                    <code><?= htmlspecialchars($license['license_key']) ?></code>
                                </td>
                                <td><?= htmlspecialchars($license['metadata']['email'] ?? 'N/A') ?></td>
                                <td><?= htmlspecialchars($license['license_type'] ?? $license['product_id'] ?? 'N/A') ?></td>
                                <td>
                                    <?php if (!empty($license['allowed_versions'])): ?>
                                        <?php foreach ($license['allowed_versions'] as $ver): ?>
                                            <span class="badge bg-secondary"><?= htmlspecialchars($ver) ?></span>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">All</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($license['expires_at']): ?>
                                        <?= date('Y-m-d', strtotime($license['expires_at'])) ?>
                                        <?php if ($isExpired): ?>
                                            <br><small class="text-danger">期限切れ</small>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted">無期限</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        0 / <?= (int)$license['max_devices'] ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= UrlHelper::url('licenses/show/' . $license['id']) ?>" 
                                           class="btn btn-outline-primary" 
                                           title="詳細表示">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="<?= UrlHelper::url('licenses/' . $license['id'] . '/edit') ?>" 
                                           class="btn btn-outline-secondary" 
                                           title="編集">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <?php if ($license['is_active']): ?>
                                            <form method="POST" action="<?= UrlHelper::url('licenses/' . $license['id'] . '/deactivate') ?>" class="d-inline">
                                                <?= csrf_field() ?>
                                                <button type="submit" 
                                                        class="btn btn-outline-warning" 
                                                        title="無効化"
                                                        onclick="return confirm('このライセンスを無効化しますか？')">
                                                    <i class="bi bi-pause"></i>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <form method="POST" action="<?= UrlHelper::url('licenses/' . $license['id'] . '/activate') ?>" class="d-inline">
                                                <?= csrf_field() ?>
                                                <button type="submit" 
                                                        class="btn btn-outline-success" 
                                                        title="有効化"
                                                        onclick="return confirm('このライセンスを有効化しますか？')">
                                                    <i class="bi bi-play"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        <form method="POST" action="<?= UrlHelper::url('licenses/' . $license['id'] . '/delete') ?>" class="d-inline">
                                            <?= csrf_field() ?>
                                            <button type="submit" 
                                                    class="btn btn-outline-danger confirm-delete" 
                                                    title="削除">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>