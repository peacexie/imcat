

#backup#Backup

* Back up is a good habit:
 - A habit to back up, It's usefull for your life;
 - Back up this system includes: website files and database
 - Before each upgrade, or a large system maintenance, please back up you system;
 - Daily maintenance, like as modify the configuration, please backup too;

* Backup database
 - [advice] To stop the mysql service before backup;
 - To copy the database directory (recommended);
 - Use commands (Expert available);
 - Use the third party tools, like as: Adminer(https://www.adminer.org/) or phpMyAdmin(http://www.phpmyadmin.net/);

* Backup program files
 - Mainly: /code and /root dirs。
 - Important file (folder): /code/cfgs/，/code/cfgs/boot/_paths.php. 


#preset#Preparation

* Before upgrade
 - First: copy files from the latest package to cover the existing directory;
 - The earlier version (V3.0), in fact, There no such these files, so you can copy the file in this way;

* Cover files
 - /code/core/ >> blib/clib/glib/sdev subdirs
 - /root/tools/setup >> (skip index.php setup entry)
 - /code/cfgs/boot/cfg_load.php The automatic loading configs, you can edit it manually $_cfgs['acdir']
 
* Backup configuration again
 - dirs: /code/cfgs/;
 - file: /code/cfgs/boot/_paths.php;
 - set: /code and /root writeable (to add files or modify files to be copied when upgrading).

* Execution entry:
 - Upgrade entry: /root/tools/setup/upvnow.php
 - Executive order: from top to bottom, from left to right, click the button, and step by step.


#update#Upgrade Tips

* Upgrade Files:
 - Dirs: /code, core/ The newly added files and most of the modified files will be copied directly;
 - For copy files, please set dirs: /code, core/ writeable;
 - A small number of config files, will prompt comparison, please carefully contrast, and update.
 - Manual deel dirs: /skin/ Template directory, please manually processing; Pay attention the dir _pub/jslib(generic JS library);
 - Manual deel dirs: /static, /vendor, /vendui Generally can directly copy the latest package to cover the files;
 - Manual deel dirs: /root/run/ If you manually modified the entry files, please deel it (such as delete or move away);
 
* Upgrade DB-Frame(Tables,Fields):
 - The newly added tables and fields will be updated directly;
 - The modified fields, you will be prompted to contrast, please carefully contrast, and update.
 - Add and modify the index, will be prompted to contrast, please carefully contrast, and update.
 
* Upgrade DB-Data:
 - New tables and fields are added and the data(recs) is upgraded;
 - The newly added configs: table named begin with `bext`, `base`, will be upgraded together;
 - Modified config-Data: will prompt contrast, please carefully contrast, and update.


#import#Import Old-Data

* Important tips:
 - Specific import operation, according to the comparison after the SQL statement, manual (choose the required data) copy and run them;
 - Before importing, please settings (add or modify fields) or manually modify the database structure;
 - After modifying the structure of the database, you can be re initialized the cache, to check the structure.

* Set database:
 - File: {code}/cfgs/excfg/ex_outdb.php (part of $_cfgs)
 - Tips: file: {code}/cfgs/boot/cfg_db.php can copy from old system 

* Initialization cache:
 - Run first please.

* Increase or decrease of the table:
 - add : The added tables in the new system
 - old : The tables of the old system

* Modified fields:
 - edit : Modified fields
 - add : New system added fields
 - old : Old system added fields
 - skip : Modified fields (you can ignored)

* Increase or decrease of the index:
 - add : New system added index
 - old : Old system added index


#endset#After Upgrade

* General upgrade options
 - 如果升级的版本只相差一个，如从v3.1升级到v3.2，一般可选择自带[升级程序]
 - 如果升级的版本相差较大，如从v3.0升级到v3.5，建议选择[导入旧版数据]
 - Other tips: a specific version, please see the relevant notes at the release page

* Backup update record
 - Dirs: /vary/dtmp/update/
 - You can copy all the files here, back up, for the necessary follow-up analysis
 
* Clean up and Finish
 - Delete dir: /root/tools/setup/, or set these PHP file can not be executed
 - [Suggest]Set dir: /code and /root Can NOT write.

