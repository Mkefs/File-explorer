/* Database creation */
create database files_php;
use files_php;

/* Create db tables 
 - Users
 - Dirs
 - Files
 - Subdirs
 - Shared
*/
create table users(
	id int not null auto_increment,
	primary key(id),
	email varchar(255) not null unique,
	password varchar(60) not null,
	creation_date datetime not null default current_timestamp,
	verified boolean not null default false,
	rootdir varchar(32)
);
create table dirs(
	id int not null auto_increment,
	primary key(id),
	dirname varchar(255) not null,
	_sum varchar(32),
	user int not null,
	foreign key(user) references users(id),
	creation_date datetime not null default current_timestamp
);
create table files(
	id int not null auto_increment,
	primary key(id),
	filename text not null,
	size int not null,
	realname varchar(32) not null,
	dir int not null,
	foreign key(dir) references dirs(id),
	user int not null,
	foreign key(user) references users(id),
	creation_date datetime not null default current_timestamp
);
create table subdirs(
	id int not null auto_increment,
	primary key(id),
	dir int not null,
	parent int not null,
	foreign key(dir) references dirs(id),
	foreign key(parent) references dirs(id)
);




delimiter $$

/* Make a md5sum before creating a dir, 
	using his id and name for avoiding md5 similar names */
create or replace trigger dir_sum before insert
on dirs for each row begin 
	set new._sum = md5(
		concat((
			select auto_increment from information_schema.tables 
			where table_schema = "files_php" and table_name = "dirs"
		), "-", new.dirname)
	);
end$$

/* User creation procedure, first, create the user,
	then create his root dir using his email
*/
create or replace procedure create_user
(in mail varchar(255), in pass varchar(60))
begin 
	declare user_id int;
	declare n_dir varchar(32);

	insert into users(email, password) values (mail, pass);
	set user_id = last_insert_id();

	insert into dirs(dirname, user) values 
		(concat(mail, "-root"), user_id);

	set n_dir = (select _sum from dirs where id=last_insert_id());
	update users set rootdir=n_dir where id=user_id;
end$$



/* Dir creation procedure, first get the dir id using his mdsum
	and then use before creating the dir in a subdir linking */
create or replace procedure create_dir
(in dirname varchar(255), in parent varchar(32), in userid int)
begin
	declare parent_dir int;
	set parent_dir = (select id from dirs where _sum=parent and user=userid);
	insert into dirs(dirname, user) values (dirname, userid);
	insert into subdirs(dir, parent) values (last_insert_id(), parent_dir);
end$$


/* Uploading files procedure */
create or replace procedure upload_file
(in fname text, in rname varchar(32), in _size int,
in pdir varchar(32), in userid int)
begin
	declare parent_dir int;
	set parent_dir = (select id from dirs where _sum=pdir and user=userid);
	insert into files (filename, realname, size, dir, user) values
	(fname, rname, _size, parent_dir, userid);
end$$


/* Get al subdirs using his parent mdsum and user id */
create or replace procedure get_dirs
(in parent varchar(32), in userid int) 
begin
	declare parent_dir int;
	set parent_dir = (select id from dirs where _sum=parent and user=userid);
	select d.dirname, d._sum, d.creation_date from subdirs as s 
	inner join dirs as d on s.dir=d.id where s.parent=parent_dir and d.user=userid;
end$$

/* The same as above but getting files */
create or replace procedure get_files
(in parent varchar(32), in userid int)
begin
	declare parent_dir int;
	set parent_dir = (select id from dirs where _sum=parent and user=userid);
	select * from files where user=userid and dir=parent_dir;
end$$

delimiter ;
