# Installing SLAM #

## Requirements: ##
SLAM is designed to work with a standard "LAMP" internet server setup (Linux, Apache, MySQL, PHP). Many academic departments that provide web hosting for your lab website likely already run such a setup. Alternatively, you can set one up for yourself, as most standard Macintosh or Linux distributions (Ubuntu, Fedora, etc.) either come with these components preinstalled, or make it very easy to install them yourself:

  * http://www.mamp.info/en/mamp/index.html
  * http://fedorasolved.org/server-solutions/lamp-stack

Regardless of which route you choose, you will need the following before attempting to install SLAM:

  1. A modern browser: Google Chrome, Safari, Firefox 3+, or Internet Explorer 6+ have all been verified, but others may work as well.
  1. A folder/directory on the web server on which all of the files attached to SLAM assets can be stored. SLAM compresses attached files on-the-fly to save space, but if you attach many large files to your SLAM assets, this directory could eventually become quite large.
  1. Access to MySQL server. This will consist of three or four components: The server name, and a login name and password for access. If system administrator has already created a database for you, he/she will provide you the name of the database as well.


---


## Installing SLAM is an easy four-step process. ##
Once you have the required information, download SLAM (e.g. SLAM\_1.1.0.tgz), uncompress it, and place it in your web server directory where it can be accessed. You should now start the SLAM installer by opening the installer URL in your browser. (e.g. "`http://yourserver.com/slam/install`")

If SLAM detects that everything is OK, you will be presented with the license agreement. Please read it careful and click the "Start Installation" to proceed to the first configuration step.

### Step 1 ###
Here you will fill in some basic information SLAM needs in order to store asset information as well as the files your users will attach to those assets. Some options will be automatically filled out, and may not need to be changed. Clicking the "Check these values" buttons for each section will cause SLAM to test their suitability.

_**General Settings**_

  * **Installation path** - This option is the absolute file system path to your SLAM installation, and probably shouldn't be changed except in special circumstances.
  * **Lab name** - The name of your lab should go here, and will appear at the top of every SLAM page after the installation is complete.
  * **Lab prefix** - Choose two letters (A-Z) that will uniquely identify your lab. This will be incorporated into the  identifier SLAM will create for every asset.
  * **Mail header** - If a user forgets their password, SLAM will send them a reset email. If you'd like to change who these emails appear to originate from, you can change it here. The default value is probably appropriate.

_**Database Settings**_

  * **Server** - The name or IP address of the MySQL server. Many web servers host their own MySQL server, in which case this should be "localhost".
  * **Database name** - If you already have a database created that you would like SLAM to use, or if one has been provided for you by your IT staff / sysadmin, type it here. Otherwise, SLAM will attempt to create one for you on the server.
  * **Login name** - The username SLAM should use to access the MySQL server.
  * **Login password** - The password SLAM should use to access the MySQL server.

_**Attached File Settings**_

SLAM will need to have access to at least one directory in which it can save attached files. These can be pre-existing folders, otherwise SLAM will attempt to create them.

  * **Attachment directory** - The directory SLAM will save all attached asset files
  * **Temporary directory** - The directory SLAM will utilize to temporarily store uploaded and downloaded files.

Once you have filled these values out, click the "Check these values" button for each section to verify that they are indeed correct and that everything works properly. If SLAM reports that "these settings are OK" for each section, then **congratulations**! you've just finished the hardest part of the installation.

You can proceed to the next step by clicking "Save these settings and Continue".

_NOTE: Clicking "Cancel these settings and Go Back" will cause you to loose any changes you've currently made. SLAM will always retain any previously saved values, however._

### Step 2 ###
Here you can set up some default asset categories for you to start SLAM out with. After you finish installing, you may of course delete, modify, or copy any categories.

Click the checkbox beside each category type you'd like SLAM to set up for you. You can also change the identifier prefix if you like. A brief description of each category is provided.

Click "Save these setting and Continue" to go onto the second step.

### Step 3 ###
Here you can define as many projects as you'd like SLAM to initially start with (you can always add or delete projects later). You can also let your users create their own projects, if you wish, by checking the appropriate checkbox.

Additional spaces for projects can be added by pressing the "add project" button. Project names should be short and immediately recognizable.

Once you've added all the desired projects, click the "Save these settings and Continue" button to go to the last step.

### Step 4 ###
Here you can set up the users ("Researchers") that will use SLAM. In addition to regular users, you will also be required to create an administrator account that can access, modify, or remove anybody's assets. (By default, SLAM only lets an asset's owner and the members of its owners group modify or remove it). Superusers can also see assets that have been removed, allowing them to "undelete" accidentally removed assets.

Input a superuser name, password, and email address.

An unlimited number of users can now be set up (although more can be added later, if necessary). You can also connect users and projects now, if you specified any projects in the previous step. A user can belong to multiple projects, just control or command click to select them.

Click the "Save the settings and Continue" button to go to the confirmation page.

### Confirmation ###
SLAM will attempt to verify all your settings one last time, and will display any errors it has encountered. If none have been found, you can still go back and change any options you wish by clicking on the appropriate button.

if you are satisfied with your options, complete the SLAM installation by clicking the "Complete Installation" button.

The installer will now create the necessary configuration and preference files, as well as categories, users, and projects on the database.

Installation is now complete, and you can immediately start using SLAM!