# Essensplan

Web-based weekly meal plan manager for webtrash.ch.

## Current Status

The project now includes:

- admin-only user management
- user creation with forced `user` role
- password reset for existing users and admins
- email delivery of weekly plans
- a mail link to the print view
- a print layout tuned for desktop and a lighter mobile fallback
- shared webtrash.ch branding and navigation

## Main Pages

- `index.php` - overview and weekly planning
- `view_weeks.php` - list of weeks
- `view_week.php` - single week view, print and email actions
- `print.php` - print-optimized week layout
- `user_management.php` - admin user management

## Notes

The printable view is designed for A4 landscape, while the mobile screen view is intentionally simpler so it stays readable on phones.
