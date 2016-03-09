create database phone;
use phone;

create table PERSON (
	PersonId integer not null auto_increment,
	Name char(35) not null,
	CONSTRAINT PERSON_PK PRIMARY KEY(PersonId)
);

create table PHONE (
	PhoneId integer not null auto_increment,
	PersonId integer not null,
	PhoneType ENUM('other', 'work','home','cell','fax') not null,
	PhoneNumber text(10) not null,
	CONSTRAINT PHONE_PK PRIMARY KEY(PhoneId),
	CONSTRAINT PERSON_FK FOREIGN KEY (PersonId) references PERSON(PersonId)
);
	

	