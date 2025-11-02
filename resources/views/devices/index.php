<?php
$title = 'デバイス管理 - License Admin Panel';
ob_start();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-laptop me-2"></i>
        デバイス管理
    </h1>
</div>

<!-- Search and Filter -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="<?= UrlHelper::url('devices') ?>" class="row g-3">
            <div class="col-md-4">
                <label for="search" class="form-label">検索</label>
                <input type="text" 
                       class="form-control" 
                       id="search" 
                       name="search" 
                       value="<?= htmlspecialchars($filters['search'] ?? '') ?>"
                       placeholder="デバイスID、デバイス名、デバイスタイプ">
            </div>
            <div class="col-md-3">
                <label for="license_key" class="form-label">ライセンスキー</label>
                <input type="text" 
                       class="form-control" 
                       id="license_key" 
                       name="license_key" 
                       value="<?= htmlspecialchars($filters['license_key'] ?? '') ?>"
                       placeholder="ライセンスキー">
            </div>
            <div class="col-md-3">
                <label for="is_active" class="form-label">ステータス</label>
                <select class="form-select" id="is_active" name="is_active">
                    <option value="">すべて</option>
                    <option value="1" <?= (isset($filters['is_active']) && $filters['is_active'] === '1') ? 'selected' : '' ?>>アクティブ</option>
                    <option value="0" <?= (isset($filters['is_active']) && $filters['is_active'] === '0') ? 'selected' : '' ?>>非アクティブ</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-search"></i>
                        検索
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Device Table -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">デバイス一覧 (<?= count($devices) ?>件)</h5>
        <?php if (!empty($filters)): ?>
            <a href="<?= UrlHelper::url('devices') ?>" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-x-circle me-1"></i>
                フィルタクリア
            </a>
        <?php endif; ?>
    </div>
    <div class="card-body p-0">
        <?php if (empty($devices)): ?>
            <div class="text-center py-5">
                <i class="bi bi-laptop text-muted" style="font-size: 3rem;"></i>
                <h5 class="text-muted mt-3">デバイスが見つかりません</h5>
                <p class="text-muted">登録されているデバイスがないか、検索条件に該当するデバイスがありません。</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>デバイスID</th>
                            <th>デバイス名</th>
                            <th>タイプ</th>
                            <th>ライセンスキー</th>
                            <th>最終確認</th>
                            <th>ステータス</th>
                            <th>登録日時</th>
                            <th class="text-end">アクション</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($devices as $device): ?>
                            <?php
                            $lastSeen = $device['last_seen'] ? strtotime($device['last_seen']) : null;
                            $isOnline = $lastSeen && (time() - $lastSeen) <= (24 * 60 * 60); // 24時間以内
                            $statusClass = $device['is_active'] ? ($isOnline ? 'success' : 'warning') : 'secondary';
                            $statusText = $device['is_active'] ? ($isOnline ? 'オンライン' : 'オフライン') : '非アクティブ';
                            ?>
                            <tr>
                                <td>
                                    <code><?= htmlspecialchars(substr($device['device_id'], 0, 20)) ?><?= strlen($device['device_id']) > 20 ? '...' : '' ?></code>
                                </td>
                                <td>
                                    <?= htmlspecialchars($device['device_name'] ?: 'Unknown Device') ?>
                                    <?php if ($isOnline): ?>
                                        <i class="bi bi-wifi text-success" title="オンライン"></i>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-info"><?= htmlspecialchars($device['device_type'] ?: 'Unknown') ?></span>
                                </td>
                                <td>
                                    <code><?= htmlspecialchars($device['license_key']) ?></code>
                                </td>
                                <td>
                                    <?php if ($lastSeen): ?>
                                        <?= date('Y-m-d H:i', $lastSeen) ?>
                                        <br><small class="text-muted">
                                            <?php
                                            $diff = time() - $lastSeen;
                                            if ($diff < 3600) {
                                                echo floor($diff / 60) . '分前';
                                            } elseif ($diff < 86400) {
                                                echo floor($diff / 3600) . '時間前';
                                            } else {
                                                echo floor($diff / 86400) . '日前';
                                            }
                                            ?>
                                        </small>
                                    <?php else: ?>
                                        <span class="text-muted">未接続</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $statusClass ?>"><?= $statusText ?></span>
                                </td>
                                <td>
                                    <?= date('Y-m-d H:i', strtotime($device['created_at'])) ?>
                                </td>
                                <td class="text-end">
                                    <form method="POST" action="<?= UrlHelper::url('devices/' . urlencode($device['device_id']) . '/remove') ?>" class="d-inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" 
                                                class="btn btn-sm btn-outline-danger confirm-delete" 
                                                title="デバイス削除"
                                                onclick="return confirm('このデバイスを削除しますか？\nライセンスから解除され、再認証が必要になります。')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Device Information Panel -->
<?php if (!empty($devices)): ?>
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-bar-chart me-2"></i>
                    デバイス統計
                </h5>
            </div>
            <div class="card-body">
                <?php
                $totalDevices = count($devices);
                $activeDevices = count(array_filter($devices, function($d) { return $d['is_active']; }));
                $onlineDevices = count(array_filter($devices, function($d) { 
                    $lastSeen = $d['last_seen'] ? strtotime($d['last_seen']) : null;
                    return $d['is_active'] && $lastSeen && (time() - $lastSeen) <= (24 * 60 * 60); 
                }));
                ?>
                <div class="row text-center">
                    <div class="col-4">
                        <h4 class="text-primary"><?= $totalDevices ?></h4>
                        <small class="text-muted">総デバイス数</small>
                    </div>
                    <div class="col-4">
                        <h4 class="text-success"><?= $activeDevices ?></h4>
                        <small class="text-muted">アクティブ</small>
                    </div>
                    <div class="col-4">
                        <h4 class="text-info"><?= $onlineDevices ?></h4>
                        <small class="text-muted">オンライン</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    デバイス管理について
                </h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        デバイスは自動的に登録されます
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        24時間以内のアクセスでオンライン表示
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        削除すると再認証が必要になります
                    </li>
                    <li>
                        <i class="bi bi-check-circle text-success me-2"></i>
                        ライセンス詳細からも管理可能
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>