DROP SCHEMA IF EXISTS lab2 CASCADE;
CREATE SCHEMA lab2;

CREATE TABLE lab2.building (
	address VARCHAR(75) NOT NULL PRIMARY KEY,
	city VARCHAR(45) NOT NULL,
	state VARCHAR(45) NOT NULL,
	name VARCHAR(45),
	zipcode INTEGER NOT NULL
);

CREATE TABLE lab2.office (
	room_number INTEGER NOT NULL PRIMARY KEY,
	building VARCHAR(180) NOT NULL REFERENCES lab2.building(address),
	waiting_room_capacity INTEGER NOT NULL
);

CREATE TABLE lab2.doctor (
	medical_license_num INTEGER NOT NULL PRIMARY KEY,
	first_name VARCHAR(45) NOT NULL,
	last_name VARCHAR(45) NOT NULL,
	office INTEGER NOT NULL REFERENCES lab2.office(room_number)
);

CREATE TABLE lab2.insurance (
	policy_number INTEGER NOT NULL PRIMARY KEY,
	insurer VARCHAR(45) NOT NULL
);

CREATE TABLE lab2.patient (
	ssn INTEGER NOT NULL PRIMARY KEY,
	first_name VARCHAR(45) NOT NULL,
	last_name VARCHAR(45) NOT NULL,
	insurance INTEGER NOT NULL REFERENCES lab2.insurance (policy_number)
);

CREATE TABLE lab2.condition (
	icd10 VARCHAR(10) NOT NULL PRIMARY KEY,
	description VARCHAR(45) NOT NULL
);

CREATE TABLE lab2.labwork (
	test_name VARCHAR(45) NOT NULL PRIMARY KEY,
	test_timestamp TIMESTAMP NOT NULL,
	test_value VARCHAR(180) NOT NULL,
	patient INTEGER NOT NULL REFERENCES lab2.patient(ssn) ON DELETE CASCADE
);

CREATE TABLE lab2.patient_condition (
	id SERIAL PRIMARY KEY,
	patient INTEGER NOT NULL REFERENCES lab2.patient(ssn) ON DELETE CASCADE,
	condition VARCHAR(10) NOT NULL REFERENCES lab2.condition(icd10)
);

CREATE TABLE lab2.appointment (
	id SERIAL PRIMARY KEY,
	doctor INTEGER NOT NULL REFERENCES lab2.doctor(medical_license_num),
	patient INTEGER NOT NULL REFERENCES lab2.patient(ssn) ON DELETE CASCADE,
	appt_date DATE NOT NULL,
	appt_time TIME NOT NULL
);

INSERT INTO lab2.building (address, city, state, name, zipcode) VALUES
	('123 Place Way', 'Turbotown', 'Missouri', 'The Taco Tower', 62226),
	('456 Location Street', 'Macroville', 'Kansas', 'Bank Building Six', 12223),
	('789 Address Drive', 'Microburg', 'Illinois', 'Marguss', 56665);

INSERT INTO lab2.office (room_number, building, waiting_room_capacity) VALUES
	(23, '123 Place Way', 60),
	(45, '456 Location Street', 20),
	(12, '789 Address Drive', 50);

INSERT INTO lab2.doctor (medical_license_num, first_name, last_name, office) VALUES
	(111222333, 'Mark', 'Doctorman', 23),
	(444555666, 'Jill', 'Neurostan', 45),
	(777888999, 'Tom', 'Killguy', 12);

INSERT INTO lab2.insurance (policy_number, insurer) VALUES
	(123456789, 'Hands in Your Pockets'),
	(987654321, 'Money Money'),
	(192837465, 'Scythe');

INSERT INTO lab2.patient (ssn, first_name, last_name, insurance) VALUES
	(123123123, 'Hal', 'Neintousan', 123456789),
	(456456456, 'Pustgrus', 'Sooks', 987654321),
	(789789789, 'Forck', 'Spune', 192837465);

INSERT INTO lab2.condition (icd10, description) VALUES
	('S04.02xA', 'Sadness plus misery.'),
	('F04.23xC', 'Pretty bad.'),
	('V03.56xQ', 'Sudden dismemberment syndrome.');

INSERT INTO lab2.labwork (test_name, test_timestamp, test_value, patient) VALUES
	('Duck Test', '2011-05-27 22:30:02', 'Positive', 123123123),
	('Foot Test', '2011-05-27 22:17:02', 'Negative', 123123123),
	('Explosion Test Test', '2011-05-27 22:48:02', 'Pending...', 123123123);

INSERT INTO lab2.patient_condition (patient, condition) VALUES
	(123123123, 'S04.02xA'),
	(123123123, 'F04.23xC'),
	(456456456, 'V03.56xQ');

INSERT INTO lab2.appointment (doctor, patient, appt_date, appt_time) VALUES
	(111222333, 456456456, '2011-05-29', '05:34:12'),
	(444555666, 123123123, '2011-05-13', '05:12:34'),
	(777888999, 789789789, '2011-05-12', '05:13:45');