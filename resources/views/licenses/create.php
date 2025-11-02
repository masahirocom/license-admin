<?php
$title = '新規ライセンス発行 - License Admin Panel';
ob_start();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-plus-circle me-2"></i>
        新規ライセンス発行
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
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">ライセンス情報</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?= UrlHelper::url('licenses') ?>">
                    <?= csrf_field() ?>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">
                                メールアドレス <span class="text-danger">*</span>
                            </label>
                            <input type="email" 
                                   class="form-control" 
                                   id="email" 
                                   name="email" 
                                   value="<?= old('email') ?>"
                                   required>
                            <div class="form-text">ライセンス保有者のメールアドレス</div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="license_type" class="form-label">
                                ライセンスタイプ <span class="text-danger">*</span>
                            </label>
                            <select class="form-control" 
                                    id="license_type" 
                                    name="license_type" 
                                    required>
                                <option value="">-- 選択してください --</option>
                                <option value="trial" <?= old('license_type') === 'trial' ? 'selected' : '' ?>>Trial (試用版)</option>
                                <option value="paid" <?= old('license_type') === 'paid' ? 'selected' : '' ?>>Paid (有償版)</option>
                            </select>
                            <div class="form-text">ライセンスの種類を選択</div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="product_name" class="form-label">
                                プロダクト名 <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="product_name" 
                                   name="product_name" 
                                   value="<?= old('product_name') ?>"
                                   required>
                            <div class="form-text">ライセンス対象のプロダクト名</div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="version" class="form-label">
                                バージョン <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="version" 
                                   name="version" 
                                   value="<?= old('version') ?>"
                                   placeholder="例: 1.0.0, 2.x, latest"
                                   required>
                            <div class="form-text">有効なバージョン（ワイルドカード可）</div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="max_devices" class="form-label">
                                最大デバイス数 <span class="text-danger">*</span>
                            </label>
                            <input type="number" 
                                   class="form-control" 
                                   id="max_devices" 
                                   name="max_devices" 
                                   value="<?= old('max_devices', '1') ?>"
                                   min="1"
                                   required>
                            <div class="form-text">このライセンスで利用可能なデバイス数</div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="expires_at" class="form-label">
                                有効期限
                            </label>
                            <input type="date" 
                                   class="form-control" 
                                   id="expires_at" 
                                   name="expires_at" 
                                   value="<?= old('expires_at') ?>"
                                   min="<?= date('Y-m-d') ?>">
                            <div class="form-text">空白の場合は無期限</div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ライセンス状態</label>
                            <div class="form-check">
                                <input type="checkbox" 
                                       class="form-check-input" 
                                       id="is_active" 
                                       name="is_active" 
                                       <?= old('is_active', true) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_active">
                                    ライセンスを有効化
                                </label>
                            </div>
                            <div class="form-text">無効化すると認証が失敗します</div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="metadata" class="form-label">
                            メタデータ (JSON)
                        </label>
                        <textarea class="form-control" 
                                  id="metadata" 
                                  name="metadata" 
                                  rows="4"
                                  placeholder='{"company": "Example Corp", "note": "Test license"}'><?= old('metadata') ?></textarea>
                        <div class="form-text">JSON形式でライセンスに関連する追加情報を記録</div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="<?= UrlHelper::url('licenses') ?>" class="btn btn-secondary">キャンセル</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>
                            ライセンス発行
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-info-circle me-2"></i>
                    発行について
                </h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        ライセンスキーは自動生成されます
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        メールアドレスは一意である必要があります
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        有効期限は将来の日付を指定してください
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        最大デバイス数は後から変更可能です
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        メタデータは検索に利用できます
                    </li>
                </ul>
                
                <div class="alert alert-info">
                    <i class="bi bi-lightbulb me-2"></i>
                    <strong>ヒント:</strong> バージョンに「*」を指定すると全バージョンに対応できます。
                </div>
            </div>
        </div>
        
        <div class="card border-0 shadow-sm mt-3">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-code-square me-2"></i>
                    API例
                </h5>
            </div>
            <div class="card-body">
                <pre><code class="text-muted small">curl -X POST \
  '<?= config('license_api.url') ?>/verify' \
  -H 'Content-Type: application/json' \
  -d '{
    "license_key": "XXXX-XXXX-XXXX-XXXX",
    "email": "user@example.com"
  }'</code></pre>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/app.php';
?>