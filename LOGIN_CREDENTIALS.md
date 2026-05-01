# Login Credentials & Test Accounts

## Test Accounts Created
The following test accounts have been automatically created and are ready to use:

### Student Account
- **Email:** `student@test.com`
- **Password:** `password`
- **Role:** Student 🎓

### Owner Account
- **Email:** `owner@test.com`
- **Password:** `password`
- **Role:** Owner 🏠

### Broker Account
- **Email:** `broker@test.com`
- **Password:** `password`
- **Role:** Broker 🤝

## How to Use
1. Navigate to the login page
2. Enter the email and password for the account you want to test
3. Click "Login"
4. You should be redirected to the home page and authenticated

## Issues Fixed

### 1. Database Seeding Issues
- **Problem:** Composer autoload configuration was pointing to incorrect paths (`../MySql/` instead of `../Database/`)
- **Solution:** Fixed references in:
  - `Backend/composer.json`
  - `Backend/vendor/composer/autoload_psr4.php`
  - `Backend/vendor/composer/autoload_static.php`
- **Result:** Database seeder now runs successfully and test accounts are created

### 2. Registration Form Validation Error Messages
- **Problem:** When registering with a new email, validation errors weren't clearly displayed, making it difficult to know what was wrong
- **Solution:** 
  - Updated error handling in `Frontend/src/stores/auth.js` to show field-specific validation errors
  - Modified `Frontend/src/views/auth/Register.vue` to display errors next to each form field
  - Added CSS styling for error states
- **Result:** Users now see clear error messages for each field that fails validation

## Registration Requirements
When registering a new account, ensure:
- **Name:** Required, max 255 characters
- **Email:** Required, valid email format, must be unique (not already registered)
- **Password:** Required, minimum 8 characters, must be confirmed
- **Role:** Required (Student, Owner, or Broker)
- **Phone:** Optional, max 20 characters
- **Language:** Optional (Arabic or English)

## Example Validation Errors You May Encounter
- `email: The email has already been taken.` - Try a different email address
- `password: The password confirmation does not match.` - Ensure both password fields are identical
- `password: The password must be at least 8 characters.` - Use a longer password
- `role_id: The selected role_id is invalid.` - Select a valid role from the form

## Testing the Application
1. **Test Login:** Use any of the test accounts above
2. **Test Registration:** Enter a unique email address and complete the registration form
3. **Verify Token Storage:** Check browser localStorage to confirm auth token is stored
4. **Test Protected Routes:** After login, try accessing protected features (listings, favorites, etc.)

## Backend Database
- **Database Name:** `laravel`
- **Database User:** `root`
- **Password:** (empty)
- **Host:** `localhost`
- **Port:** `3306`

## Related Files Modified
- `Backend/composer.json` - Fixed autoload paths
- `Backend/vendor/composer/autoload_psr4.php` - Fixed autoload PSR-4 configuration
- `Backend/vendor/composer/autoload_static.php` - Fixed static autoload configuration
- `Frontend/src/stores/auth.js` - Improved error handling
- `Frontend/src/views/auth/Register.vue` - Added field-level validation error display

## Next Steps
If you encounter any other issues:
1. Clear browser cache and localStorage
2. Ensure both backend (`php artisan serve`) and frontend (`npm run dev`) are running
3. Check browser console for any JavaScript errors
4. Check backend logs in `storage/logs/` for PHP errors
