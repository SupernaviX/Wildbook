drop database if exists wildbook;
create database wildbook;
use wildbook;

create table wildbook.user (
	uid int(3) auto_increment not null primary key,
	username varchar(30) not null,
	passhash varchar(255) not null,
	age int(3) not null,
	city varchar (30) not null);

create table wildbook.activity (
	aname varchar(30) primary key not null);

create table wildbook.location (
	lid int(3) auto_increment not null primary key,
	lname varchar(30) not null,
	latitude float(8,6) not null,
	longitude float(8,6) not null);

create table wildbook.useractivity (
	uid int(3) not null,
	aname varchar(30) not null,
	primary key(uid,aname),
	foreign key (uid) references user(uid),
	foreign key (aname) references activity(aname) );

create table wildbook.useractivitylocation (
	uid int(3) not null,
	aname varchar(30) not null,
	lid int(3) not null,
	primary key(uid,aname,lid),
	foreign key (uid) references user(uid),
	foreign key (aname) references activity(aname),
	foreign key (lid) references location(lid) );

create table wildbook.friend (
	firstuid int(3) not null,
	seconduid int(3) not null,
	since datetime not null,
	privacy int(3) not null,
	primary key(firstuid,seconduid),
	foreign key (firstuid) references user(uid),
	foreign key (seconduid) references user(uid) );

create table wildbook.request (
	requester int(3) not null,
	requestee int(3) not null,
	primary key(requester, requestee),
	foreign key (requester) references user(uid),
	foreign key (requestee) references user(uid) );

create table wildbook.diarypost (
	did int(3) auto_increment primary key not null,
	posteruid int(3) not null,
	posteeuid int (3) not null,
	title varchar(40) not null,
	timestamp datetime not null,
	content text not null,
	lid int(3) null,
	privacy int(1) not null,
	foreign key (posteruid) references user(uid),
	foreign key (posteeuid) references user(uid),
	foreign key (lid) references location(lid) );

create table wildbook.comment (
	cid int(3) auto_increment primary key not null,
	did int(3) not null,
	uid int(3) not null,
	message text not null,
	timestamp datetime not null,
	foreign key (did) references diarypost(did),
	foreign key (uid) references user(uid) );

create table wildbook.photo (
	pid int(3) auto_increment primary key not null,
	did int(3) not null,
	content mediumblob not null,
	content_type varchar(30) not null,
	privacy int(1) not null,
	foreign key (did) references diarypost(did));
create table wildbook.audio (
	aid int(3) auto_increment primary key not null,
	did int(3) not null,
	content mediumblob not null,
	content_type varchar(30) not null,
	privacy int(1) not null,
	foreign key (did) references diarypost(did));
create table wildbook.video (
	vid int(3) auto_increment primary key not null,
	did int(3) not null,
	content mediumblob not null,
	content_type varchar(30) not null,
	privacy int(1) not null,
	foreign key (did) references diarypost(did));

create table wildbook.diarylike (
	uid int(3) not null,
	did int(3) not null,
	primary key(uid,did),
	foreign key (did) references diarypost(did),
	foreign key (uid) references user(uid) );
	
create view wildbook.accepted_friends as
	select f1.firstuid, f1.seconduid
	from friend f1, friend f2
	where f1.firstuid=f2.seconduid and f1.seconduid=f2.firstuid;
	
create view wildbook.fof as
	select a1.firstuid as firstuid, a2.seconduid as seconduid
	from accepted_friends a1, accepted_friends a2
	where a1.seconduid = a2.firstuid and a1.firstuid != a2.seconduid;
