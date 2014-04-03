README
======

This directory should be used to place project specfic documentation including
but not limited to project notes, generated API/phpdoc documentation, or
manual files generated or hand written.  Ideally, this directory would remain
in your development environment only and should not be deployed with your
application to it's final production location.


Setting Up Your VHOST
=====================

The following is a sample VHOST you might want to consider for your project.

<VirtualHost *:80>
   DocumentRoot "F:/php_ws/outsourcing2/public"
   ServerName .local

   # This should be omitted in the production environment
   SetEnv APPLICATION_ENV development

   <Directory "F:/php_ws/outsourcing2/public">
       Options Indexes MultiViews FollowSymLinks
       AllowOverride All
       Order allow,deny
       Allow from all
   </Directory>

</VirtualHost>



//测试用户vps00298059: 当outsourcing2及其子文件（包括目录）的所属，全部改为apache:apache后，
//就可能以vps00298059用户执行了.
//测试crontab2: 是否OK？No
No?
It's OK now!