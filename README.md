# Pawsome Connections (Work In Progress)

Pawsome Connections is a Pet Adoption platform built with PHP, MySQL, Vanilla CSS, and HTML.  
**Note: This project is currently under active production and development.**

## Completed Features
The system currently supports the following verified features:
- **Responsive Design**: Entire platform dynamically scales down to mobile breakpoints smoothly via custom CSS overrides.
- **Pet Adoption Flow**: Users can browse available pets, click "Apply to Adopt", and submit a detailed application form.
- **Application Tracking**: Adopters have a localized dashboard tracking their application statuses.
- **Admin Application Management**: Admins can review all pending adoption requests, alongside seeing red notification badges on the sidebar indicating pending workloads.
- [x] **Adopter Dashboards**: Users can track their applications and view care guides assigned to them.
- [x] **Admin Pet Modals**: Admins can click on any pet's name within the dashboard or pet list to instantly view a premium card-style modal containing the pet’s full details and image gallery.
- [x] **Applicant Modals**: Admins can click on an applicant's name to instantly pull up a detailed modal outlining their reasoning and household status.
- **Automated Emailing**: Upon an Admin clicking 'Approve' or 'Reject', the system uses PHPMailer to securely send an HTML-formatted status update to the applicant's email inbox.
- **Dynamic Breed Filtering**: When creating or editing pet listings, the breed options automatically update based on the selected species (e.g., selecting 'Dog' only shows dog breeds). This is implemented across both Admin and Shelter interfaces.
- **"Already Adopted" Indicators**: The public UI correctly greys out adopted pets, applies a visual badge, and disables their application buttons dynamically.

## Setup Instructions

1. **Clone the repository** to your local server directory (e.g., `htdocs` for XAMPP).
2. **Create Database**: Open PHPMyAdmin (or your preferred MySQL client) and create a new database named `pawsome_connections`.
3. **Import Database**: Import the `database/schema.sql` file into your newly created `pawsome_connections` database.
4. **Configuration**:
   - Navigate to the `config/` directory.
   - You will see a `config.example.php`. Rename it or copy it to `config.php`.
   - Open `config.php` and configure the **SMTP Settings** (Google App Password) if you want email notifications to work.
   - If your database username/password is not `root` / `(empty)`, update `config/database.php` as well.
5. **Directory Permissions**: Ensure that the `uploads/` directory is writable by your web server if you plan to upload new pet images or shelter logos. (The `uploads/` directory tracks user-generated content).

## Access Accounts
The system contains pre-seeded default data. 

**Default Admin Credentials:**
- **Email:** `admin@pawsome.com`
- **Password:** `Admin@1234`

*(Adopter and Shelter accounts are also seeded, passwords default to `Admin@1234`)*
