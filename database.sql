-- Active: 1719311726569@@127.0.0.1@5432@suahub@public
CREATE DATABASE suahub;

CREATE TABLE department(
    deptID SERIAL PRIMARY KEY,
    deptName VARCHAR(100),
    mgrID INT
);

CREATE TABLE users(
    userID SERIAL PRIMARY KEY,
    userFname VARCHAR(50),
    userSurname VARCHAR(50),
    userEmail VARCHAR(50),
    userAddress VARCHAR(100),
    ADD COLUMN userType INT,
    ADD COLUMN userPass VARCHAR(255),
    ADD COLUMN gender VARCHAR(1)
);

CREATE TABLE user_type(
    typeID SERIAL PRIMARY KEY,
    usertype VARCHAR(10)
);

ALTER TABLE users
ADD CONSTRAINT user_role
FOREIGN KEY (userType)
REFERENCES user_type(typeID);

INSERT INTO user_type(usertype) VALUES('admin'),('suahub');

CREATE TABLE user_dpt(
    userID INT,
    userDpt INT
);

ALTER TABLE user_dpt
ADD CONSTRAINT user_department
FOREIGN KEY (userID)
REFERENCES users(userID);
ALTER TABLE user_dpt
ADD CONSTRAINT departments
FOREIGN KEY (userDpt)
REFERENCES department(deptID);


CREATE TABLE salary(
    salaryID SERIAL PRIMARY KEY,
    salaryScale VARCHAR(50),
    salaryAmount FLOAT
);


CREATE TABLE users_salary(
    userID INT,
    salaryID INT
);

ALTER TABLE users_salary
ADD CONSTRAINT users_and_salary
FOREIGN KEY (userID)
REFERENCES users(userID);
ALTER TABLE users_salary
ADD CONSTRAINT salary_scales
FOREIGN KEY (salaryID)
REFERENCES salary(salaryID);

CREATE TABLE contacts(
    contactID SERIAL PRIMARY KEY,
    userID INT,
    phoneNo VARCHAR(20)
);

ALTER TABLE contacts
ADD CONSTRAINT user_contacts
FOREIGN KEY (userID)
REFERENCES users(userID);

CREATE TABLE house(
    houseID SERIAL PRIMARY KEY,
    houseNo VARCHAR(50),
    houseRent FLOAT,
    housestatus VARCHAR(50) NOT NULL,
    CHECK(houseStatus IN ('Vacant', 'Occupied'))
);

CREATE TABLE house_user(
    houseID INT,
    userID INT
);

ALTER TABLE house_user
ADD CONSTRAINT house_renters 
FOREIGN KEY (userID)
REFERENCES users(userID);

ALTER TABLE house_user
ADD CONSTRAINT houses 
FOREIGN KEY (houseID)
REFERENCES house(houseID);

CREATE TABLE contract(
    contractID SERIAL PRIMARY KEY,
    contractName VARCHAR(50),
    contractType VARCHAR(50),
    contractDescription VARCHAR(255)
);

CREATE TABLE signed_contract(
    signingID SERIAL PRIMARY KEY,
    userID INT,
    contractID INT,
    houseID INT,
    date_signed TIMESTAMP
);


ALTER TABLE signed_contract
ADD CONSTRAINT houses_number
FOREIGN KEY (houseID)
REFERENCES house(houseID);
ALTER TABLE signed_contract
ADD CONSTRAINT user_signing
FOREIGN KEY (userID)
REFERENCES users(userID);
ALTER TABLE signed_contract
ADD CONSTRAINT signed_contract
FOREIGN KEY (contractID)
REFERENCES contract(contractID);


CREATE TABLE repairment(
    repairmentID SERIAL PRIMARY KEY,
    repairmentName VARCHAR(50),
    houseID INT,
    repairmentCost FLOAT,
    repairmentDate DATE
);

ALTER TABLE repairment
ADD CONSTRAINT house_to_repair
FOREIGN KEY (houseID)
REFERENCES house(houseID);


CREATE TABLE inventory (
    invID SERIAL PRIMARY KEY,
    invName VARCHAR(50),
    invNo VARCHAR(50),
    invType VARCHAR(50),
    house INT NULL
);


ALTER TABLE inventory
ADD CONSTRAINT house_inv
FOREIGN KEY (house  )
REFERENCES house(houseID);
ALTER TABLE house_inventory
ADD CONSTRAINT inv
FOREIGN KEY (invID)
REFERENCES inventory(invID);

CREATE TABLE location (
   locationID SERIAL PRIMARY KEY,
   locationName VARCHAR(50),
   campus VARCHAR(50),
   block VARCHAR(50)
);

CREATE TABLE house_location(
    houseID INT,
    houseLocation INT
);

ALTER TABLE house_location
ADD CONSTRAINT house
FOREIGN KEY (houseID)
REFERENCES house(houseID);
ALTER TABLE house_location
ADD CONSTRAINT location
FOREIGN KEY (houseLocation)
REFERENCES location(locationID);








-- For user_dpt table
ALTER TABLE user_dpt
DROP CONSTRAINT user_department;
ALTER TABLE user_dpt
ADD CONSTRAINT user_department
FOREIGN KEY (userID)
REFERENCES users(userID)
ON DELETE CASCADE;

ALTER TABLE user_dpt
DROP CONSTRAINT departments;
ALTER TABLE user_dpt
ADD CONSTRAINT departments
FOREIGN KEY (userDpt)
REFERENCES department(deptID)
ON DELETE CASCADE;

-- For users_salary table
ALTER TABLE users_salary
DROP CONSTRAINT users_and_salary;
ALTER TABLE users_salary
ADD CONSTRAINT users_and_salary
FOREIGN KEY (userID)
REFERENCES users(userID)
ON DELETE CASCADE;

ALTER TABLE users_salary
DROP CONSTRAINT salary_scales;
ALTER TABLE users_salary
ADD CONSTRAINT salary_scales
FOREIGN KEY (salaryID)
REFERENCES salary(salaryID)
ON DELETE CASCADE;

-- For contacts table
ALTER TABLE contacts
DROP CONSTRAINT user_contacts;
ALTER TABLE contacts
ADD CONSTRAINT user_contacts
FOREIGN KEY (userID)
REFERENCES users(userID)
ON DELETE CASCADE;

-- For house_user table
ALTER TABLE house_user
DROP CONSTRAINT house_renters;
ALTER TABLE house_user
ADD CONSTRAINT house_renters
FOREIGN KEY (userID)
REFERENCES users(userID)
ON DELETE CASCADE;

ALTER TABLE house_user
DROP CONSTRAINT houses;
ALTER TABLE house_user
ADD CONSTRAINT houses
FOREIGN KEY (houseID)
REFERENCES house(houseID)
ON DELETE CASCADE;

-- For signed_contract table
ALTER TABLE signed_contract
DROP CONSTRAINT houses_number;
ALTER TABLE signed_contract
ADD CONSTRAINT houses_number
FOREIGN KEY (houseID)
REFERENCES house(houseID)
ON DELETE CASCADE;

ALTER TABLE signed_contract
DROP CONSTRAINT user_signing;
ALTER TABLE signed_contract
ADD CONSTRAINT user_signing
FOREIGN KEY (userID)
REFERENCES users(userID)
ON DELETE CASCADE;

ALTER TABLE signed_contract
DROP CONSTRAINT signed_contract;
ALTER TABLE signed_contract
ADD CONSTRAINT signed_contract
FOREIGN KEY (contractID)
REFERENCES contract(contractID)
ON DELETE CASCADE;

-- For repairment table
ALTER TABLE repairment
DROP CONSTRAINT house_to_repair;
ALTER TABLE repairment
ADD CONSTRAINT house_to_repair
FOREIGN KEY (houseID)
REFERENCES house(houseID)
ON DELETE CASCADE;

-- For inventory table
ALTER TABLE inventory
DROP column house;
ALTER TABLE inventory
ADD CONSTRAINT house_inventory
FOREIGN KEY (house)
REFERENCES house(houseid)
ON DELETE CASCADE;

-- For house_location table
ALTER TABLE house_location
DROP CONSTRAINT house;
ALTER TABLE house_location
ADD CONSTRAINT house
FOREIGN KEY (houseID)
REFERENCES house(houseID)
ON DELETE CASCADE;

ALTER TABLE house_location
DROP CONSTRAINT location;
ALTER TABLE house_location
ADD CONSTRAINT location
FOREIGN KEY (houseLocation)
REFERENCES location(locationID)
ON DELETE CASCADE;


ALTER TABLE inventory
DROP CONSTRAINT house_inv,
ADD CONSTRAINT house_inv FOREIGN KEY (house) REFERENCES house(houseid) ON DELETE CASCADE;
