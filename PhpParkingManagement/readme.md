# Php Parking Management Documentation

Welcome to the documentation of **Php Parking Management**. This project aims to provide a robust solution for managing vehicle fleets.

## Table of Contents
1. [Introduction](#introduction)
2. [Prerequisites](#Prerequisites)
2. [Installation](#installation)
3. [Usage](#usage)

---

## Introduction

This project is designed to help companies manage their vehicle fleets efficiently. It allows users to:

- Create fleet
- Register vehicle
- Localize vehicle

## Prerequisites

1. Install php:
    ```bash
    sudo apt update
    sudo apt install php libapache2-mod-php php-mysql
    ```
2. Install composer
    ```bash
    sudo apt install curl php-cli php-mbstring git unzip
    curl -sS https://getcomposer.org/installer | php
    sudo mv composer.phar /usr/local/bin/composer
    ```
3. Install mariadb (optional)
    ```bash
    sudo apt install mariadb-server
    sudo systemctl start mariadb
    sudo mysql_secure_installation
    ```

## Installation

To install **Php Parking Management**, follow these steps:

1. Clone the repository:
    ```bash
    git clone https://github.com/David-Bouv/fulll.git
    ```

2. Install dependencies using Composer:

    ```bash
    cd PhpParkingManagement
    composer install
    ```

3. Set up your config.php file and configure the database connection or set useDatabase at false.

4. Import bdd/init.sql for init database.
    

## Usage

Here are the available commands to manage vehicles and fleets via the command-line interface.

### 1. Create a Fleet

To create a fleet for a user, use the following command:

    ```bash
    php bin/console fleet:create <userId>
    ```

This will return the ID of the created fleet, which you can use to register vehicles in that fleet.

Example:

    ```bash
    php console.php bin/console fleet:create user-id-123
    ```

### 2. Register a Vehicle

To register a vehicle in a fleet, use the following command:

    ```bash
    php bin/console fleet:register-vehicle <fleetId> <vehiclePlateNumber>
    ```

fleetId: The ID of the fleet where you want to register the vehicle.
vehiclePlateNumber: The license plate number of the vehicle to register.

Example:

    ```bash
    php bin/console fleet:register-vehicle 98765 ABC123
    ```

This will register the vehicle with the license plate ABC123 in the fleet with ID 98765.

### 3. Localize a Vehicle

To localize a vehicle in a fleet to a specific location, use the following command:

    ```bash
    php bin/console fleet:localize-vehicle <fleetId> <vehiclePlateNumber> <lat> <lng> [alt]
    ```

fleetId: The ID of the fleet containing the vehicle.
vehiclePlateNumber: The license plate number of the vehicle.
lat: The latitude of the location where you want to localize the vehicle.
lng: The longitude of the location.
alt (optional): The altitude of the vehicle's location.

Example:

    ```bash
    php bin/console fleet:localize-vehicle98765 ABC123 48.8566 2.3522
    ```

This will localize the vehicle with license plate ABC123 in the fleet with ID 98765 at the specified geographical location (latitude 48.8566, longitude 2.3522).

If you want to include an altitude, you can add an additional parameter:

    ```bash
    php bin/console fleet:localize-vehicle 98765 ABC123 48.8566 2.3522 35
    ```

This will localize the vehicle at the specified position with an altitude of 35 meters.
