drop database if exists wildbook;
create database wildbook;
use wildbook;

create table wildbook.user (
	uid int(3) auto_increment not null primary key,
	username varchar(30) not null,
	passhash varchar(255) not null,
	age int(3) not null,
	city varchar (30) not null,
	unique (username));

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

-- Returns the following:
-- All posts made by the user
-- All posts made on the user's wall
-- All posts made by a friend that are shared with friends (or above)
CREATE PROCEDURE wildbook.timeline(user_id INT(3)) READS SQL DATA
	(SELECT dp.did AS `did`, u1.username AS `postername`, u2.username AS `posteename`, dp.title AS `title`, dp.timestamp AS `timestamp`, dp.content AS `content`
		FROM diarypost dp
		JOIN user u1 ON dp.posteruid = u1.uid
		JOIN user u2 ON dp.posteeuid = u2.uid
		WHERE dp.posteruid = user_id)
	UNION DISTINCT
	(SELECT dp.did AS `did`, u1.username AS `postername`, u2.username AS `posteename`, dp.title AS `title`, dp.timestamp AS `timestamp`, dp.content AS `content`
		FROM diarypost dp
		JOIN user u1 ON dp.posteruid = u1.uid
		JOIN user u2 ON dp.posteeuid = u2.uid
		WHERE dp.posteeuid = user_id)
	UNION DISTINCT
	(SELECT dp.did AS `did`, u1.username AS `postername`, u2.username AS `posteename`, dp.title AS `title`, dp.timestamp AS `timestamp`, dp.content AS `content`
		FROM diarypost dp
		JOIN accepted_friends af ON dp.posteruid = af.seconduid
		JOIN user u1 ON dp.posteruid = u1.uid
		JOIN user u2 ON dp.posteeuid = u2.uid
		WHERE af.firstuid = user_id AND dp.privacy >= 2)
	ORDER BY `timestamp` DESC;

-- Returns the following:
-- All posts made by the user at location
-- All posts made on the user's wall at location
-- All posts made by a friend that are shared with friends(or above) and are at location
-- All posts made by an FOF that are shared with FOFs(or above) and are at location
-- All posts shared with everyone about location
CREATE PROCEDURE wildbook.postsin(location VARCHAR(30), user_id INT(3)) READS SQL DATA
	(SELECT dp.did AS `did`, u1.username AS `postername`, u2.username AS `posteename`, dp.title AS `title`, dp.timestamp AS `timestamp`, dp.content AS `content`
		FROM diarypost dp
		JOIN user u1 ON dp.posteruid = u1.uid
		JOIN user u2 ON dp.posteeuid = u2.uid
		JOIN location l ON dp.lid = l.lid
		WHERE dp.posteruid = user_id AND l.lname = location)
	UNION DISTINCT
	(SELECT dp.did AS `did`, u1.username AS `postername`, u2.username AS `posteename`, dp.title AS `title`, dp.timestamp AS `timestamp`, dp.content AS `content`
		FROM diarypost dp
		JOIN user u1 ON dp.posteruid = u1.uid
		JOIN user u2 ON dp.posteeuid = u2.uid
		JOIN location l ON dp.lid = l.lid
		WHERE dp.posteeuid = user_id AND l.lname = location)
	UNION DISTINCT
	(SELECT dp.did AS `did`, u1.username AS `postername`, u2.username AS `posteename`, dp.title AS `title`, dp.timestamp AS `timestamp`, dp.content AS `content`
		FROM diarypost dp
		JOIN accepted_friends af ON dp.posteruid = af.seconduid
		JOIN user u1 ON dp.posteruid = u1.uid
		JOIN user u2 ON dp.posteeuid = u2.uid
		JOIN location l ON dp.lid = l.lid
		WHERE af.firstuid = user_id AND dp.privacy >= 2 AND l.lname = location)
	UNION DISTINCT
	(SELECT dp.did AS `did`, u1.username AS `postername`, u2.username AS `posteename`, dp.title AS `title`, dp.timestamp AS `timestamp`, dp.content AS `content`
		FROM diarypost dp
		JOIN fof ON dp.posteruid = fof.seconduid
		JOIN user u1 ON dp.posteruid = u1.uid
		JOIN user u2 ON dp.posteeuid = u2.uid
		JOIN location l ON dp.lid = l.lid
		WHERE fof.firstuid = user_id AND dp.privacy >= 3 AND l.lname = location)
	UNION DISTINCT
	(SELECT dp.did AS `did`, u1.username AS `postername`, u2.username AS `posteename`, dp.title AS `title`, dp.timestamp AS `timestamp`, dp.content AS `content`
		FROM diarypost dp
		JOIN user u1 ON dp.posteruid = u1.uid
		JOIN user u2 ON dp.posteeuid = u2.uid
		JOIN location l ON dp.lid = l.lid
		WHERE dp.privacy >= 4 AND l.lname = location)
	ORDER BY `timestamp` DESC;

-- Returns the following:
-- All posts made by the user about term
-- All posts made on the user's wall about term
-- All posts made by a friend that are shared with friends(or above) and are about term
-- All posts made by an FOF that are shared with FOFs(or above) and are about term
-- All posts shared with everyone about term
CREATE PROCEDURE wildbook.postsabout(term VARCHAR(65535), user_id INT(3)) READS SQL DATA
	(SELECT dp.did AS `did`, u1.username AS `postername`, u2.username AS `posteename`, dp.title AS `title`, dp.timestamp AS `timestamp`, dp.content AS `content`
		FROM diarypost dp
		JOIN user u1 ON dp.posteruid = u1.uid
		JOIN user u2 ON dp.posteeuid = u2.uid
		WHERE dp.posteruid = user_id AND dp.content LIKE CONCAT('%', term, '%'))
	UNION DISTINCT
	(SELECT dp.did AS `did`, u1.username AS `postername`, u2.username AS `posteename`, dp.title AS `title`, dp.timestamp AS `timestamp`, dp.content AS `content`
		FROM diarypost dp
		JOIN user u1 ON dp.posteruid = u1.uid
		JOIN user u2 ON dp.posteeuid = u2.uid
		JOIN location l ON dp.lid = l.lid
		WHERE dp.posteeuid = user_id AND dp.content LIKE CONCAT('%', term, '%'))
	UNION DISTINCT
	(SELECT dp.did AS `did`, u1.username AS `postername`, u2.username AS `posteename`, dp.title AS `title`, dp.timestamp AS `timestamp`, dp.content AS `content`
		FROM diarypost dp
		JOIN accepted_friends af ON dp.posteruid = af.seconduid
		JOIN user u1 ON dp.posteruid = u1.uid
		JOIN user u2 ON dp.posteeuid = u2.uid
		JOIN location l ON dp.lid = l.lid
		WHERE af.firstuid = user_id AND dp.privacy >= 2 AND dp.content LIKE CONCAT('%', term, '%'))
	UNION DISTINCT
	(SELECT dp.did AS `did`, u1.username AS `postername`, u2.username AS `posteename`, dp.title AS `title`, dp.timestamp AS `timestamp`, dp.content AS `content`
		FROM diarypost dp
		JOIN fof ON dp.posteruid = fof.seconduid
		JOIN user u1 ON dp.posteruid = u1.uid
		JOIN user u2 ON dp.posteeuid = u2.uid
		JOIN location l ON dp.lid = l.lid
		WHERE fof.firstuid = user_id AND dp.privacy >= 3 AND dp.content LIKE CONCAT('%', term, '%'))
	UNION DISTINCT
	(SELECT dp.did AS `did`, u1.username AS `postername`, u2.username AS `posteename`, dp.title AS `title`, dp.timestamp AS `timestamp`, dp.content AS `content`
		FROM diarypost dp
		JOIN user u1 ON dp.posteruid = u1.uid
		JOIN user u2 ON dp.posteeuid = u2.uid
		JOIN location l ON dp.lid = l.lid
		WHERE dp.privacy >= 4 AND dp.content LIKE CONCAT('%', term, '%'))
	ORDER BY `timestamp` DESC;

-- Does a search over users, activities, and locations.
-- Returns a table with two columns:
	-- name is the full name of what's being searched for
	-- type is the type of the thing (user, activity, or location)
CREATE PROCEDURE wildbook.search(term VARCHAR(65535)) READS SQL DATA
	(SELECT `username` AS `name`, 'user' AS `type`
		FROM `user`
		WHERE `username` LIKE CONCAT('%', term, '%'))
	UNION
	(SELECT `aname` AS `name`, 'activity' AS `type`
		FROM `activity`
		WHERE `aname` LIKE CONCAT('%', term, '%'))
	UNION
	(SELECT `lname` AS `name`, 'location' AS `type`
		FROM `location`
		WHERE `lname` LIKE CONCAT('%', term, '%'))
	ORDER BY CHAR_LENGTH(`name`);