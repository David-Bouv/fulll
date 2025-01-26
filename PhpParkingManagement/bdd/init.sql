CREATE DATABASE ParkingManagementDB;

USE ParkingManagementDB;

CREATE TABLE Users (
    userId VARCHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

CREATE TABLE Fleets (
    fleetId VARCHAR(36) PRIMARY KEY,
    userId VARCHAR(36) NOT NULL,
    name VARCHAR(255) NOT NULL,
    FOREIGN KEY (userId) REFERENCES Users(userId) ON DELETE CASCADE
);

CREATE TABLE Locations (
    locationId INT AUTO_INCREMENT PRIMARY KEY,
    latitude DECIMAL(9,6) NOT NULL,
    longitude DECIMAL(9,6) NOT NULL,
    altitude DECIMAL(8,2) NULL
);

CREATE TABLE Vehicles (
    vehicleId VARCHAR(36) PRIMARY KEY,
    licensePlate VARCHAR(20) NOT NULL UNIQUE,
    type VARCHAR(50) NULL,
    locationId INT NULL,
    FOREIGN KEY (locationId) REFERENCES Locations(locationId) ON DELETE SET NULL
);

CREATE TABLE FleetVehicles (
    fleetId VARCHAR(36) NOT NULL,
    vehicleId VARCHAR(36) NOT NULL,
    PRIMARY KEY (fleetId, vehicleId),
    FOREIGN KEY (fleetId) REFERENCES Fleets(fleetId) ON DELETE CASCADE,
    FOREIGN KEY (vehicleId) REFERENCES Vehicles(vehicleId) ON DELETE CASCADE
);