<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'License Admin Panel' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #f8f9fa;
        }
        .nav-link.active {
            background-color: #007bff;
            color: white !important;
        }
        .table th {
            background-color: #f8f9fa;
        }
        .badge-expired {
            background-color: #dc3545;
        }
        .badge-active {
            background-color: #28a745;
        }
        .badge-inactive {
            background-color: #6c757d;
        }
        .card-stats {
            transition: transform 0.2s;
        }
        .card-stats:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-decoration-none">
                        <i class="bi bi-shield-check fs-4 me-2"></i>
                        <span class="fs-5 fw-bold">License Admin</span>
                    </div>
                    <hr>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link <?= UrlHelper::isActive('/dashboard') ? 'active' : '' ?>" href="<?= UrlHelper::url('dashboard') ?>">
                                <i class="bi bi-house-door me-2"></i>
                                ダッシュボード
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= UrlHelper::isActive('/licenses') ? 'active' : '' ?>" href="<?= UrlHelper::url('licenses') ?>">
                                <i class="bi bi-key me-2"></i>
                                ライセンス管理
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= UrlHelper::isActive('/devices') ? 'active' : '' ?>" href="<?= UrlHelper::url('devices') ?>">
                                <i class="bi bi-laptop me-2"></i>
                                デバイス管理
                            </a>
                        </li>
                    </ul>
                    <hr>
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-2"></i>
                            <strong><?= $_SESSION['admin_email'] ?? 'Admin' ?></strong>
                        </a>
                        <ul class="dropdown-menu text-small shadow">
                            <li>
                                <form method="POST" action="<?= UrlHelper::url('logout') ?>" class="m-0">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="dropdown-item">
                                        <i class="bi bi-box-arrow-right me-2"></i>
                                        ログアウト
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="pt-3">
                    <!-- Flash messages -->
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle me-2"></i>
                            <?= htmlspecialchars($_SESSION['success']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <?= htmlspecialchars($_SESSION['error']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['errors'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>入力エラーがあります:</strong>
                            <ul class="mb-0 mt-2">
                                <?php foreach ($_SESSION['errors'] as $field => $error): ?>
                                    <li><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['errors']); ?>
                    <?php endif; ?>

                    <!-- Page content -->
                    <?= $content ?>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
        
        // Confirm deletion
        document.querySelectorAll('.confirm-delete').forEach(button => {
            button.addEventListener('click', (e) => {
                if (!confirm('本当に削除してもよろしいですか？この操作は取り消せません。')) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>