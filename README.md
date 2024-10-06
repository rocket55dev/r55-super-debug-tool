# R55 Super Debug Tool

[![License](https://img.shields.io/badge/license-GPL2-blue.svg)](https://www.gnu.org/licenses/old-licenses/gpl-2.0.html)

## Description

The **R55 Super Debug Tool** is a WordPress plugin designed to display extensive debug information for the current post. Originally created as an internal tool to aid in debugging, it is now offered to the wider community as-is and free of support.

**Note**: This plugin is intended for use in development environments. It provides detailed information that should not be exposed on a production site.

## Features

- **Comprehensive Debug Information**: Displays detailed information about the current post, including meta keys, taxonomies, terms, and more.
- **Bootstrap Modal Interface**: Presents the debug information in a user-friendly Bootstrap modal.
- **Syntax Highlighting**: Utilizes Highlight.js for syntax-highlighted JSON outputs.
- **Admin Bar Integration**: Adds a convenient "Debug Tool" button to the WordPress admin bar for quick access.
- **ACF Support**: Displays Advanced Custom Fields (ACF) data if the ACF plugin is active.
- **Customizable Settings**: Includes a settings page to enable or disable the loading of Bootstrap assets.

## Table of Contents

- [Installation](#installation)
- [Usage](#usage)
- [Settings](#settings)
- [Screenshots](#screenshots)
- [Contributing](#contributing)
- [License](#license)
- [Disclaimer](#disclaimer)
- [Acknowledgments](#acknowledgments)
- [Contact](#contact)

## Installation

### Prerequisites

- **WordPress** version 5.0 or higher.
- **PHP** version 7.0 or higher.
- **WP_DEBUG** set to `true` in your `wp-config.php` file.

### Steps

1. **Download the Plugin**

   - Clone or download the plugin files from the [GitHub repository](https://github.com/rocket55dev/r55-super-debug-tool/).

2. **Upload to WordPress**

   - Upload the plugin folder `r55-super-debug-tool` to the `/wp-content/plugins/` directory of your WordPress installation.

3. **Activate the Plugin**

   - Go to the **Plugins** menu in the WordPress admin dashboard.
   - Find **R55 Super Debug Tool** in the list and click **Activate**.

4. **Verify WP_DEBUG**

   - Ensure that `WP_DEBUG` is set to `true` in your `wp-config.php` file:

     ```php
     define( 'WP_DEBUG', true );
     ```

## Usage

1. **Accessing the Debug Tool**

   - Log in to your WordPress admin dashboard as an administrator.
   - Navigate to any single post page on the front end of your site.
   - Click the **Debug Tool** button in the WordPress admin bar at the top of the page.

     ![Admin Bar Debug Tool Button](assets/admin-bar-button.png) *(Include an actual screenshot in your repository.)*

2. **Viewing Debug Information**

   - The debug tool will append `?debugtool=1` to the URL and automatically display a modal window with debug information.
   - Use the navigation pills at the top of the modal to switch between different sections.

     ![Debug Modal](assets/debug-modal.png) *(Include an actual screenshot in your repository.)*

3. **Navigating Sections**

   - **Meta Keys**: Displays all post meta data.
   - **Taxonomies & Terms**: Shows taxonomies and associated terms for the post.
   - **Post Object**: Provides the complete post object data.
   - **Attached Media**: Lists media attached to the post.
   - **Parent & Child Posts**: Displays information about parent and child posts.
   - **Post Type & Template**: Shows the post type and page template used.
   - **Author Info**: Provides information about the post author.
   - **Query Info**: Displays query variables and whether it's the main query.
   - **Comments**: Lists comments associated with the post.
   - **Rewrite Rules**: Shows the site's rewrite rules.
   - **Enqueued Scripts & Styles**: Lists scripts and styles enqueued on the page.
   - **ACF Fields**: Displays Advanced Custom Fields data (if ACF is active).
   - **Custom Post Types**: Lists custom post types registered on the site.
   - **Custom Taxonomies**: Lists custom taxonomies registered on the site.

## Settings

1. **Accessing the Settings Page**

   - In the WordPress admin dashboard, navigate to **Settings > R55 Super Debug Tool**.

     ![Settings Menu](assets/settings-menu.png) *(Include an actual screenshot in your repository.)*

2. **Configuring Plugin Options**

   - **Load Bootstrap Assets**: Enable or disable the loading of Bootstrap CSS and JS assets.
     - **Enable**: The plugin will load Bootstrap assets when the debug tool is activated.
     - **Disable**: Use this if your theme or another plugin already includes Bootstrap to prevent conflicts.

     ![Settings Page](assets/settings-page.png) *(Include an actual screenshot in your repository.)*

3. **Save Changes**

   - After adjusting the settings, click **Save Changes** to apply them.

## Screenshots

*(Include the following screenshots in the `assets/` directory of your repository and reference them here.)*

1. **Admin Bar Debug Tool Button**

   ![Admin Bar Debug Tool Button](assets/admin-bar-button.png)

2. **Debug Modal with Navigation Pills**

   ![Debug Modal](assets/debug-modal.png)

3. **Settings Page**

   ![Settings Page](assets/settings-page.png)

## Contributing

Contributions are welcome! If you encounter any issues or have suggestions for improvements, please submit an issue or pull request on [GitHub](https://github.com/rocket55dev/r55-super-debug-tool/).

### Steps to Contribute

1. **Fork the Repository**

   - Click the **Fork** button at the top right of the repository page.

2. **Clone Your Fork**

   ```bash
   git clone https://github.com/YourUsername/r55-super-debug-tool.git
