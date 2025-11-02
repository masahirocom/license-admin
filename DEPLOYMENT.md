# Server Deployment Guide

## サーバー構成

```text
/home/metamondes.com/
├── public_html/
│   └── license/           # ← ここに public/ の中身のみ配置
│       ├── index.php
│       ├── .htaccess
│       └── assets/
└── license-admin/         # ← ここにpublic以外の全ファイル配置
    ├── app/
    ├── config/
    ├── resources/
    ├── routes/
    ├── bootstrap/
    ├── storage/
    ├── vendor/
    ├── composer.json
    ├── .env
    └── README.md
```

## 手動アップロード手順

### 1. 依存関係のインストール (ローカル)

```bash
cd /Volumes/NVMe2TB/LicenseAdmin
composer install --no-dev --optimize-autoloader
```

### 2. ファイルのアップロード

#### A. メインアプリケーション (/home/metamondes.com/license-admin/)
以下のディレクトリ・ファイルをアップロード：
```
app/
config/
resources/
routes/
bootstrap/
storage/
vendor/
composer.json
.env.example
README.md
```

#### B. 公開ディレクトリ (/home/metamondes.com/public_html/license/)
`public/` フォルダの中身のみをアップロード：
```
index.php
.htaccess-production → .htaccess (リネーム)
```

### 3. 環境設定

サーバーにSSHで接続し、環境設定ファイルを作成：

```bash
ssh username@metamondes.com
cd /home/metamondes.com/license-admin
cp .env.example .env
nano .env
```

### 4. 権限設定

```bash
# アプリケーションディレクトリ
chmod -R 755 /home/metamondes.com/license-admin
chmod -R 777 /home/metamondes.com/license-admin/storage/sessions

# 公開ディレクトリ
chmod 644 /home/metamondes.com/public_html/license/.htaccess
chmod 644 /home/metamondes.com/public_html/license/index.php
```

### 5. .env 設定例

```bash
# Database Configuration (Supabase)
DB_CONNECTION=pgsql
DB_HOST=db.xxxxxxxxxxxx.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=your_supabase_password

# Supabase Configuration
SUPABASE_URL=https://xxxxxxxxxxxx.supabase.co
SUPABASE_ANON_KEY=your_anon_key
SUPABASE_SERVICE_ROLE_KEY=your_service_role_key

# Application Configuration
APP_NAME="License Admin Panel"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://metamondes.com/license
APP_KEY=base64:your_generated_key

# Admin Authentication
ADMIN_EMAIL=admin@metamondes.com
ADMIN_PASSWORD=your_secure_password
ADMIN_SESSION_LIFETIME=120

# Security
SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
APP_TIMEZONE=Asia/Tokyo
```

## 自動デプロイ

### スクリプトを使用する場合

```bash
# デプロイスクリプトに実行権限を付与
chmod +x deploy.sh

# 設定を編集
nano deploy.sh

# デプロイ実行
./deploy.sh
```

## アクセス確認

設定完了後、以下のURLでアクセス確認：

- **管理画面**: https://metamondes.com/license/login
- **ダッシュボード**: https://metamondes.com/license/dashboard

## トラブルシューティング

### よくある問題

1. **500 エラー**
   ```bash
   # エラーログ確認
   tail -f /home/metamondes.com/public_html/license/error.log
   
   # 権限確認
   ls -la /home/metamondes.com/license-admin/storage/sessions
   ```

2. **Composer autoload エラー**
   ```bash
   cd /home/metamondes.com/license-admin
   composer dump-autoload --optimize
   ```

3. **データベース接続エラー**
   - .env の DB_* 設定を確認
   - Supabase プロジェクトの接続情報を確認

4. **セッションエラー**
   ```bash
   chmod -R 777 /home/metamondes.com/license-admin/storage/sessions
   ```

### デバッグモード (開発時のみ)

```bash
# .env で一時的にデバッグを有効化
APP_DEBUG=true
APP_ENV=local
```

**注意**: 本番環境では必ず `APP_DEBUG=false` に設定してください。