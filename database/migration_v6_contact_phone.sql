-- ==========================================================
-- migration_v6_contact_phone.sql
-- Adds a required contact phone number to bookings.
-- Safe to run once. Import this in phpMyAdmin (or via CLI)
-- against your existing "movie booking" database.
-- ==========================================================

ALTER TABLE `bookings`
  ADD COLUMN `contact_phone` VARCHAR(30) DEFAULT NULL AFTER `contact_email`;
