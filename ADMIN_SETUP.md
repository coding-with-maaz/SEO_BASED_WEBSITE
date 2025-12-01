# Admin Panel Security Setup

## ✅ Security Features Implemented

1. **Authentication Required** - All admin routes require login
2. **Admin Role Check** - Only users with `is_admin = true` can access admin panel
3. **Middleware Protection** - `AdminMiddleware` protects all admin routes
4. **Secure Login** - CSRF protection, password hashing, session management
5. **Logout Functionality** - Secure logout with session invalidation

## 🔐 Default Admin Credentials

After running the seeder, the default admin credentials are:

- **Email**: `admin@nazaarabox.com` (or value from `.env` `ADMIN_EMAIL`)
- **Password**: `admin123` (or value from `.env` `ADMIN_PASSWORD`)

⚠️ **IMPORTANT**: Change the default password immediately after first login!

## 📋 Setup Instructions

### 1. Run Migration
```bash
php artisan migrate
```

### 2. Seed Admin User
```bash
php artisan db:seed --class=AdminSeeder
```

Or seed all:
```bash
php artisan db:seed
```

### 3. Configure Admin Credentials (Optional)

Add to your `.env` file:
```env
ADMIN_EMAIL=admin@yourdomain.com
ADMIN_NAME=Administrator
ADMIN_PASSWORD=your_secure_password_here
```

Then run the seeder again to create admin with custom credentials.

## 🔑 Access Admin Panel

1. Navigate to: `http://your-domain.com/admin/login`
2. Enter admin email and password
3. You'll be redirected to the admin dashboard

## 🛡️ Security Features

### AdminMiddleware
- Checks if user is authenticated
- Verifies user has admin privileges (`is_admin = true`)
- Redirects to login if not authenticated
- Returns 403 if not admin

### LoginController
- Validates credentials
- Only allows admin users to login
- Regenerates session on login
- Supports "Remember Me" functionality
- Secure logout with session invalidation

### User Model
- `isAdmin()` method to check admin status
- `is_admin` boolean field in database
- Password automatically hashed

## 📁 Files Created/Modified

### New Files:
- `app/Http/Middleware/AdminMiddleware.php` - Admin access control
- `app/Http/Controllers/Auth/LoginController.php` - Login/logout handling
- `database/migrations/2025_12_01_214755_add_is_admin_to_users_table.php` - Adds is_admin field
- `database/seeders/AdminSeeder.php` - Creates default admin user
- `resources/views/admin/auth/login.blade.php` - Login page
- `resources/views/components/admin-header.blade.php` - Logout button component

### Modified Files:
- `app/Models/User.php` - Added `is_admin` field and `isAdmin()` method
- `routes/web.php` - Protected admin routes with middleware
- `bootstrap/app.php` - Registered AdminMiddleware alias
- `resources/views/admin/dashboard.blade.php` - Added logout button
- `database/seeders/DatabaseSeeder.php` - Includes AdminSeeder

## 🔄 Creating Additional Admin Users

### Via Database:
```sql
UPDATE users SET is_admin = 1 WHERE email = 'user@example.com';
```

### Via Tinker:
```bash
php artisan tinker
```
```php
$user = App\Models\User::where('email', 'user@example.com')->first();
$user->is_admin = true;
$user->save();
```

### Via Seeder:
Modify `AdminSeeder.php` to create additional admin users.

## 🚨 Security Best Practices

1. **Change Default Password** - Always change default admin password
2. **Use Strong Passwords** - Minimum 12 characters, mix of letters, numbers, symbols
3. **Limit Admin Users** - Only grant admin access to trusted users
4. **Regular Audits** - Periodically review admin users
5. **Session Security** - Use HTTPS in production
6. **Rate Limiting** - Consider adding rate limiting to login route

## 🔧 Troubleshooting

### Can't Access Admin Panel
- Check if user has `is_admin = 1` in database
- Verify middleware is applied to routes
- Check session configuration

### Login Not Working
- Verify credentials are correct
- Check database for user existence
- Clear cache: `php artisan cache:clear`
- Clear config: `php artisan config:clear`

### 403 Forbidden Error
- User is authenticated but not admin
- Check `is_admin` field in users table
- Verify AdminMiddleware is working

## 📝 Notes

- All admin routes are protected by `auth` and `admin` middleware
- Login route is public (no authentication required)
- Logout route requires authentication
- Session timeout follows Laravel's default configuration
- Password reset functionality can be added if needed

