-- Create the bookingcalendar database if it does not exist
CREATE DATABASE IF NOT EXISTS bookingcalendar;

-- Use the bookingcalendar database
USE bookingcalendar;

-- Create the rooms table
CREATE TABLE rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

-- Create the users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    is_admin TINYINT,
    password VARCHAR(255) NOT NULL,
    username VARCHAR(255) UNIQUE NOT NULL
);

-- Create the bookings table
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date DATE,
    desk VARCHAR(255),
    room_id INT,
    user_id INT,
    FOREIGN KEY (room_id) REFERENCES rooms(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);