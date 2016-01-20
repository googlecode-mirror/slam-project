# Tutorials #

Outlines for now, will flesh out later.

## Creating a new category ##

  1. Log into phpmyadmin
  1. Click on the database name (right side)
  1. Click on the Template link (right side)
  1. Click on "Operations" (top)
  1. Lower right: "Copy table to"
  1. Enter name, select "Structure only"
  1. Click on "Go"

## Adding a new category to SLAM ##

  1. Log into phpmyadmin
  1. Click on the database name (right side)
  1. Click on the SLAM\_Categories link (right side)
  1. Click on Insert (top)
  1. Fields:
Name
Prefix
List Fields
Field Order
Title Field
Owner Field
Date Field

  1. Click on "Go"

## Customizing a Category ##
  1. Log into phpmyadmin
  1. Click on the database name (right side)
  1. Click on desired category name (right side)
  1. Click on "Structure"
  1. To remove, click on "Drop" - WARNING!
  1. To add, scroll down to "Add 1 columns" After (desired field) - "Go"
  1. Fields:
"Column" (attribute)
"Type" (INT, VARCHAR, TEXT, Date, Enum,Set)
"Length/Values" Values: 'value1','value2','value3'
"Default (As defined:)"
"Comments #link"

## Adding a new Project ##
  1. Log into phpmyadmin
  1. Click on the database name (right side)
  1. Click on the SLAM\_Projects link (right side)
  1. Click on Insert (top)
  1. Name
  1. Click on "Go"

## Adding a new User ##
  1. Log into phpmyadmin
  1. Click on the database name (right side)
  1. Click on the SLAM\_Researchers link (right side)
  1. Click on Insert (top)
  1. Fields:
username
crypt (blank)
salt (blank)
email
group
  1. Click on "Go"