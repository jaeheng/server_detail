# Server Detail Plugin

## Introduction

The Server Detail plugin provides an overview of key server information, allowing administrators to quickly check the
server status, including OS, CPU, memory, disk space, PHP version, MySQL version, and more.

## Features

- **General Server Information**:
    - Server software (e.g., Nginx version)
    - Operating system and architecture details
    - CPU information
    - Memory size
    - Disk usage

- **PHP Information**:
    - PHP version
    - Maximum execution time, upload limits, POST data size, etc.
    - Enabled extensions (e.g., curl, zip)
    - Session name and path

- **Database Information**:
    - MySQL version
    - Database size occupied by the site

- **Emlog-related Information**:
    - Current user role
    - Emlog version
    - Theme and plugin paths
    - Site template details

- **Other Server Configurations**:
    - Access URL
    - PHP configuration file path
    - Cookie and session storage paths

## Installation

You can install this plugin directly from the application store. or:

1. Download and extract this plugin into the Emlog plugin directory, e.g., `/content/plugins/`.
2. In the Emlog admin panel, go to **Plugin Management**, find this plugin, and activate it.
3. In the **Admin Panel**, navigate to **Server Details** to view the server status information.

## Compatibility

- PHP 7.4 or later
- Emlog Pro 2.5.7 or later
- Compatible with Linux, macOS, and Windows server environments

## Common Issues

### 1. The plugin cannot retrieve some information

Ensure that PHP has the necessary permissions to access server information, and verify if `phpinfo()` functions
correctly.

### 2. 'File not found' error

If certain files are missing or incorrect paths are used, check if the `plugins` directory is correctly set and all
plugin files are intact.

## Contributions & Feedback

If you encounter bugs or have suggestions for improvement, feel free to submit an issue or PR to the GitHub repository (
if available).

## License

This plugin is licensed under the MIT License, allowing free use and modification.

