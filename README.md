# License Admin Panel

TypeScript/Vercel/Supabaseで構築されたライセンスサーバー（API）用のPHP管理画面です。

## 機能

- **ライセンス管理**
  - 新規ライセンス発行（有効バージョン・アプリ・有効期限・最大デバイス数・メタ情報指定可能）
  - ライセンスの編集・無効化・削除
  - 検索・絞り込み（メール・バージョン・アプリ・有効期限など）

- **デバイス管理**
  - ライセンスごとのデバイス一覧表示
  - デバイスの解除・削除
  - オンライン状態の確認

- **セキュリティ**
  - パスワード認証必須
  - CSRF保護
  - セッション管理

- **UI/UX**
  - Bootstrap 5によるレスポンシブデザイン
  - 直感的な操作画面
  - リアルタイム統計表示

## 技術スタック

- **PHP**: 8.1+
- **フレームワーク**: Laravel Components (Eloquent, Routing, Views)
- **データベース**: PostgreSQL (Supabase)
- **HTTP クライアント**: Guzzle
- **フロントエンド**: Bootstrap 5 + Bootstrap Icons
- **認証**: セッションベース認証

## 必要要件

- PHP 8.1 以上
- Composer
- PostgreSQL (Supabase)
- Webサーバー (Apache/Nginx)

## インストール手順

### 1. プロジェクトのセットアップ

```bash
# プロジェクトディレクトリに移動
cd /path/to/license-admin

# Composer依存関係のインストール
composer install
```

### 2. 環境設定

```bash
# 環境設定ファイルをコピー
cp .env.example .env

# 環境設定を編集
nano .env
```

### 3. 環境変数の設定

`.env`ファイルを以下のように設定してください：

```bash
# Database Configuration (Supabase)
DB_CONNECTION=pgsql
DB_HOST=db.xxxxxxxxxxxx.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=your_supabase_db_password

# Supabase Configuration
SUPABASE_URL=https://xxxxxxxxxxxx.supabase.co
SUPABASE_ANON_KEY=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...
SUPABASE_SERVICE_ROLE_KEY=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...

# Application Configuration
APP_NAME="License Admin Panel"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
APP_KEY=base64:your_app_key_here

# Admin Authentication
ADMIN_EMAIL=admin@yourdomain.com
ADMIN_PASSWORD=secure_password_here
ADMIN_SESSION_LIFETIME=120

# License Server API (オプション)
LICENSE_API_URL=https://your-license-api.vercel.app
LICENSE_API_KEY=your_license_api_key
```

### 4. Webサーバー設定

#### Apache (.htaccess)

プロジェクトルートに `.htaccess` が含まれています。

#### Nginx

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /path/to/license-admin/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### 5. 権限設定

```bash
# ストレージディレクトリの権限設定
chmod -R 755 storage/
chmod -R 777 storage/sessions/
```

## データベーステーブル構造

管理画面は以下のテーブルを参照します：

### licenses テーブル

```sql
CREATE TABLE licenses (
    id SERIAL PRIMARY KEY,
    license_key VARCHAR(255) UNIQUE NOT NULL,
    email VARCHAR(255) NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    version VARCHAR(100) NOT NULL,
    expires_at TIMESTAMP NULL,
    max_devices INTEGER NOT NULL DEFAULT 1,
    is_active BOOLEAN NOT NULL DEFAULT true,
    metadata JSONB NULL,
    created_at TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP NOT NULL DEFAULT NOW()
);
```

### devices テーブル

```sql
CREATE TABLE devices (
    id SERIAL PRIMARY KEY,
    device_id VARCHAR(255) UNIQUE NOT NULL,
    license_key VARCHAR(255) NOT NULL,
    device_name VARCHAR(255) NULL,
    device_type VARCHAR(100) NULL,
    last_seen TIMESTAMP NULL,
    is_active BOOLEAN NOT NULL DEFAULT true,
    metadata JSONB NULL,
    created_at TIMESTAMP NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP NOT NULL DEFAULT NOW(),
    FOREIGN KEY (license_key) REFERENCES licenses(license_key) ON DELETE CASCADE
);
```

## 使用方法

### 1. 管理画面へのアクセス

1. ブラウザで `https://yourdomain.com/login` にアクセス
2. `.env`で設定したメールアドレスとパスワードでログイン

### 2. ライセンス発行

1. 「新規ライセンス発行」をクリック
2. 必要な情報を入力：
   - メールアドレス（必須）
   - プロダクト名（必須）
   - バージョン（必須）
   - 最大デバイス数（必須）
   - 有効期限（オプション）
   - メタデータ（オプション・JSON形式）
3. 「ライセンス発行」をクリック
4. 生成されたライセンスキーを確認

### 3. ライセンス管理

- **検索・フィルタ**: メール、プロダクト名、バージョン、ステータスで絞り込み
- **編集**: ライセンス情報の更新
- **無効化/有効化**: ライセンスの状態変更
- **削除**: ライセンスの完全削除（注意）

### 4. デバイス管理

- **一覧表示**: 登録されているデバイスの確認
- **オンライン状態**: 24時間以内のアクセスでオンライン表示
- **デバイス削除**: ライセンスからデバイスを解除

## API利用例

### ライセンス認証

```bash
curl -X POST \
  'https://your-license-api.vercel.app/api/verify' \
  -H 'Content-Type: application/json' \
  -H 'Authorization: Bearer YOUR_API_KEY' \
  -d '{
    "license_key": "ABCD1234-EFGH5678-IJKL9012-MNOP3456",
    "email": "user@example.com",
    "device_id": "unique-device-identifier",
    "device_name": "User MacBook Pro",
    "product_name": "YourApp",
    "version": "1.0.0"
  }'
```

**成功レスポンス (200)**:
```json
{
  "valid": true,
  "license": {
    "id": 1,
    "license_key": "ABCD1234-EFGH5678-IJKL9012-MNOP3456",
    "email": "user@example.com",
    "product_name": "YourApp",
    "version": "1.0.0",
    "expires_at": "2024-12-31T23:59:59Z",
    "max_devices": 3,
    "is_active": true
  },
  "device": {
    "device_id": "unique-device-identifier",
    "license_key": "ABCD1234-EFGH5678-IJKL9012-MNOP3456",
    "device_name": "User MacBook Pro",
    "last_seen": "2023-11-02T10:30:00Z"
  }
}
```

**エラーレスポンス (400)**:
```json
{
  "valid": false,
  "error": "Invalid license key or email",
  "code": "INVALID_LICENSE"
}
```

### デバイス解除

```bash
curl -X DELETE \
  'https://your-license-api.vercel.app/api/device/unique-device-identifier' \
  -H 'Authorization: Bearer YOUR_API_KEY' \
  -H 'Content-Type: application/json' \
  -d '{
    "license_key": "ABCD1234-EFGH5678-IJKL9012-MNOP3456"
  }'
```

### ライセンス情報取得

```bash
curl -X GET \
  'https://your-license-api.vercel.app/api/license/ABCD1234-EFGH5678-IJKL9012-MNOP3456' \
  -H 'Authorization: Bearer YOUR_API_KEY'
```

## トラブルシューティング

### 1. データベース接続エラー

```
Failed to fetch licenses: Connection refused
```

**解決方法**:
- `.env`のデータベース設定を確認
- Supabaseプロジェクトの設定を確認
- ネットワーク接続を確認

### 2. Composerエラー

```
Class 'Dotenv\Dotenv' not found
```

**解決方法**:
```bash
composer install --no-dev --optimize-autoloader
```

### 3. 権限エラー

```
Permission denied for sessions directory
```

**解決方法**:
```bash
sudo chown -R www-data:www-data storage/
chmod -R 755 storage/
```

### 4. ログインできない

**確認事項**:
- `.env`の`ADMIN_EMAIL`と`ADMIN_PASSWORD`が正しく設定されているか
- セッションディレクトリに書き込み権限があるか

## セキュリティ考慮事項

1. **本番環境での設定**:
   - `APP_DEBUG=false` に設定
   - `APP_ENV=production` に設定
   - 強固なパスワードを使用

2. **HTTPS の使用**:
   - 本番環境では必ずHTTPSを使用
   - SSL証明書の設定

3. **定期的なセキュリティ更新**:
   - PHP の更新
   - Composer パッケージの更新

4. **アクセス制限**:
   - 管理画面へのIPアドレス制限を検討
   - ファイアウォール設定

## ライセンス

MIT License

## サポート

問題が発生した場合は、以下の情報と共にお問い合わせください：

- PHP バージョン
- エラーメッセージ
- ログファイル（該当箇所）
- 環境設定（機密情報を除く）

---

© 2023 License Admin Panel. All rights reserved.