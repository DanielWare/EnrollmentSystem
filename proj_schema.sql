drop table studentenrolledcourses;
drop table my_tmp;

drop table section cascade constraints;
drop table clientsession cascade constraints;
drop table myclient cascade constraints;
drop table student cascade constraints;
drop table class cascade constraints;


create table myclient (
	clientid varchar2(8) primary key,
	password varchar2(12),
	aflag varchar2(1),
	sflag varchar2(1)
);

create table clientsession (
	sessionid varchar2(32) primary key,
	clientid varchar2(8) not null,
	sessiondate date,
	foreign key (clientid) references myclient ON DELETE CASCADE
);

create table student (
	sid varchar2(8) primary key,
	fname varchar2(30) not null,
	lname varchar2(30) not null,
	clientid varchar2(8) not null,
	age number(2),
	streetNumber number(8),
	streetName varchar2(30),
	city varchar2(30),
	state varchar2(30),
	zipCode varchar2(5),
	typeflag varchar2(1),
	probationflag varchar2(1),
	gpa number(2,1),
	foreign key (clientid) references myclient ON DELETE CASCADE
);

create table class (
	cid varchar2(6) primary key,
	title varchar2(30),
	credits number(1),
	prereq1 varchar2(6),
	prereq2 varchar2(6),
	foreign key (prereq1) references class (cid),
	foreign key (prereq2) references class (cid)
); 

create table section (
	cid varchar2(6) not null,
	sectionid number(4) not null,
	semester number(4) not null,
	stime varchar2(10),
	maxstudents number(3),
	numstudents number(3),
	enrolldeadline date,
	primary key (cid, sectionid, semester),
	foreign key (cid) references class
);

create table studentenrolledcourses (
	sid varchar2(8) not null,
	cid varchar2(6) not null,
	semester number(4) not null,
	sectionid number(4) not null,
	enrollflag varchar2(1),
	grade number(1),
	primary key (sid, cid, sectionid, semester),
	foreign key (sid) references student ON DELETE CASCADE,
	foreign key (cid, sectionid, semester) references section (cid, sectionid, semester)
);

create table my_tmp(
	my_grade number,
	my_credits number
);

--probationflag trigger
create or replace trigger probation_trigger
	after insert or update of gpa on Student
	declare
		CURSOR c1 is select sid, gpa from student order by sid;
		my_sid varchar2(8);
		my_gpa number(2,1);
	begin
		OPEN c1;
		LOOP
			fetch c1 into my_sid, my_gpa;
			EXIT WHEN c1%NOTFOUND;
			--dbms_output.put_line(my_sid);
			--dbms_output.put_line(my_gpa);
			IF my_gpa < 2.0 THEN
				--dbms_output.put_line('prob');
				update student set probationflag = 1 where sid = my_sid;
			ELSE
				--dbms_output.put_line('no prob');
				update student set probationflag = 0 where sid = my_sid;
			END IF;
		END LOOP;
		CLOSE c1;
	end;
	/

--update_gpa procedure
create or replace procedure update_gpa(student_id in varchar2) as
	CURSOR c1 is select grade, credits from class natural join studentenrolledcourses
	where sid = student_id and enrollflag = 0;

	my_grade number;
	my_credits number;

	grade_total number;
	credits_total number;

	my_gpa number(3,1);
	begin

		delete from my_tmp;
		commit;
		
		open c1;
		LOOP
			fetch c1 into my_grade, my_credits;
			EXIT WHEN c1%NOTFOUND;
			my_grade := my_grade * my_credits;
			insert into my_tmp values(my_grade, my_credits);
			commit;
		END LOOP;
		close c1;
		select SUM(my_grade) into grade_total from my_tmp;
		select SUM(my_credits) into  credits_total from my_tmp;
		my_gpa := grade_total/credits_total;
		--dbms_output.put_line(my_gpa);
		update student set gpa = my_gpa where sid = student_id;
		commit;
	end;
	/


insert into myclient values ('dware', 'daniel', 1, 1);
insert into myclient values ('mwhite', 'matlock', 1, 1);
insert into myclient values ('jdoe', 'john', 0, 1);

insert into student values ('mw000001', 'Matlock', 'White', 'mwhite', 25, 1, 'a', 'edmond', 'ok', '7', 'u', 0, null);
insert into student values ('dw000002', 'Daniel', 'Ware', 'dware', 25, 2, 'a' , 'edmond', 'ok', 7, 'g', 0, null);
insert into student values ('jd000003', 'John', 'Doe', 'jdoe', 25, 3, 'b', 'edmond', 'ok', 7, 'u', 0, null);

insert into class values ( 'cs1111', 'Intro to Computers', 3, null, null);
insert into class values ( 'ma1111', 'Math 1', 4, null, null);
insert into class values ( 'cs2111', 'Programming 1', 3, 'cs1111', null);
insert into class values ( 'cs2211', 'Programming 2', 3, 'cs2111', 'ma1111');
insert into class values ( 'ma2111', 'Math 2', 4, 'ma1111', null);
insert into class values ( 'ma2211', 'Math 3', 4, 'ma1111', 'ma2111');

--cs sections
insert into section values ('cs1111', 0001, 2014,    0,  3, 3, TO_DATE('20140101', 'yyyymmdd'));
insert into section values ('cs2111', 0001, 2014,    0,  2, 1, TO_DATE('20140101', 'yyyymmdd'));
insert into section values ('cs2211', 0001, 2014,    0,  2, 0, TO_DATE('20140101', 'yyyymmdd'));
insert into section values ('cs1111', 0001, 2015, 1300,  3, 0, TO_DATE('20151225', 'yyyymmdd'));
insert into section values ('cs2111', 0001, 2015, 1400,  2, 0, TO_DATE('20151101', 'yyyymmdd'));
insert into section values ('cs2211', 0001, 2015, 1500,  1, 0, TO_DATE('20151225', 'yyyymmdd'));
--ma sections
insert into section values ('ma1111', 0001, 2014,    0,  3, 3, TO_DATE('20140101', 'yyyymmdd'));
insert into section values ('ma2111', 0001, 2014,    0,  2, 1, TO_DATE('20140101', 'yyyymmdd'));
insert into section values ('ma2211', 0001, 2014,    0,  2, 0, TO_DATE('20140101', 'yyyymmdd'));
insert into section values ('ma1111', 0001, 2015, 1300,  3, 0, TO_DATE('20151225', 'yyyymmdd'));
insert into section values ('ma2111', 0001, 2015, 1400,  2, 0, TO_DATE('20151101', 'yyyymmdd')); 
insert into section values ('ma2211', 0001, 2015, 1500,  1, 0, TO_DATE('20151225', 'yyyymmdd'));

insert into studentenrolledcourses values ( 'mw000001', 'cs1111', 2014, 0001, 0, 1);
insert into studentenrolledcourses values ( 'dw000002', 'cs1111', 2014, 0001, 0, 1);
insert into studentenrolledcourses values ( 'jd000003', 'cs1111', 2014, 0001, 0, 2);
insert into studentenrolledcourses values ( 'dw000002', 'cs2111', 2014, 0001, 0, 4);
insert into studentenrolledcourses values ('dw000002', 'ma1111', 2014, 0001, 0, 3);

create or replace procedure check_deadline
	(my_cid in varchar2, my_sectionid in varchar2, my_semester in number, my_error out varchar2)
	is
	my_date date := CURRENT_DATE;
	my_enrolldeadline date;

	begin
		select enrolldeadline into my_enrolldeadline from section 
			where cid = my_cid and sectionid = my_sectionid and semester = my_semester;

		IF my_enrolldeadline < my_date THEN
			dbms_output.put_line('deadline passed error');
			my_error := 'Enroll deadline passed for class ' 
				|| my_cid
				|| ' section '
				|| my_sectionid;
		END IF;
	END;
	/


create or replace procedure check_passed_course
	(my_cid in varchar2, my_sid in varchar2, my_error out varchar2)
	is
	my_grade number;
	begin
		select max(grade) into my_grade from studentenrolledcourses 
			where cid = my_cid and sid = my_sid;
		IF my_grade IS NOT NULL THEN
			IF my_grade > 1 THEN
				my_error := 'Class '
					|| my_cid
					|| ' previously passed';
			END IF;
		END IF;
	END;
	/


create or replace procedure check_prereq
	(my_cid in varchar2, my_sectionid in varchar2, my_sid in varchar2, my_error out varchar2)
	is
	my_prereq varchar2(30);
	my_tmp number;
	begin
		--check prereq1
		select prereq1 into my_prereq from class
			where cid = my_cid;
		IF my_prereq IS NOT NULL THEN
			select count(*) into my_tmp from studentenrolledcourses
				where cid = my_prereq and sid = my_sid and enrollflag = 0;
			IF my_tmp = 0 THEN
				my_error := 'Prereq1 not taken for class '
					|| my_cid
					|| ' section '
					|| my_sectionid;
			END IF;
		END IF;

		--check prereq2
		select prereq2 into my_prereq from class 
			where cid = my_cid;
		IF my_prereq IS NOT NULL THEN
			select count(*) into my_tmp from studentenrolledcourses
				where cid = my_prereq and sid = my_sid and enrollflag = 0;
			IF my_tmp = 0 THEN
				my_error := my_error 
					|| 'Prereq2 not taken for class '
					|| my_cid
					|| ' section '
					|| my_sectionid;
			END IF;
		END IF;
	END;
	/


create or replace procedure check_seat_available
	(my_cid in varchar2, my_sectionid in varchar2, my_semester in number, my_sid in varchar2, my_error out varchar2)
	is
	my_students number;
	my_max_students number;
	my_tmp number;
	begin
		delete from my_tmp;
		select count(*) into my_tmp from studentenrolledcourses
			where cid = my_cid and sectionid = my_sectionid and semester=my_semester and enrollflag = 1;
		IF my_tmp = 0 THEN
			select numstudents, maxstudents into my_students, my_max_students from section
				where cid = my_cid and sectionid = my_sectionid and semester = my_semester FOR UPDATE;
			IF (my_max_students - my_students) > 0 THEN
				insert into studentenrolledcourses 
					values (my_sid, my_cid, my_semester, my_sectionid, 1, NULL);
				my_students := my_students + 1;
				update section set numstudents = my_students
					where cid = my_cid and sectionid = my_sectionid and semester = my_semester;
				COMMIT;
			ELSE
				ROLLBACK;
				my_error := 'No seats available for '
					|| my_cid
					|| ' section '
					|| my_sectionid;
			END IF;
		ELSE
			my_error := 'Currently enrolled in '
				|| my_cid
				|| ' section '
				|| my_sectionid;
		END IF;
	END;
	/


create or replace procedure new_student_id
	(my_fname in varchar2, my_lname in varchar2, my_clientid in varchar2, my_sid out varchar2, my_error out varchar2)
	is
	--my_sid varchar2(30);
	my_count number;
	begin
		LOCK TABLE student in ROW EXCLUSIVE MODE NOWAIT;
		select count(*) into my_count from student;
		my_count := my_count + 1;
		my_sid := SUBSTR(my_fname,1,1) || SUBSTR(my_lname,1,1)
			|| TO_CHAR(my_count,'FM000000');
		insert into student (sid, fname, lname, clientid) values (my_sid, my_fname, my_lname, my_clientid); 
		commit;
		my_error := my_sid;
	end;
	/

create or replace view currently_enrolled_courses as
	select sid, cid, title, sectionid, credits, grade
	from class natural join studentenrolledcourses where enrollflag = 1;

commit;
