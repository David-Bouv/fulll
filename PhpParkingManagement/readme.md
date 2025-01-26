# Php Parking Management Documentation

Welcome to the documentation of **Php Parking Management**. This project aims to provide a robust solution for managing vehicle fleets.

## Table of Contents
1. [Introduction](#introduction)
2. [Prerequisites](#Prerequisites)
2. [Installation](#installation)

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
