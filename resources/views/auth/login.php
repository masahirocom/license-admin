<?php
$title = 'Login - License Admin Panel';
ob_start();
?>

<form method="POST" action="<?= UrlHelper::url('login') ?>">
    <?= csrf_field() ?>
    
    <div class="mb-3">
        <label for="email" class="form-label">
            <i class="bi bi-envelope me-2"></i>
            メールアドレス
        </label>
        <input type="email" 
               class="form-control form-control-lg" 
               id="email" 
               name="email" 
               value="<?= old('email') ?>"
               required 
               autofocus>
    </div>
    
    <div class="mb-4">
        <label for="password" class="form-label">
            <i class="bi bi-lock me-2"></i>
            パスワード
        </label>
        <input type="password" 
               class="form-control form-control-lg" 
               id="password" 
               name="password" 
               required>
    </div>
    
    <div class="d-grid">
        <button type="submit" class="btn btn-primary btn-lg">
            <i class="bi bi-box-arrow-in-right me-2"></i>
            ログイン
        </button>
    </div>
</form>

<div class="mt-4 text-center">
    <small class="text-muted">
        <i class="bi bi-info-circle me-1"></i>
        デフォルト認証情報は.envファイルで設定できます
    </small>
</div>

<?php
$content = ob_get_clean();
unset($_SESSION['old']); // Clear old input
include __DIR__ . '/../layouts/auth.php';
?>