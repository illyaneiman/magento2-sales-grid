# Ineiman_SalesGrid Module for Magento2 EE

[![Ineiman Sales Grid](https://img.shields.io/badge/version-2.4.6.0-green.svg)](https://github.com/illyaneiman/magento2-sales-grid.git)
[![Package](https://img.shields.io/badge/package-2.4.6.0-blue.svg)](https://github.com/illyaneiman/magento2-sales-grid.git)

This module add new table (ineiman_salesgrid_flat) with indexed data and grid in admin to simplify order grid view

## Overview
If you have to many attributes for show in Sales Order Grid this module can help you.

After installation there will be new Order Grid called "Sales Grid" in Sales menu.

This Grid simplify order view and add additional columns such as product details per each ordered product in order, comment history etc.

## Features
- New simplified Order Grid in Sales menu.
- New indexer "ineiman_salesgrid" to index and transfer data to new Order Grid table

# User Manual
## Usage in Admin

- Go to Admin Panel -> Sales -> Sales Grid

## Console usage
If you noticed some data not rendered in grid make sure this data exist in original grid and run reindex:

```bin/magento index:reindex ineiman_salesgrid```
