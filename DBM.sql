CREATE TABLE Students (
    StudentID INT PRIMARY KEY,
    StudentName NVARCHAR(100),
    Department NVARCHAR(100)
);

CREATE TABLE Courses (
    CourseID INT PRIMARY KEY,
    CourseName NVARCHAR(100)
);

CREATE TABLE Grading (
   StudentID INT,
   CourseID INT,
   Grade nvarchar(5),
   primary key(StudentID, CourseID),
   foreign key(StudentID) references Students(StudentID),
   foreign key(CourseID) references Courses(CourseID)
);

CREATE TABLE Departments (
	DepartmentID int primary key,
    DepartmentName nvarchar(100),
    Head nvarchar(100)
);

CREATE TABLE users (
    userID INT PRIMARY KEY auto_increment,
    username varchar(20),
    password varchar(50)
);

CREATE TABLE accounts (
    StudentID INT,
    password varchar(50),
    foreign key(StudentID) references Students(StudentID)
);

CREATE TABLE Registrations (
    StudentID INT,
    CourseID INT,
    foreign key(StudentID) references Students(StudentID),
    foreign key(CourseID) references Courses(CourseID)
);

Alter table Students
ADD DepartmentID int,
drop column Department;

alter table Students
ADD foreign key (DepartmentID) references Departments(DepartmentID);


INSERT INTO Students (StudentID, StudentName) VALUES
(1001, 'John Doe'),
(1002, 'Jane Smith'),
(1003, 'Alice Brown'),
(1004, 'Bob Miller'),
(1005, 'Charlie Green'),
(1006, 'Diana Rose'),
(1007, 'Ethan White'),
(1008, 'Fiona Black'),
(1009, 'George Blue'),
(1010, 'Hannah Grey'),
(1011, 'Ivan Red'),
(1012, 'Julia Pink'),
(1013, 'Kevin Gold'),
(1014, 'Laura Silver'),
(1015, 'Michael Brown'),
(1016, 'Nina Violet'),
(1017, 'Oscar Indigo'),
(1018, 'Paula Orange'),
(1019, 'Quinn Lemon'),
(1020, 'Rachel Cyan');
