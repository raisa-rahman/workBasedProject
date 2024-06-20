# workBasedProject

# Desk Reservation Web Application

This repository contains the implementation of a desk reservation web application developed as part of the Work-Based Project (IN2034) by Raisa Rahman.

## Table of Contents

1. [Introduction](#introduction)
2. [Implementation Description](#implementation-description)
   - [Programming Language and Tools](#programming-language-and-tools)
   - [Directory Structure](#directory-structure)
   - [Compilation and Runtime Components](#compilation-and-runtime-components)
   - [Deployment](#deployment)
3. [Testing](#testing)
   - [Test Plan](#test-plan)
   - [Testing Results](#testing-results)
4. [Instructions for Installation, Setup, and End-Users](#instructions-for-installation-setup-and-end-users)
   - [Installation Instructions](#installation-instructions)
   - [Setup Instructions](#setup-instructions)
   - [End-User Instructions](#end-user-instructions)
5. [Traceability to Design Document](#traceability-to-design-document)
6. [Conclusion](#conclusion)

---

## Introduction

This repository hosts the desk reservation web application designed and implemented for the Work-Based Project (IN2034). The application allows users to reserve desks, view reservations, and manage their bookings efficiently.

## Implementation Description

### Programming Language and Tools

- **Programming Languages**: PHP, HTML, CSS, SQL
- **Database Management System**: MySQL
- **Web Server**: Apache
- **Development Tools**: Visual Studio Code, phpMyAdmin

### Directory Structure

The project's directory structure is organized as follows:

```
/desk-reservation-app
├── /admin.php
├── /book.php
├── /calendar.php
├── /cancel_booking.php
├── /create_booking.php
├── /database_setup.sql
├── /delete_booking.php
├── /download_report.php
├── /floorplan.jpeg
├── /functions.php
├── /in_office.php
├── /login.php
├── /main.php
├── /manage_bookings.php
├── /README.md
└── /weekly.php
```

### Compilation and Runtime Components

- **Compilation**: PHP is an interpreted language and does not require compilation.
- **Run-time Components**:
  - Web Server: Apache
  - PHP Interpreter: Version 7.4 or higher
  - MySQL Database: Version 5.7 or higher
  - Browser: Modern browsers such as Chrome, Firefox, or Edge

### Deployment

For deployment on a stand-alone PC:
- Install XAMPP (which includes Apache, PHP, and MySQL).
- Place the project files in the `htdocs` directory of XAMPP.
- Import the `database_setup.sql` file to set up the database.
- Configure the application's database connection settings in `config.php`.
- Access the application via `http://localhost/WorkBasedProject/workBasedProject/login.php` in a web browser.

## Testing

### Test Plan

- **Functional Testing**: Ensures each function operates as per requirements.
  - User Login, Desk Reservation, View Reservations, Cancel Reservations.
- **Unit Testing**: Tests individual components (e.g., User Registration).
- **System Testing**: Simulates real-world scenarios (Operational Profile Example).

### Testing Results

Detailed testing results, including identified issues and their resolutions, are documented.

## Instructions for Installation, Setup, and End-Users

### Installation Instructions

1. **Install Required Software**:
   - Download and install XAMPP.
   - Start Apache and MySQL services from the XAMPP control panel.

2. **Set Up the Database**:
   - Create a new database named `bookings` using phpMyAdmin.
   - Import the `database_setup.sql` file to create tables and initial data.

3. **Configure the Application**:
   - Update database connection settings in `config.php`.

### Setup Instructions

- Place the project files in `htdocs` directory of your XAMPP installation.
- Ensure Apache and MySQL services are running.
- Access the application via `http://localhost/desk-reservation-app` in a web browser.

### End-User Instructions

- **User Login**:
  - Navigate to the login page.
  - Enter your username and password.
  - Click "Login" to access your account.
- **Making a Reservation**:
  - Log in.
  - Navigate to the ‘Make a Booking’ page.
  - Select a date and desk.
  - Click "Book" to reserve the desk.
- **Viewing and Cancelling Reservations**:
  - Log in.
  - Navigate to "Manage my Bookings".
  - View your reservations.
  - Click "Cancel" next to a reservation to cancel it.

## Traceability to Design Document

The implementation aligns with the design document, including class associations and defined functionalities.

## Conclusion

This repository provides a detailed overview of the desk reservation web application, covering its development, deployment, testing processes, and user instructions.

---

For more details, refer to the commented source code included in the repository.