-- Basic configuration for Infoschool

delete from person where id=1 or (first_name='' and last_name='admin');
insert into person (id,passwd,last_name) values(1,password(''),'admin');
delete from admin where pid=1;
insert into admin (pid) values(1);
delete from gruppe where id='1';
insert into gruppe (id,name,leiter) values (1,'All',1);
delete from cron;
insert into cron (minute,hour,do) values (0,0,'supply/clean.php');
delete from about;
insert into about (name) values ('provider');
insert into about (name) values ('admin');
delete from db_version;
insert into db_version (db_version) values ('2005_10_22_16_55');

