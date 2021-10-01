![logo](./images/logo.svg)

# Blankify

## Installation

### Normal WordPress zip deployment

1. Download the latest [zip file](https://github.com/listingslab-software/listingslab-download/raw/master/wordpress/themes/blankify.zip)
2. Navigate to the [Add Plugins](/wp-admin/plugin-install.php) page in wp-admin
3. Tap Upload Plugin and select the zip file you downloaded
4. Upload and activate 
5. Configure the plugin on first run

### Fork & Develop 

In this scenario you can use unix's `ln` command to create a symlink to a forked local version of @blankify

> ln [-fs] [-L|-P] source_file target_file

```bash
ln -s ~/Desktop/Node/listingslab/php/blankify ~/Desktop/PHP/WordPress/demosite/wp-content/themes
```
