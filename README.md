# PracticalERP

A Laravel-based ERP module for managing contacts and custom fields, with user authentication and dashboard features.

## Installation

1. **Clone the repository:**
   ```bash
   git clone <your-repo-url>
   cd PracticalERP
   ```

2. **Install PHP dependencies:**
   ```bash
   composer install
   ```

3. **Copy the example environment file and set your environment variables:**
   ```bash
   cp .env.example .env
   ```
   Edit `.env` to match your database and environment settings.

4. **Generate application key:**
   ```bash
   php artisan key:generate
   ```

5. **Run migrations:**
   ```bash
   php artisan migrate
   ```

6. **Start the development server:**
   ```bash
   php artisan serve
   ```

## Main Features

- **User Authentication**
  - Register, login, and logout functionality
  - Auth-protected dashboard

- **Contact Management**
  - Create, view, edit, and delete contacts
  - Upload profile images and additional files for contacts
  - Filter/search contacts by name, email, phone, gender, and custom fields
  - Merge duplicate contacts with preview and conflict resolution

- **Custom Fields**
  - Create, edit, and delete custom fields for contacts
  - Support for various field types (text, email, number, date, textarea, select)
  - Assign custom fields to contacts and filter/search by custom field values

- **Dashboard**
  - Overview of tasks and quick navigation to main modules

- **AJAX Support**
  - Many actions (listing, filtering, merging) support AJAX for a smoother user experience

- **Responsive Admin UI**
  - Admin panel for managing contacts and custom fields

## Project Structure

- `app/Http/Controllers/` - Main controllers for contacts, custom fields, authentication, and dashboard
- `app/Models/` - Eloquent models for core entities
- `resources/views/` - Blade templates for UI
- `routes/web.php` - Web routes
- `database/migrations/` - Database schema

## Requirements
- PHP >= 7.3
- Composer
- MySQL or compatible database

## License

MIT
