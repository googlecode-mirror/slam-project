# Introduction To SLAM Theory and Usage #


## What SLAM is ##
> SLAM (SQL-based Laboratory Asset Management) is a web-based application for digitally storing information on various research laboratory elements. By storing this data in a single, searchable location, lab members can keep track not only their own samples, but ensure continuity of documentation for those in the future.

## Who SLAM was designed for ##
> SLAM was designed to assist small to medium-sized research groups keep track of samples ("assets") like glycerol stocks of cell strains, plasmid minipreps, primers, NMR samples, etc. All too often, members of such groups will run across sample tubes in their freezers or cold rooms that have unclear or nonspecific markings with little documentation on what they are or even what project they belong to. This problem is especially troublesome when a student graduates, often resulting in significant duplication of effort as students attempt to pick up where their predecessor left off.

> Evidence has shown that if entering data for an asset is difficult or time-consuming, the researchers in a group will likely avoid it, rendering the solution useless. SLAM, however, makes entering information on assets almost effortless. By assigning a unique and instantly recognizable "identifier" to every asset, important attributes and details about that sample can be immediately recovered, even if the original researcher's notebooks are missing or illegible.

> SLAM is easily customizable in recognition of the unique needs of each lab. It utilizes the industry-standard MySQL database system, used by thousands of applications and companies such as Facebook, Google, and Wikipedia. As SLAM relies upon the database for not only information storage but structure, it can be seamlessly complemented with existing database design and management tools. Finally, because SLAM is web based, it can be accessed from any computer with a fairly modern browser. It does not require any plugins, java applets, or specialized software.

## What SLAM does not do ##
> SLAM was not designed to be a complete replacement for lab books or good organizational practices. Many laboratory information management systems ("LIMS") seek to replace the lab notebooks by offering a complicated scheme of data storage and contextualization. Unfortunately, many researchers run afoul of these inflexible project templates, leading to underutilization and, ironically, poor record keeping.

> SLAM does one thing, and strives to do it well: store information about the physical components of research, such as where it is stored, who made it, when it was last used, etc. To do this effectively, SLAM relies on a very simple set of organizational rules.

## The "Identifier" ##
> When an asset is entered into SLAM, it is assigned a unique identifier. This identifier is of the form "**AABB\_n**", where **AA** is a two-letter sequence indicating which laboratory the asset is from, and **BB** are two letters that indicate the type (category) of asset. This letter portion is immediately followed by an underscore and a number. As this identifier is unique, it can be used as a label on microcentrifuge tubes, 96-well plates, freezer racks, etc., allowing for immediate lookup of asset information. Categories can also be made for essentially nonphysical entities like protocols, NMR assignments, and protein mutants.

> For example, **MFPL\_202** indicates "**MF**", i.e. the "Mark Foster" lab, "**PL**" for plasmid prep, and the unique number **202**. The lab and category type letter codes may be specified in the SLAM configuration file. There is essentially no limit on the number of identifiers, as MySQL can support thousands of categories and millions of records per category.

## Interface ##
> The SLAM interface consists of thee regions: the top navigation bar, the category list on the left, and the central asset list or editing panel.

> When a user logs into SLAM, they are initially presented with their "Dashboard", which lists any assets they have tagged. This allows researchers to easily follow only the assets they are currently using. The dashboard also allows users to search multiple categories for assets meeting the specified search criterion. This powerful behavior is covered in the "Searching" portion of this document.

> At any time, all assets from a category can be viewed by clicking the category name on the left category list. By default, assets in any list are ordered descendingly by their identifier.

> There are multiple ways to view the attributes of an individual asset. When browsing a list, the user may either click the "view" or "open" link to the left of the asset's identifier, or by checking the assets checkbox and pressing the "Edit" button at the top or bottom of the list. Doing so will open the editing or viewing panel, which consists of a list of field types on the left, and attribute values on the right.

## Adding assets ##
> There are several ways to add an asset entry, but the simplest is to simply click on a category name from the list on the left, and then on the "New" button in the list of action buttons at the top or bottom of the list. This will take the user to the asset editing page. Here they can fill in the various attributes of the new sample, which has already been given a unique identifier.

> All assets possess the following fields, in addition to other asset-specific attributes: the "Identifier", "Project", "Label", "Researcher", "Entered By", "Date", "Notes", and "Files". A quick description of any field type may be available by passing the mouse cursor over the field name. The "Researcher", "Entered By", and "Date' attributes are automatically filled in every time a new asset is created.

> Because external files such as sequence maps, chromatograms, gel pictures, etc. are often important parts of information to keep with an asset, SLAM allows researchers to attach an essentially unlimited number of files via the "Files" attribute. Clicking the "Open Browser" button at the bottom of the asset editor will open the file browser and attachment popup, allowing users to upload/download, or even view some files from within the browser.

> Because it can be tedious to re-enter attributes while adding many similar assets, an existing asset may be "cloned" by checking its checkbox and pressing the "Clone" button. This will cause all of the selected attributes to be copied to a new asset (except for the identifier, which will be unique), and the new asset opened for editing. Note that any files attached to the previous asset will not be copied to the new asset.

> Although any asset may be viewed by any researcher who logs in to SLAM, only the user who entered an asset may modify it, in addition to any lab "superusers". It is advisable for each lab to have at least one "superuser" who can transfer ownership of assets or make changes to assets originally entered by individuals who have subsequently left the lab.

> If the user attempts to navigate away from an open asset in which they have not saved their changes, SLAM will ask if they wish to discard any modifications. Changes may be saved by either clicking "Save", which will return the user to where they were before editing the asset, or by clicking "Save Changes" which will commit the changes to the database, but keep the user at the current asset.

## Searching ##
> One of the most powerful features of SLAM is its ability to search for assets both within a given category and across categories with a number of different search modes.

> To search within a single category, click on the category name from the list on the left. At the top, above the list of assets, is the search box. Each search "term" consists of four components. From left to right:
    1. The attribute field menu
    1. The search logic menu
    1. The search value field
    1. The search join menu.

> The attribute field specifies which attribute of the asset you would like to search in. If you were looking for all assets entered by a specific researcher, for example, you should choose the "Entered By" option from the menu.

> The search logic menu specifies how you would like to match the provided search text to the specified field. The default tilde ("~") option represents "like", meaning that searching for the phrase "foo" will return matches like "foobar", "bar foo" and even "FooBar", as matching is case-insensitive. Conversely, the "!~" option means "not like", and means that any results matching the search phrase will be excluded. The additional search modes, ">", "=", and "<" are best used for numerical (e.g. dates) comparisons, as their behavior with text values may be somewhat unpredictable. An important exception is that the equals sign can be used to signify that SLAM should only look for an exact match to the provided search text.

> The search value field is where you should input the search text you are looking for. Wildcard characters are currently not supported, but can be emulated by using multiple search terms.

> Finally, the search join menu plays an important role when combining multiple search terms. To add an additional search term, click on the "+" text to the right of the search join menu. This will create an additional search term with the same layout as previously described. By default, terms are joined by "AND" terms, which means that any returned assets much match all of the provided terms.

> For example, if you would like to find all of the entries in the current category that were entered by "D.Smith" before 2010/08/17, except those in the "TRAP" project, you would use the following search terms:
| Entered By | ~ | D.Smith | AND |
|:-----------|:--|:--------|:----|
| Date       | < | 2010-08-17 | AND |
| Project    | !~ | trap    |     |

> The number of returned results can also be specified via the "limit" menu. By default, only 10 results are returned when searching, while by default 100 assets are displayed when navigating categories. Note that results can be sorted by clicking on the asset list attribute name, clicking a second time will reverse the sorting order.

> To search across multiple categories, return to the dashboard by clicking the "Dashboard" link at the top of the category list, and then the "Multi-category search" button on the right. This will open a drop-down menu of all available categories. To select categories, hold down the command (Macintosh) or control (Windows) key, and click on the desired categories. All of the available categories may be selected at once by command-A (Macintosh) or control-A (Windows). Once the desired categories are selected, press the "Select" button at the bottom of the list. This will then open a search box similar to that used for searching in a single category, and the search syntax is identical.

## Deleting assets ##
> Assets can be "deleted" by checking the asset's checkbox and pressing the "Delete" button. This will not actually remove the asset permanently from the database, but result in it no longer appearing in searches or lists. A superuser can make the asset re-available for editing. Note: depending upon how SLAM is configured, attached files may or may not be permanently deleted when an asset is hidden.

## Tagging assets - The Dashboard ##
> Users may access their dashboard at any time by clicking either the "Dashboard" link at the top of the category list on the left, or by clicking their lab's name at the left of the top navigation bar. The user's dashboard will display any assets they have currently tagged.

> Assets may be "tagged" which will result in them being visible in the user's dashboard. This is convenient for assets that are frequently used, as the researcher will not have to perform a search for them each time their information is to be updated or referenced.

> When a tagged asset is cloned, the resulting asset will also be tagged. Likewise, if the "New" button to the right of the category name in the dashboard is clicked, the resulting new asset will also by default be tagged. Assets may be "untagged" by selecting their checkbox and pressing the "Untag" button at the bottom of the dashboard asset list.

## Exporting information ##
> At any time, the information on current list of assets may be exported as a CSV file (suitable for import into Excel, OpenOffice, etc.) by clicking on the "export" link to the left of the action buttons. This will download a file named "export.csv", and will contain a list of the attribute field names and values for each asset currently visible.

## Miscellaneous ##
> To log out of SLAM, or to change the current user's password, click the user's name in the upper right hand side of the top navigation bar. A popup-window listing the available user actions will appear.

> As SLAM is currently in closed development, all bug reports and feature requests are currently handled through Google Code's project hosting. The SLAM project is accessible through code.google.com/p/slam-project, or through the "report a bug" link at the bottom of any SLAM page.

## Additional Resources ##
  1. [MySQL](http://www.mysql.com)Free, enterprise-class database system. The MySQL Workbench admin tool simplifies many duties like modifying and backing up databases, adding/deleting connections, etc.
  1. [phpMyAdmin](http://www.phpmyadmin.net) Free, web-based database design and administrative tool for administrative functions like adding/deleting categories, adding/deleting asset attributes
  1. [Adminer](http://www.adminer.org) Similar to phpMyAdmin, also free and web-based, but with a simpler interface and installation.
