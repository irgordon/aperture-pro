📸 Aperture Pro
Professional Photography Proofing & Delivery Platform for WordPress
Aperture Pro is a modern, production‑grade WordPress plugin that transforms WordPress into a full photography proofing, delivery, and client‑communication platform. It is built with a modular architecture, background job processing, token‑secured client portals, and a premium admin UI.

This plugin is engineered for performance, reliability, and long‑term maintainability — ideal for high‑volume studios and SaaS‑style deployments.

🚀 Features
Client Portal
Token‑secured access (no login required)

Proofing gallery with approve/reject/revision workflow

Delivery page with ZIP download links

Mobile‑optimized, modern UI

Fully REST‑driven with skeleton loaders and toasts

Admin Dashboard
Project list + detail view with tabbed interface

Proofing management tools

Delivery management (ZIP generation, link sending)

Background job viewer

Health dashboard (schema, storage, cron)

Proofing Workflow
Multi‑round proofing (open → submitted → reopened)

Per‑image statuses + notes

Submission messaging

Admin unlock + completion actions

Event system for notifications and automations

Delivery Workflow
Background ZIP generation via job queue

Storage adapters (Local, S3‑ready architecture)

Token‑secured download links

Delivery state tracking

Background Jobs
Job queue with retries, dead‑lettering, and logging

ZIP generation job

Extensible job types

Admin job viewer

Storage Layer
Pluggable storage adapters

Local storage adapter included

S3 adapter ready to be added

Health checks and path isolation

Security
Token‑based access control

Middleware for permissions and state validation

Sanitization and enum validation utilities

No reliance on WP user accounts for clients

🧱 Architecture Overview
Aperture Pro is built using a clean, domain‑driven folder structure:

Code
src/
├─ Admin/           # Admin UI screens
├─ Client/          # Client portal shortcode + assets
├─ Core/            # Autoloader, installer, hooks, schema
├─ Domain/
│  ├─ Jobs/         # Job queue system
│  ├─ Proofing/     # Proofing workflow
│  ├─ Tokens/       # Access + download tokens
│  ├─ Delivery/     # ZIP generation + delivery state
│  └─ Project/      # Project stages
├─ Http/
│  ├─ Middleware/   # Permissions, validation, input
│  └─ Rest/         # REST API controllers
├─ Storage/         # Storage adapters + manager
└─ Support/         # Utilities (Sanitize, Enum, Error, Date, etc.)
Assets:

Code
assets/
├─ admin.css
├─ admin.js
├─ client.css
├─ client.js
└─ components.css
Templates:

Code
templates/
├─ admin/
└─ client/
🛠 Installation
Upload the plugin folder to:

Code
/wp-content/plugins/aperture-pro/
Activate the plugin in WordPress → Plugins.

The plugin automatically:

Creates required database tables

Registers REST routes

Loads admin UI

Enables the client portal shortcode

🧩 Client Portal Usage
Create a WordPress page and add:

Code
[aperture_pro_portal]
Then send clients a link like:

Code
https://your-site.com/client-portal/?token=ACCESS_TOKEN_HERE
Tokens are generated automatically when a project is created or when delivery links are sent.

🧵 Background Jobs
Aperture Pro includes a full job queue:

queued → running → succeeded

Automatic retries

Dead‑letter queue

Logging + admin viewer

Jobs are triggered via:

php
JobScheduler::enqueue($project_id, JobTypes::ZIP_GENERATION);
🗄 Storage Adapters
Aperture Pro ships with:

LocalStorageAdapter (default)

The architecture supports additional adapters:

Amazon S3

DigitalOcean Spaces

Wasabi

Google Cloud Storage

Adapters are managed via:

php
StorageManager::adapter()->store($localPath, $targetPath);
🔌 Extending Aperture Pro
Add a new job type
Implement a handler in JobRunner and enqueue via:

php
JobScheduler::enqueue($project_id, 'my_custom_job', ['foo' => 'bar']);
Add a new storage adapter
Implement StorageAdapterInterface and register it in StorageManager.

Add new REST endpoints
Create a controller in src/Http/Rest/ and register routes in aperture-pro.php.

Add new admin tabs
Hook into:

Code
do_action('ap_admin_project_tabs', $project_id);
🧪 Development Notes
Fully PSR‑4 autoloaded

No global functions except helper wrappers

All state transitions validated via middleware

All REST responses use Response utility

All errors logged via Logger

All user input sanitized via Sanitize

🛡 Security Model
Clients authenticate via single‑use, expiring tokens

Admins authenticate via WordPress capabilities

All REST endpoints use middleware:

Permissions

Token validation

Project stage validation

No sensitive data stored in browser

No direct file access — all via storage adapter

📄 License
Commercial license.
All rights reserved.

💬 Support
For support, feature requests, or custom integrations, contact your Aperture Pro development team.
