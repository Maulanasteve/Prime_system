# Prime Cargo Limited - Automated Clearance System

## Table of Contents

1. [Overview](#overview)
2. [System Purpose](#system-purpose)
3. [User Roles & Permissions](#user-roles--permissions)
4. [Core Features](#core-features)
5. [Technology Stack](#technology-stack)
6. [System Architecture](#system-architecture)
7. [Database Schema](#database-schema)
8. [User Workflows](#user-workflows)
9. [File Structure](#file-structure)
10. [Security Features](#security-features)
11. [Configuration](#configuration)
12. [Installation & Setup](#installation--setup)

---

## Overview

The **Prime Cargo Limited Automated Clearance System** is a comprehensive web-based logistics and cargo management platform designed to streamline the customs clearance process for shipments in Malawi. The system operates under the Malawi Revenue Authority (MRA) framework and automates interactions between cargo handlers, customs agents, warehouse keepers, and clients.

**Company**: Prime Cargo Limited
**Location**: Blantyre Chileka Airport, Malawi
**Platform Type**: Web Application
**Industry**: Logistics & Customs Clearance

---

## System Purpose

### Primary Objectives

- **Digitize** the cargo clearance workflow from submission to release
- **Automate** tax calculations and customs duty computations
- **Streamline** communication between stakeholders (clients, agents, keepers, admin)
- **Track** shipments through the entire clearance lifecycle
- **Manage** documentation with secure upload and verification
- **Process** payments and maintain financial records
- **Ensure** compliance with Malawi Revenue Authority regulations

### Key Benefits

- Reduces manual paperwork and processing time
- Provides real-time shipment tracking
- Ensures accurate tax calculations with live exchange rates
- Maintains complete audit trails for compliance
- Improves transparency in the clearance process
- Centralizes document management

---

## User Roles & Permissions

The system implements Role-Based Access Control (RBAC) with four distinct user types:

### 1. Admin

**Role**: System administrator with full oversight
**Permissions**:

- Manage all users and assign roles
- Issue manifest numbers to shipments
- Assign and manage TPIN (Tax Payer ID Numbers)
- Approve or reject clearance requests
- Generate and issue release orders
- View complete activity logs and system reports
- Manage payments and transaction records
- Send system-wide communications
- Access all modules and dashboards

### 2. Agent (Customs Clearance Specialist)

**Role**: Process customs declarations and tax calculations
**Permissions**:

- View assigned shipments
- Calculate taxes and customs duties
- Create customs declarations
- Request additional documents from clients
- Communicate with clients and keepers
- Track shipment clearance status
- Submit clearance documentation to admin
- Process payment information
- Cannot: Issue manifests, approve clearances, manage users

### 3. Keeper (Warehouse Verification Specialist)

**Role**: Verify physical goods against documentation
**Permissions**:

- View assigned shipments
- Verify shipment documents
- Inspect physical goods
- Record verification status (passed/failed)
- Communicate with agents about discrepancies
- Add verification notes
- Cannot: Calculate taxes, issue releases, manage users

### 4. Client (Cargo Owner/Business)

**Role**: Submit shipments and track progress
**Permissions**:

- Create new shipment submissions
- Upload supporting documents
- Track shipment status in real-time
- View assigned agent and keeper
- Make payments for clearance fees
- Communicate with assigned agent
- View payment history
- Download release orders
- Cannot: Access other clients' data, approve clearances

---

## Core Features

### 1. Shipment Management

- **Tracking System**: Unique tracking numbers (format: PC2025-XXXX)
- **Status Tracking**: 9-stage lifecycle monitoring
- **Multi-Currency**: Support for 10+ currencies (USD, EUR, GBP, CNY, INR, ZAR, KES, NGN, GHS, UGX)
- **Data Capture**: Goods description, weight, value, origin, destination
- **Agent Assignment**: Automatic or manual agent allocation
- **Expected Dates**: Clearance timeline management

**Shipment Lifecycle States**:
```
1. pending
2. under_verification
3. under_clearance
4. clearance_approved
5. clearance_rejected
6. manifest_issued
7. release_issued
8. completed
9. cancelled

```

### 2. Document Management

**Supported Document Types**:

- Commercial Invoice
- Packing List
- Bill of Lading/Airway Bill
- Certificate of Origin
- Import Permit
- Insurance Certificate
- Inspection Certificate
- Tax Invoice
- Other (custom)

**Features**:

- Secure file upload (max 10MB per file)
- Supported formats: PDF, DOC, DOCX, JPG, PNG, XLS, XLSX
- Document verification workflow
- Version control and audit trail
- Organized storage by shipment
- Secure download with access control
- Verification notes and status tracking

### 3. Customs Clearance Processing

**Tax Calculation Engine**:

- Harmonized System (HS) tariff code lookup
- Real-time currency exchange rates (via ExchangeRate-API)
- Import duty calculation based on tariff percentages
- VAT calculation (Malawi rate: 16.5%)
- Multi-currency support with automatic MWK conversion
- Fallback exchange rates when API unavailable

**Calculation Formula**:
```
Value in MWK = Declared Value × Exchange Rate
Import Duty = Value in MWK × Tariff %
Subtotal = Value in MWK + Import Duty
VAT = Subtotal × 16.5%
Total Tax = Import Duty + VAT
```

**Administrative Functions**:

- Manifest number assignment
- Release order generation with unique numbers
- Clearance approval/rejection workflow
- Declaration submission and review

### 4. Payment System

**Features**:

- Payment status tracking (pending, partial, completed)
- Multi-currency payment support
- Currency conversion to Malawi Kwacha (MWK)
- Transaction ID recording
- Payment method tracking
- Payment history and receipts
- Tax amount breakdown

**Payment Workflow**:

1. Agent calculates taxes
2. Payment record created (status: pending)
3. Client views amount in original currency + MWK
4. Client completes payment
5. Payment status updated to completed
6. Admin issues release order

### 5. Communication System

**Message Types**:

- General updates
- Document requests
- Clearance status updates
- Payment reminders
- Verification issues

**Features**:

- Agent-to-Client messaging
- Agent-to-Keeper messaging
- Admin-to-Agent messaging
- Message threading per shipment
- Read/unread tracking
- Notification system
- Email integration (PHPMailer - configured)

### 6. Verification System

**Keeper Verification Process**:

1. Review uploaded documents
2. Compare against physical goods
3. Check quantities and descriptions
4. Verify document authenticity
5. Record verification status
6. Add verification notes
7. Communicate discrepancies to agent

**Verification Records**:

- Goods verification (passed/failed)
- Document verification (verified/rejected)
- Keeper notes and observations
- Timestamp tracking
- Status management

### 7. Administrative Dashboard

**Key Metrics**:

- Total shipments count
- Pending clearances
- New submissions
- Active users (by role)
- Payment statistics
- Revenue tracking

**Admin Functions**:

- User management (create, deactivate, reset passwords)
- Agent TPIN assignment and management
- Manifest number issuance
- Release order generation
- Activity log viewing (complete audit trail)
- System reports and analytics
- Payment reconciliation

---

## Technology Stack

### Backend Technologies

| Component | Technology | Version |
|-----------|------------|---------|
| Language | PHP | 7.4+ |
| Database | MySQL/MariaDB | 5.7+ / 10.2+ |
| DB Driver | PDO (PHP Data Objects) | Built-in |
| Email | PHPMailer | Latest |
| Password Hashing | Bcrypt (PASSWORD_DEFAULT) | Built-in |
| Character Encoding | UTF-8mb4 | Full Unicode |

### Frontend Technologies

| Component | Technology | Version |
|-----------|------------|---------|
| Markup | HTML5 | Latest |
| CSS Framework | Bootstrap | 5.1.3 |
| Icons | Font Awesome | 6.0.0 |
| JavaScript | Vanilla JS | ES6+ |
| Styling | Custom CSS | - |

### Security Libraries

- **PDO Prepared Statements**: SQL injection prevention
- **CSRF Token**: Cross-site request forgery protection
- **Security Headers**: X-Frame-Options, X-Content-Type-Options, X-XSS-Protection
- **Session Management**: Secure session handling with timeout

### External APIs

- **ExchangeRate-API**: `https://api.exchangerate.host`
  - Real-time currency conversion
  - Fallback rates configured in config.php

### Server Requirements

- Apache/Nginx web server
- PHP 7.4 or higher
- MySQL 5.7+ or MariaDB 10.2+
- 100MB+ storage for file uploads
- Session support enabled
- File upload support (10MB max)
- mod_rewrite enabled (if using URL rewriting)

---

## System Architecture

### Architectural Layers

```
┌─────────────────────────────────────────────────┐
│          Client Layer (Web Browser)             │
│     HTML5 + Bootstrap 5 + JavaScript            │
└──────────────────┬──────────────────────────────┘
                   │ HTTPS
┌──────────────────▼──────────────────────────────┐
│        Presentation Layer (PHP Views)           │
│  Login, Dashboards, Forms, Reports              │
└──────────────────┬──────────────────────────────┘
                   │
┌──────────────────▼──────────────────────────────┐
│      Authentication & Authorization Layer       │
│  - Session Management (auth.php)                │
│  - RBAC (Role-Based Access Control)             │
│  - CSRF Protection                              │
└──────────────────┬──────────────────────────────┘
                   │
┌──────────────────▼──────────────────────────────┐
│          Business Logic Layer (PHP)             │
│  ┌──────────────────────────────────────────┐  │
│  │ Shipment Module                          │  │
│  │ - new_shipment.php                       │  │
│  │ - track_shipment.php                     │  │
│  │ - agent_shipments.php                    │  │
│  └──────────────────────────────────────────┘  │
│  ┌──────────────────────────────────────────┐  │
│  │ Document Module                          │  │
│  │ - upload_document.php                    │  │
│  │ - verify_document.php                    │  │
│  │ - FileHandler.php                        │  │
│  └──────────────────────────────────────────┘  │
│  ┌──────────────────────────────────────────┐  │
│  │ Tax & Clearance Module                   │  │
│  │ - agent_tax_calculation.php              │  │
│  │ - agent_declaration.php                  │  │
│  │ - admin_clearance_approval.php           │  │
│  └──────────────────────────────────────────┘  │
│  ┌──────────────────────────────────────────┐  │
│  │ Payment Module                           │  │
│  │ - payment.php                            │  │
│  │ - admin_payments.php                     │  │
│  └──────────────────────────────────────────┘  │
│  ┌──────────────────────────────────────────┐  │
│  │ Communication Module                     │  │
│  │ - agent_messaging.php                    │  │
│  │ - agent_keeper_communication.php         │  │
│  └──────────────────────────────────────────┘  │
│  ┌──────────────────────────────────────────┐  │
│  │ Admin Module                             │  │
│  │ - admin_manifest_management.php          │  │
│  │ - admin_release_orders.php               │  │
│  │ - admin_user_management.php              │  │
│  └──────────────────────────────────────────┘  │
└──────────────────┬──────────────────────────────┘
                   │
┌──────────────────▼──────────────────────────────┐
│        Data Access Layer (database.php)         │
│  - PDO Connection Class                         │
│  - Prepared Statements                          │
│  - Transaction Management                       │
│  - Connection Pooling                           │
└──────────────────┬──────────────────────────────┘
                   │
┌──────────────────▼──────────────────────────────┐
│          Database Layer (MySQL)                 │
│  11 Tables with Referential Integrity           │
│  - users, roles, clients                        │
│  - shipments, shipment_documents                │
│  - verification, payments                       │
│  - manifests, tpin_assignments                  │
│  - messages, activity_log, notifications        │
└─────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────┐
│          File Storage Layer                     │
│  /uploads/documents/shipments/{shipment_id}/    │
│  - Secure file access                           │
│  - MIME type validation                         │
└─────────────────────────────────────────────────┘
```

### Data Flow: Complete Shipment Lifecycle

```
┌──────────────┐
│ 1. CLIENT    │ Creates shipment → Status: pending
│ SUBMISSION   │ Uploads documents → Stored in /uploads
└──────┬───────┘
       │
       ▼
┌──────────────┐
│ 2. KEEPER    │ Verifies documents → Status: under_verification
│ VERIFICATION │ Checks goods → Verification record created
└──────┬───────┘
       │
       ▼
┌──────────────┐
│ 3. AGENT     │ Calculates taxes → Payment record created
│ PROCESSING   │ Creates declaration → Status: under_clearance
└──────┬───────┘
       │
       ▼
┌──────────────┐
│ 4. ADMIN     │ Reviews declaration
│ APPROVAL     │ Approves clearance → Status: clearance_approved
└──────┬───────┘
       │
       ▼
┌──────────────┐
│ 5. MANIFEST  │ Admin assigns manifest number
│ ISSUANCE     │ Status: manifest_issued
└──────┬───────┘
       │
       ▼
┌──────────────┐
│ 6. PAYMENT   │ Client completes payment
│ PROCESSING   │ Payment status: completed
└──────┬───────┘
       │
       ▼
┌──────────────┐
│ 7. RELEASE   │ Admin issues release order
│ ORDER        │ Status: release_issued
└──────┬───────┘
       │
       ▼
┌──────────────┐
│ 8. GOODS     │ Client collects goods
│ COLLECTION   │ Status: completed
└──────────────┘
```

---

## Database Schema

### Core Tables (11 Total)

#### 1. users

Stores all user accounts (admin, agents, keepers, clients)

| Column | Type | Description |
|--------|------|-------------|
| user_id | INT (PK, AUTO) | Unique user identifier |
| email | VARCHAR(255) UNIQUE | User email (login username) |
| password | VARCHAR(255) | Bcrypt hashed password |
| role | ENUM | admin, agent, keeper, client |
| full_name | VARCHAR(255) | User's full name |
| phone | VARCHAR(20) | Contact number |
| status | ENUM | active, inactive, suspended |
| tpin | VARCHAR(50) | Tax Payer ID Number (agents) |
| last_login | DATETIME | Last successful login |
| failed_login_attempts | INT | Account lockout counter |
| locked_until | DATETIME | Lockout expiration time |
| created_at | TIMESTAMP | Account creation date |

**Key Relationships**:

- Referenced by: clients, shipments, messages, activity_log
- Security: Bcrypt password hashing, account lockout after 5 failed attempts

#### 2. roles

Defines role permissions (currently not fully implemented)

| Column | Type | Description |
|--------|------|-------------|
| role_id | INT (PK, AUTO) | Unique role identifier |
| role_name | VARCHAR(50) UNIQUE | admin, agent, keeper, client |
| permissions | TEXT | JSON permissions structure |
| created_at | TIMESTAMP | Role creation date |

#### 3. clients

Extended client information

| Column | Type | Description |
|--------|------|-------------|
| client_id | INT (PK, AUTO) | Unique client identifier |
| user_id | INT (FK → users) | Link to user account |
| company_name | VARCHAR(255) | Business/company name |
| business_license | VARCHAR(100) | License number |
| tax_registration | VARCHAR(100) | Tax registration number |
| address | TEXT | Physical address |
| city | VARCHAR(100) | City |
| country | VARCHAR(100) | Country |
| created_at | TIMESTAMP | Registration date |

#### 4. shipments

Core shipment records

| Column | Type | Description |
|--------|------|-------------|
| shipment_id | INT (PK, AUTO) | Unique shipment identifier |
| tracking_number | VARCHAR(50) UNIQUE | Format: PC2025-XXXX |
| client_id | INT (FK → users) | Shipment owner |
| agent_id | INT (FK → users) | Assigned agent |
| keeper_id | INT (FK → users) | Assigned keeper |
| goods_description | TEXT | Description of goods |
| weight | DECIMAL(10,2) | Weight in kg |
| value | DECIMAL(15,2) | Declared value |
| currency | VARCHAR(10) | Currency code (USD, EUR, etc.) |
| origin | VARCHAR(255) | Origin location |
| destination | VARCHAR(255) | Destination location |
| status | ENUM | Current status (9 states) |
| manifest_number | VARCHAR(50) | MRA manifest number |
| release_number | VARCHAR(50) | Release order number |
| release_date | DATETIME | Release issued date |
| tax_amount | DECIMAL(15,2) | Total tax in MWK |
| expected_clearance_date | DATE | Expected clearance date |
| created_at | TIMESTAMP | Submission date |
| updated_at | TIMESTAMP | Last update |

**Status Enum Values**:

- pending
- under_verification
- under_clearance
- clearance_approved
- clearance_rejected
- manifest_issued
- release_issued
- completed
- cancelled

#### 5. shipment_documents

Document management

| Column | Type | Description |
|--------|------|-------------|
| document_id | INT (PK, AUTO) | Unique document identifier |
| shipment_id | INT (FK → shipments) | Associated shipment |
| document_type | VARCHAR(100) | Type of document |
| file_path | VARCHAR(255) | Storage path |
| file_name | VARCHAR(255) | Original filename |
| uploaded_by | INT (FK → users) | User who uploaded |
| verified | BOOLEAN | Verification status |
| verified_by | INT (FK → users) | Keeper who verified |
| verified_at | DATETIME | Verification timestamp |
| verification_notes | TEXT | Keeper notes |
| uploaded_at | TIMESTAMP | Upload timestamp |

**Document Types**:

- commercial_invoice
- packing_list
- bill_of_lading
- certificate_of_origin
- import_permit
- insurance_certificate
- inspection_certificate
- tax_invoice
- other

#### 6. verification

Keeper verification records

| Column | Type | Description |
|--------|------|-------------|
| verification_id | INT (PK, AUTO) | Unique verification ID |
| shipment_id | INT (FK → shipments) | Associated shipment |
| keeper_id | INT (FK → users) | Verifying keeper |
| goods_verified | BOOLEAN | Goods match documents |
| documents_verified | BOOLEAN | Documents complete |
| verification_notes | TEXT | Keeper observations |
| status | ENUM | pending, completed, failed |
| verified_at | TIMESTAMP | Verification date |

#### 7. payments

Payment tracking

| Column | Type | Description |
|--------|------|-------------|
| payment_id | INT (PK, AUTO) | Unique payment identifier |
| shipment_id | INT (FK → shipments) | Associated shipment |
| amount | DECIMAL(15,2) | Amount in original currency |
| currency | VARCHAR(10) | Currency code |
| amount_mwk | DECIMAL(15,2) | Amount in Malawi Kwacha |
| payment_method | VARCHAR(50) | Payment method used |
| transaction_id | VARCHAR(100) | Transaction reference |
| status | ENUM | pending, partial, completed |
| payment_date | DATETIME | Payment completion date |
| created_at | TIMESTAMP | Record creation date |

#### 8. manifests

Agent manifest assignments

| Column | Type | Description |
|--------|------|-------------|
| manifest_id | INT (PK, AUTO) | Unique manifest identifier |
| agent_id | INT (FK → users) | Agent assigned |
| manifest_number | VARCHAR(50) UNIQUE | Manifest reference number |
| issue_date | DATE | Date issued |
| status | ENUM | active, closed |
| created_at | TIMESTAMP | Creation date |

#### 9. tpin_assignments

Agent TPIN management

| Column | Type | Description |
|--------|------|-------------|
| tpin_id | INT (PK, AUTO) | Unique TPIN identifier |
| agent_id | INT (FK → users) | Agent assigned |
| tpin_number | VARCHAR(50) UNIQUE | Tax Payer ID Number |
| issue_date | DATE | Date issued |
| expiry_date | DATE | Expiration date |
| status | ENUM | active, inactive, suspended |
| created_at | TIMESTAMP | Creation date |

#### 10. messages

Communication system

| Column | Type | Description |
|--------|------|-------------|
| message_id | INT (PK, AUTO) | Unique message identifier |
| sender_id | INT (FK → users) | Message sender |
| recipient_id | INT (FK → users) | Message recipient |
| shipment_id | INT (FK → shipments) | Related shipment (optional) |
| subject | VARCHAR(255) | Message subject |
| message | TEXT | Message content |
| message_type | ENUM | Message category |
| is_read | BOOLEAN | Read status |
| created_at | TIMESTAMP | Sent timestamp |

**Message Types**:

- general
- document_request
- clearance_update
- payment_reminder
- verification_issue

#### 11. activity_log

Complete audit trail

| Column | Type | Description |
|--------|------|-------------|
| log_id | INT (PK, AUTO) | Unique log identifier |
| user_id | INT (FK → users) | User who performed action |
| action | VARCHAR(255) | Action description |
| table_name | VARCHAR(100) | Table affected |
| record_id | INT | Record affected |
| details | TEXT | Additional details |
| ip_address | VARCHAR(45) | User IP address |
| created_at | TIMESTAMP | Action timestamp |

**Common Actions**:

- shipment_created
- document_uploaded
- payment_completed
- manifest_issued
- release_order_generated
- user_login
- clearance_approved

#### 12. notifications

System notifications

| Column | Type | Description |
|--------|------|-------------|
| notification_id | INT (PK, AUTO) | Unique notification ID |
| user_id | INT (FK → users) | Recipient user |
| title | VARCHAR(255) | Notification title |
| message | TEXT | Notification content |
| type | VARCHAR(50) | Notification type |
| is_read | BOOLEAN | Read status |
| created_at | TIMESTAMP | Creation timestamp |

### Database Relationships

```
users (1) ──────┬──────> (N) shipments (as client_id)
                ├──────> (N) shipments (as agent_id)
                ├──────> (N) shipments (as keeper_id)
                ├──────> (N) clients
                ├──────> (N) shipment_documents (as uploaded_by)
                ├──────> (N) verification
                ├──────> (N) manifests
                ├──────> (N) tpin_assignments
                ├──────> (N) messages (as sender_id)
                ├──────> (N) messages (as recipient_id)
                ├──────> (N) activity_log
                └──────> (N) notifications

shipments (1) ──┬──────> (N) shipment_documents
                ├──────> (N) verification
                ├──────> (N) payments
                └──────> (N) messages
```

---

## User Workflows

### Client Complete Workflow

```
STEP 1: REGISTRATION & LOGIN
├─ Navigate to registration page
├─ Enter: email, password, full name, phone, company name
├─ Submit registration
├─ Account created with status: inactive (awaiting admin approval)
├─ Admin activates account
└─ Login with email and password

STEP 2: CREATE SHIPMENT
├─ Login to dashboard
├─ Click "Create New Shipment"
├─ Fill shipment form:
│  ├─ Goods description
│  ├─ Weight (kg)
│  ├─ Declared value
│  ├─ Currency selection
│  ├─ Origin country
│  ├─ Destination address
│  └─ Expected clearance date
├─ Submit shipment
├─ System generates tracking number (PC2025-XXXX)
├─ Status set to: pending
└─ Shipment appears in "My Shipments"

STEP 3: UPLOAD DOCUMENTS
├─ Select shipment from dashboard
├─ Click "Upload Documents"
├─ Upload required documents:
│  ├─ Commercial Invoice (mandatory)
│  ├─ Packing List (mandatory)
│  ├─ Bill of Lading/Airway Bill (mandatory)
│  ├─ Certificate of Origin (if required)
│  ├─ Import Permit (if required)
│  ├─ Insurance Certificate
│  └─ Other supporting documents
├─ Each file max 10MB
├─ Supported: PDF, DOC, DOCX, JPG, PNG, XLS, XLSX
└─ Documents stored and awaiting verification

STEP 4: TRACK SHIPMENT
├─ View "Track Shipment" page
├─ Monitor status changes:
│  ├─ pending → under_verification
│  ├─ → under_clearance
│  ├─ → clearance_approved
│  ├─ → manifest_issued
│  ├─ → release_issued
│  └─ → completed
├─ View assigned agent name
├─ View assigned keeper name
├─ Check payment status
└─ Receive notifications on updates

STEP 5: RESPOND TO REQUESTS
├─ Check messages from agent
├─ If documents requested:
│  ├─ Upload additional documents
│  └─ Send confirmation message
└─ Wait for clearance approval

STEP 6: MAKE PAYMENT
├─ Receive tax calculation from agent
├─ View payment amount:
│  ├─ Original currency amount
│  ├─ Exchange rate
│  ├─ Amount in MWK
│  └─ Tax breakdown
├─ Navigate to "Make Payment"
├─ Select payment method
├─ Complete payment
├─ Enter transaction ID
├─ Submit payment confirmation
└─ Payment status: completed

STEP 7: RECEIVE RELEASE ORDER
├─ Admin issues release order
├─ Receive notification
├─ View/download release order
├─ Note release number
└─ Status: release_issued

STEP 8: COLLECT GOODS
├─ Present release order to warehouse
├─ Provide identification
├─ Collect cleared goods
├─ Shipment status updated: completed
└─ Process complete
```

### Agent Complete Workflow

```
STEP 1: VIEW ASSIGNED SHIPMENTS
├─ Login to agent dashboard
├─ View list of assigned shipments
├─ Shipments sorted by submission date
├─ Filter by status:
│  ├─ under_verification
│  ├─ under_clearance
│  └─ manifest_issued
└─ Select shipment to process

STEP 2: REVIEW SHIPMENT DETAILS
├─ View shipment information:
│  ├─ Client name and contact
│  ├─ Goods description
│  ├─ Weight and value
│  ├─ Origin and destination
│  └─ Expected clearance date
├─ View uploaded documents
├─ Check keeper verification status
└─ Identify missing information

STEP 3: REQUEST DOCUMENTS (if needed)
├─ Click "Send Message to Client"
├─ Select message type: document_request
├─ Specify required documents:
│  ├─ List missing documents
│  └─ Explain requirements
├─ Send message
├─ Client receives notification
└─ Wait for client response

STEP 4: CALCULATE TAXES
├─ Navigate to "Tax Calculation"
├─ Enter/verify shipment details
├─ Look up HS Tariff Code:
│  ├─ Search harmonized system database
│  └─ Example: 8471.30.00 (Portable computers)
├─ Enter tariff code
├─ System fetches:
│  ├─ Real-time exchange rate
│  ├─ Tariff percentage (e.g., 5%)
│  └─ VAT rate (16.5%)
├─ System calculates:
│  ├─ Value in MWK = Value × Exchange Rate
│  ├─ Import Duty = Value in MWK × Tariff %
│  ├─ Subtotal = Value in MWK + Import Duty
│  ├─ VAT = Subtotal × 16.5%
│  └─ Total Tax = Import Duty + VAT
├─ Review calculation
├─ Save tax calculation
└─ Payment record created (status: pending)

STEP 5: CREATE CUSTOMS DECLARATION
├─ Navigate to "Create Declaration"
├─ Fill declaration form:
│  ├─ Shipper information
│  ├─ Consignee information
│  ├─ Goods classification (HS code)
│  ├─ Value declaration
│  ├─ Tax calculations
│  ├─ Agent TPIN number
│  └─ Supporting document references
├─ Review declaration accuracy
├─ Submit declaration to admin
└─ Status updated: under_clearance

STEP 6: COMMUNICATE WITH KEEPER
├─ Check verification status
├─ If issues reported:
│  ├─ Review keeper notes
│  ├─ Contact keeper for clarification
│  ├─ Resolve discrepancies
│  └─ Update documents if needed
└─ Confirm verification complete

STEP 7: AWAIT ADMIN APPROVAL
├─ Monitor shipment status
├─ Admin reviews declaration
├─ If approved:
│  ├─ Status: clearance_approved
│  └─ Proceed to next step
├─ If rejected:
│  ├─ Review rejection reason
│  ├─ Correct errors
│  └─ Resubmit declaration
└─ Notify client of status

STEP 8: MONITOR PAYMENT
├─ Check payment status
├─ Send payment reminder to client if needed
├─ Confirm payment completion
└─ Ready for release order

STEP 9: FINALIZE SHIPMENT
├─ Admin issues manifest number
├─ Admin issues release order
├─ Send completion notification to client
├─ Update shipment status: completed
└─ Archive documentation
```

### Keeper Complete Workflow

```
STEP 1: VIEW ASSIGNED SHIPMENTS
├─ Login to keeper dashboard
├─ View shipments assigned for verification
├─ Filter by status: under_verification
├─ Select shipment to verify
└─ Review shipment details

STEP 2: REVIEW DOCUMENTS
├─ Access shipment documents
├─ Review each uploaded document:
│  ├─ Commercial Invoice
│  ├─ Packing List
│  ├─ Bill of Lading
│  ├─ Certificates
│  └─ Other documents
├─ Check document quality
├─ Verify document authenticity
└─ Identify any issues

STEP 3: VERIFY PHYSICAL GOODS
├─ Locate goods in warehouse
├─ Compare against documents:
│  ├─ Goods description matches invoice
│  ├─ Quantities match packing list
│  ├─ Weight matches declaration
│  ├─ Condition acceptable
│  └─ Packaging intact
├─ Inspect goods physically
└─ Note any discrepancies

STEP 4: DOCUMENT VERIFICATION
├─ For each document:
│  ├─ Mark as "verified" or "rejected"
│  ├─ Add verification notes
│  └─ Record verification timestamp
├─ If issues found:
│  ├─ Document specific problems
│  ├─ Take photos if needed
│  └─ Prepare detailed notes
└─ Complete document checklist

STEP 5: COMMUNICATE ISSUES
├─ If discrepancies found:
│  ├─ Send message to agent
│  ├─ Describe issues clearly
│  ├─ Provide recommendations
│  └─ Request clarification
├─ Await agent response
├─ Resolve issues
└─ Re-verify if corrections made

STEP 6: COMPLETE VERIFICATION
├─ Navigate to "Complete Verification"
├─ Fill verification record:
│  ├─ Goods verified: Yes/No
│  ├─ Documents verified: Yes/No
│  ├─ Overall status: completed/failed
│  └─ Final verification notes
├─ Submit verification
├─ Status updated: verified (if passed)
├─ Agent receives notification
└─ Shipment ready for clearance processing
```

### Admin Complete Workflow

```
STEP 1: MONITOR DASHBOARD
├─ Login to admin dashboard
├─ View key metrics:
│  ├─ Total shipments
│  ├─ Pending clearances
│  ├─ New submissions today
│  ├─ Active users count
│  └─ Revenue statistics
├─ Check notifications
└─ Review alerts

STEP 2: MANAGE USERS
├─ Navigate to "User Management"
├─ View all users (admins, agents, keepers, clients)
├─ Actions:
│  ├─ Create new user accounts
│  ├─ Activate/deactivate accounts
│  ├─ Reset passwords
│  ├─ Assign/change roles
│  ├─ Suspend accounts
│  └─ View user activity
└─ Monitor user performance

STEP 3: ASSIGN AGENTS TO SHIPMENTS
├─ View unassigned shipments
├─ Check agent availability/workload
├─ Assign agent to shipment
├─ Agent receives notification
└─ Shipment appears in agent's dashboard

STEP 4: MANAGE TPIN ASSIGNMENTS
├─ Navigate to "TPIN Management"
├─ View agent TPIN list
├─ Actions:
│  ├─ Assign TPIN to agent
│  ├─ Set expiry date
│  ├─ Renew expired TPINs
│  ├─ Suspend TPINs
│  └─ Track TPIN usage
└─ Ensure compliance

STEP 5: REVIEW CLEARANCE REQUESTS
├─ Navigate to "Clearance Approval"
├─ View submitted declarations
├─ Review each declaration:
│  ├─ Agent details and TPIN
│  ├─ Tax calculations
│  ├─ Supporting documents
│  ├─ Keeper verification status
│  └─ Compliance with regulations
├─ Decision: Approve or Reject
├─ If rejecting:
│  ├─ Provide rejection reason
│  └─ Specify corrections needed
├─ Update shipment status
└─ Notify agent of decision

STEP 6: ISSUE MANIFEST NUMBERS
├─ Navigate to "Manifest Management"
├─ View shipments ready for manifest (status: clearance_approved)
├─ Generate/enter manifest number
├─ Verify no duplicates
├─ Assign manifest to shipment
├─ Update status: manifest_issued
├─ Create manifest record
└─ Log activity

STEP 7: VERIFY PAYMENTS
├─ Navigate to "Payment Management"
├─ View payment records
├─ Filter by status: pending, completed
├─ Verify transaction IDs
├─ Reconcile payments
├─ Resolve payment issues
└─ Generate payment reports

STEP 8: ISSUE RELEASE ORDERS
├─ Navigate to "Release Orders"
├─ View shipments ready for release:
│  ├─ Manifest issued
│  └─ Payment completed
├─ Generate unique release number
├─ Verify all requirements met:
│  ├─ Clearance approved
│  ├─ Taxes paid
│  ├─ Documents verified
│  └─ Manifest assigned
├─ Issue release order
├─ Update status: release_issued
├─ Record release date
├─ Notify client
└─ Log activity

STEP 9: VIEW ACTIVITY LOGS
├─ Navigate to "Activity Logs"
├─ View complete audit trail:
│  ├─ User actions
│  ├─ Timestamps
│  ├─ IP addresses
│  ├─ Tables affected
│  └─ Record details
├─ Filter by:
│  ├─ User
│  ├─ Action type
│  ├─ Date range
│  └─ Shipment
├─ Export logs for compliance
└─ Investigate anomalies

STEP 10: GENERATE REPORTS
├─ Navigate to "Reports"
├─ Available reports:
│  ├─ Shipment statistics (by period)
│  ├─ Revenue reports
│  ├─ Agent performance
│  ├─ Clearance times (average)
│  ├─ Payment reconciliation
│  └─ User activity summaries
├─ Select date range
├─ Generate report
├─ Export as PDF/Excel
└─ Share with stakeholders
```

---

## File Structure

```
C:\wamp64\www\Prime_system\

├── index.php                              # Entry point (redirects to login)
├── config.php                             # Centralized configuration
├── database.php                           # Database connection class
├── auth.php                               # Authentication logic

├── Authentication & User Management
│   ├── login.php                          # User login page
│   ├── logout.php                         # Session termination
│   ├── register.php                       # New user registration
│   ├── forgot_password.php                # Password reset request
│   ├── reset_password.php                 # Password reset confirmation
│   └── profile.php                        # User profile management

├── Client Pages (Role: client)
│   ├── dashboard.php                      # Client dashboard
│   ├── new_shipment.php                   # Create shipment
│   ├── upload_document.php                # Upload documents
│   ├── track_shipment.php                 # Track shipment status
│   ├── payment.php                        # Make payment
│   ├── client_payments.php                # Payment history
│   ├── documents.php                      # Document management
│   └── document_guide.php                 # Document requirements guide

├── Agent Pages (Role: agent)
│   ├── agent_dashboard.php                # Agent dashboard
│   ├── agent_shipments.php                # View assigned shipments
│   ├── agent_declaration.php              # Create customs declaration
│   ├── agent_tax_calculation.php          # Calculate taxes
│   ├── agent_clearance.php                # Process clearance
│   ├── agent_documents.php                # Review documents
│   ├── agent_messaging.php                # Message clients
│   ├── agent_keeper_communication.php     # Message keepers
│   ├── agent_payments.php                 # View payments
│   ├── agent_review_tasks.php             # Review tasks
│   └── agent_clients.php                  # Manage clients

├── Keeper Pages (Role: keeper)
│   ├── verify_document.php                # Verify documents & goods
│   ├── keeper_shipments.php               # View assigned shipments
│   └── keeper_payments.php                # View payment status

├── Admin Pages (Role: admin)
│   ├── admin_dashboard.php                # Admin dashboard
│   ├── admin_manifest_management.php      # Manage manifests
│   ├── admin_tpin_management.php          # Manage TPINs
│   ├── admin_release_orders.php           # Issue release orders
│   ├── admin_clearance_approval.php       # Approve clearances
│   ├── admin_shipment_management.php      # Manage all shipments
│   ├── admin_user_management.php          # User management
│   ├── admin_agent_communication.php      # Communicate with agents
│   ├── admin_activity_logs.php            # View activity logs
│   ├── admin_payments.php                 # Manage payments
│   └── admin_reports.php                  # Generate reports

├── Utility Files
│   ├── document_types.php                 # Document type definitions
│   ├── delete_document.php                # Delete document handler
│   ├── download.php                       # Secure document download
│   ├── mark_notification_read.php         # Mark notification read
│   └── debug_agents.php                   # Debugging utilities

├── includes/
│   └── FileHandler.php                    # File upload/download security

├── assets/
│   ├── css/
│   │   └── style.css                      # Custom styles
│   └── js/
│       └── script.js                      # Frontend JavaScript

├── uploads/                               # File storage directory
│   └── documents/
│       └── shipments/
│           └── {shipment_id}/             # Organized by shipment
│               ├── commercial_invoice_*.pdf
│               ├── packing_list_*.pdf
│               └── ...

└── Database Schema Files
    ├── database_schema.sql                # Complete database schema
    ├── setup_database.php                 # Database initialization
    ├── add_declarations_table.sql         # Migration: declarations
    ├── add_expected_clearance_date.sql    # Migration: clearance date
    ├── alter_declarations_table.sql       # Migration: alter declarations
    └── messages_table.sql                 # Migration: messages

Total: ~45 PHP files + SQL schemas + assets
```

### Key Files Description

| File | Purpose | Access Level |
|------|---------|--------------|
| config.php | Central configuration (DB, security, rates) | All |
| database.php | PDO database connection class | All |
| auth.php | Session validation & RBAC | All pages |
| login.php | Authentication with lockout protection | Public |
| dashboard.php | Client shipment overview | Client |
| agent_tax_calculation.php | Tax calculation engine | Agent |
| verify_document.php | Document/goods verification | Keeper |
| admin_release_orders.php | Release order generation | Admin |
| FileHandler.php | Secure file upload/download | All |

---

## Security Features

### 1. Authentication & Authorization

- **Password Security**

  - Bcrypt hashing (PASSWORD_DEFAULT, cost factor 12)
  - Minimum 8 characters required
  - No password storage in plain text

- **Session Management**
  - Secure session handling with PHP sessions
  - Session timeout: 3600 seconds (1 hour)
  - Session fixation prevention
  - Session regeneration on role change

- **Account Lockout**
  - Max failed login attempts: 5
  - Lockout duration: 900 seconds (15 minutes)
  - Tracked in `users.failed_login_attempts` and `users.locked_until`

- **Role-Based Access Control (RBAC)**
  - Every page checks `$_SESSION['role']`
  - Unauthorized access redirects to login
  - Role-specific functionality restrictions

### 2. Data Security

- **SQL Injection Prevention**

  - PDO prepared statements for all queries
  - No direct string concatenation in SQL
  - Parameterized queries exclusively

- **Cross-Site Scripting (XSS) Prevention**
  - `htmlspecialchars()` on all user input display
  - ENT_QUOTES flag for comprehensive escaping
  - Content Security Policy headers

- **CSRF Protection**
  - Token generation for forms
  - Token validation on submission
  - Tokens stored in session
  - One-time use tokens for critical actions

### 3. File Upload Security

- **MIME Type Validation**

  - Whitelist of allowed types: PDF, DOC, DOCX, JPG, PNG, XLS, XLSX
  - Server-side MIME check via `finfo_file()`
  - Extension validation

- **File Size Limits**
  - Maximum upload size: 10MB per file
  - Enforced in PHP and HTML

- **Secure Storage**
  - Files stored outside web root (recommended)
  - Organized by shipment ID
  - Random filename generation
  - Access control via download.php

- **Content Scanning**
  - FileHandler.php validates all uploads
  - Rejects executable files
  - Strips metadata (optional)

### 4. Security Headers

```php
header("X-Frame-Options: DENY");                    // Prevent clickjacking
header("X-Content-Type-Options: nosniff");          // Prevent MIME sniffing
header("X-XSS-Protection: 1; mode=block");          // XSS filter
header("Strict-Transport-Security: max-age=31536000"); // HTTPS only
header("Content-Security-Policy: default-src 'self'"); // CSP
```

### 5. Activity Logging

- **Comprehensive Audit Trail**

  - All user actions logged to `activity_log` table
  - Logs include: user ID, action, timestamp, IP address
  - Immutable log records
  - Retention policy for compliance

- **Logged Actions**
  - User login/logout
  - Shipment creation/updates
  - Document uploads
  - Payment processing
  - Manifest issuance
  - Release order generation
  - User management actions
  - Clearance approvals/rejections

### 6. Input Validation

- **Server-Side Validation**

  - Email format validation
  - Phone number format
  - Numeric field validation
  - Required field checks
  - Data type enforcement

- **Sanitization**
  - `trim()` on all text inputs
  - `filter_var()` for email validation
  - Regular expressions for format validation
  - Whitelist validation for enums

### 7. Database Security

- **Connection Security**

  - PDO with error mode: EXCEPTION
  - No error display to users (production)
  - Secure credential storage (config.php)
  - Connection timeout limits

- **Data Integrity**
  - Foreign key constraints
  - UNIQUE constraints on sensitive fields
  - NOT NULL requirements
  - DEFAULT values for safety

### 8. Additional Security Measures

- **IP Address Logging**

  - Login attempts tracked by IP
  - Activity log includes IP
  - Useful for forensics

- **Email Verification** (Planned)
  - PHPMailer configured
  - Email verification on registration
  - Password reset via email

- **Two-Factor Authentication** (Future Enhancement)
  - Not currently implemented
  - Recommended for admin accounts

---

## Configuration

### Database Configuration (config.php)

```php
// Database credentials
define('DB_HOST', 'localhost');
define('DB_NAME', 'prime_cargo_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');
```

### Security Configuration

```php
// Session timeout (1 hour)
define('SESSION_TIMEOUT', 3600);

// Maximum failed login attempts
define('MAX_LOGIN_ATTEMPTS', 5);

// Account lockout duration (15 minutes)
define('LOCKOUT_TIME', 900);

// Password minimum length
define('MIN_PASSWORD_LENGTH', 8);

// CSRF token name
define('CSRF_TOKEN_NAME', 'csrf_token');
```

### File Upload Configuration

```php
// Maximum upload file size (10MB)
define('MAX_UPLOAD_SIZE', 10485760);

// Allowed file types
define('ALLOWED_FILE_TYPES', [
    'application/pdf',
    'application/msword',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'image/jpeg',
    'image/png',
    'application/vnd.ms-excel',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
]);

// Upload directory
define('UPLOAD_DIR', __DIR__ . '/uploads/documents/');
```

### Tax Configuration

```php
// VAT rate for Malawi
define('VAT_RATE', 0.165); // 16.5%

// Customs duty rates (example)
define('CUSTOMS_DUTY_RATES', [
    'electronics' => 0.05,  // 5%
    'textiles' => 0.10,     // 10%
    'machinery' => 0.05,    // 5%
    'vehicles' => 0.25,     // 25%
    // ... more categories
]);
```

### Currency Exchange Rates

```php
// Exchange rate API
define('EXCHANGE_RATE_API', 'https://api.exchangerate.host/latest');

// Fallback exchange rates (MWK per unit)
define('FALLBACK_EXCHANGE_RATES', [
    'USD' => 1630.00,
    'EUR' => 1750.00,
    'GBP' => 2050.00,
    'CNY' => 230.00,
    'INR' => 19.50,
    'ZAR' => 85.00,
    'KES' => 12.50,
    'NGN' => 2.10,
    'GHS' => 135.00,
    'UGX' => 0.43
]);
```

### Email Configuration (PHPMailer)

```php
// SMTP settings (configure for production)
define('SMTP_HOST', 'smtp.example.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'noreply@primecargo.mw');
define('SMTP_PASSWORD', 'your-smtp-password');
define('SMTP_FROM_EMAIL', 'noreply@primecargo.mw');
define('SMTP_FROM_NAME', 'Prime Cargo Limited');
```

---

## Installation & Setup

### System Requirements

```
Server:
- Apache 2.4+ or Nginx 1.18+
- PHP 7.4 or higher (8.0+ recommended)
- MySQL 5.7+ or MariaDB 10.2+

PHP Extensions:
- pdo
- pdo_mysql
- mbstring
- fileinfo
- gd (for image processing)
- curl (for exchange rate API)

Storage:
- 100MB+ for application files
- 500MB+ for document storage (scalable)

Network:
- Internet access for exchange rate API
- Email server access (SMTP) for notifications
```

### Installation Steps

#### 1. Download/Clone Repository

```bash

# Clone repository or download files
git clone https://github.com/your-repo/prime-cargo-system.git
cd prime-cargo-system
```

#### 2. Configure Web Server

**Apache (httpd.conf or .htaccess)**

```apache

<Directory "C:/wamp64/www/Prime_system">
    Options -Indexes +FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>

# Enable mod_rewrite if using URL rewriting
LoadModule rewrite_module modules/mod_rewrite.so
```

**Nginx (nginx.conf)**

```nginx

server {
    listen 80;
    server_name primecargo.local;
    root C:/wamp64/www/Prime_system;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

#### 3. Create Database

**Option A: Using MySQL Command Line**

```bash

mysql -u root -p
```

```sql
CREATE DATABASE prime_cargo_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'prime_user'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON prime_cargo_db.* TO 'prime_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

**Option B: Using phpMyAdmin**

1. Navigate to http://localhost/phpmyadmin
2. Create new database: `prime_cargo_db`
3. Set collation: `utf8mb4_unicode_ci`

#### 4. Import Database Schema

**Command Line:**

```bash
mysql -u root -p prime_cargo_db < database_schema.sql
```

**phpMyAdmin:**

1. Select `prime_cargo_db` database
2. Click "Import" tab
3. Choose `database_schema.sql` file
4. Click "Go"

#### 5. Configure Application

Edit `config.php`:

```php
// Update database credentials
define('DB_HOST', 'localhost');
define('DB_NAME', 'prime_cargo_db');
define('DB_USER', 'prime_user');
define('DB_PASS', 'secure_password');

// Update site URL
define('SITE_URL', 'http://localhost/Prime_system');

// Configure email (if using)
define('SMTP_HOST', 'smtp.yourhost.com');
define('SMTP_USERNAME', 'noreply@primecargo.mw');
define('SMTP_PASSWORD', 'your-password');
```

#### 6. Set Directory Permissions

**Linux/Unix:**

```bash
# Make uploads directory writable
chmod 755 uploads/
chmod 755 uploads/documents/

# Set ownership (if using Apache)
chown -R www-data:www-data uploads/
```

**Windows:**

```

Right-click uploads folder
Properties > Security
Grant "Modify" permissions to IUSR and IIS_IUSRS
```

#### 7. Create Admin Account

**Option A: Use setup_database.php**

```bash
# Run the setup script
php setup_database.php
```

**Option B: Direct SQL Insert**

```sql
USE prime_cargo_db;

INSERT INTO users (email, password, role, full_name, phone, status, created_at)
VALUES (
    'admin@primecargo.mw',
    '$2y$10$abcdefghijklmnopqrstuvwxyz1234567890ABCDEFGHIJKLMNOPQR',  -- Change this
    'admin',
    'System Administrator',
    '+265888123456',
    'active',
    NOW()
);
```

Generate password hash:

```php
<?php
echo password_hash('YourSecurePassword', PASSWORD_DEFAULT);
?>
```

#### 8. Test Installation

1. Navigate to: `http://localhost/Prime_system`
2. You should see login page
3. Login with admin credentials
4. Verify dashboard loads correctly
5. Test creating a test client account
6. Test file upload functionality

#### 9. Security Hardening (Production)

```php
// In config.php, set to production mode
define('ENVIRONMENT', 'production');
define('DISPLAY_ERRORS', false);

// In php.ini
display_errors = Off
log_errors = On
error_log = /var/log/php/error.log

// Set secure session settings
session.cookie_httponly = 1
session.cookie_secure = 1  // If using HTTPS
session.use_strict_mode = 1
```

#### 10. Configure SSL/HTTPS (Recommended)

**Apache:**

```apache
<VirtualHost *:443>
    ServerName primecargo.mw
    DocumentRoot "C:/wamp64/www/Prime_system"

    SSLEngine on
    SSLCertificateFile "/path/to/certificate.crt"
    SSLCertificateKeyFile "/path/to/private.key"
    SSLCertificateChainFile "/path/to/chain.crt"
</VirtualHost>
```

---

## Default Credentials (from database_schema.sql)

After running `database_schema.sql`, the following test accounts are available:

| Role | Email | Password | Name |
|------|-------|----------|------|
| Admin | admin@primecargo.mw | admin123 | Admin User |
| Agent | agent@primecargo.mw | agent123 | John Agent |
| Keeper | keeper@primecargo.mw | keeper123 | Jane Keeper |
| Client | client@primecargo.mw | client123 | ABC Trading Ltd |

**IMPORTANT**: Change all default passwords immediately after installation.

---

## System Maintenance

### Regular Backups

**Database Backup:**

```bash
# Daily backup script
mysqldump -u prime_user -p prime_cargo_db > backup_$(date +%Y%m%d).sql

# Automated with cron (Linux)
0 2 * * * /path/to/backup_script.sh
```

**File Backup:**

```bash
# Backup uploads directory
tar -czf uploads_backup_$(date +%Y%m%d).tar.gz uploads/
```

### Log Rotation

```bash
# Rotate activity logs (keep 90 days)
DELETE FROM activity_log WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY);
```

### Performance Optimization

```sql
-- Add indexes for common queries
CREATE INDEX idx_shipments_status ON shipments(status);
CREATE INDEX idx_shipments_tracking ON shipments(tracking_number);
CREATE INDEX idx_shipments_client ON shipments(client_id);
CREATE INDEX idx_messages_recipient ON messages(recipient_id, is_read);
CREATE INDEX idx_activity_log_user ON activity_log(user_id, created_at);
```

---

## Support & Documentation

- **System Administrator**: admin@primecargo.mw
- **Company**: Prime Cargo Limited, Blantyre Chileka Airport, Malawi
- **Malawi Revenue Authority**: https://www.mra.mw

---

## Changelog

### Version 1.0 (Initial Release)

- Complete shipment management system
- Role-based access control (4 roles)
- Document upload and verification
- Tax calculation with real-time exchange rates
- Payment tracking
- Messaging system
- Activity logging
- Admin dashboard and reports

---

## Future Enhancements

- [ ] Email notifications (PHPMailer integration)
- [ ] SMS notifications for shipment updates
- [ ] Two-factor authentication for admin accounts
- [ ] Advanced reporting and analytics dashboard
- [ ] Mobile application (iOS/Android)
- [ ] API for third-party integrations
- [ ] Automated tariff code lookup
- [ ] Real-time shipment tracking with GPS
- [ ] Digital signature support for release orders
- [ ] Multi-language support (English, Chichewa)
- [ ] Export declarations to MRA ASYCUDA system
- [ ] Automated email reminders for pending tasks
- [ ] Document OCR for automated data extraction
- [ ] Integration with payment gateways (Airtel Money, TNM Mpamba)

---

**Document Version**: 1.0
**Last Updated**: 2025-11-28
**Prepared By**: System Documentation Team
**Classification**: Internal Use

---

© 2025 Prime Cargo Limited. All Rights Reserved.
