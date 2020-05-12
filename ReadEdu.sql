-- phpMyAdmin SQL Dump
-- version 4.0.10.18
-- https://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: Apr 29, 2017 at 06:49 PM
-- Server version: 5.6.35-cll-lve
-- PHP Version: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `ReadEdu`
--
CREATE DATABASE IF NOT EXISTS `ReadEdu` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `ReadEdu`;

DELIMITER $$
--
-- Procedures
--
DROP PROCEDURE IF EXISTS `addBook`$$
CREATE  PROCEDURE `addBook`(IN `pBookTitle` VARCHAR(100), IN `pBookLevel` VARCHAR(100), IN `pGenre` VARCHAR(100), IN `pPoints` VARCHAR(100), IN `ppicPath` VARCHAR(100), IN `pWordCount` INT(10), IN `pAuthorLastName` VARCHAR(100), IN `pAuthorFirstName` VARCHAR(100), IN `pISBN` INT(100))
    NO SQL
BEGIN
	declare pBookId int;

	-- add a book
	insert into book
		(BookTitle, BookLevel, Genre, Points, picPath, wordCount, authorLastName, authorFirstName,ISBN)
	values(
		pBookTitle, pBookLevel, pGenre, pPoints, ppicPath, pWordCount, pAuthorLastName, pAuthorFirstName,pISBN);

    SELECT LAST_INSERT_ID() into pBookId;

END$$

DROP PROCEDURE IF EXISTS `addBookQuiz`$$
CREATE  PROCEDURE `addBookQuiz`(IN `pBookId` INT(10), IN `pIsPractice` INT(1), IN `pNumOfQuestions` INT(1))
    NO SQL
BEGIN
	declare pBookQuizId int;
    
	insert into bookquiz ( BookId, IsPractice, numOfQuestions)
    values ( pBookId, pIsPractice, pNumOfQuestions);
    
    select LAST_INSERT_ID() into pBookQuizId;
    
END$$

DROP PROCEDURE IF EXISTS `AddNewStudent`$$
CREATE  PROCEDURE `AddNewStudent`(IN `pFName` VARCHAR(50), IN `pLName` VARCHAR(50), IN `pGender` CHAR(1), IN `pDOB` DATE, IN `pPass` VARCHAR(50), IN `pEmail` VARCHAR(50), IN `pVerified` TINYINT(1), IN `pClassId` INT)
    NO SQL
BEGIN 
declare pStudentlogin varchar(50) DEFAULT "";
declare pStudentId int;

DECLARE pRollback BOOL DEFAULT 0;
DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET pRollback = 1;

START TRANSACTION;


		set pStudentlogin=concat(LEFT(pFName,1),pLName,cast(year(pDOB)+18  as char(50)));
		select pStudentlogin;
		INSERT INTO member
		(`LoginId`,`Password`,`Email`,`Verified`,`Created`,`TeacherLogin`)
		VALUES
		(pStudentlogin,pPass,pEmail,pVerified,CURRENT_TIMESTAMP (), 0);
		
        
		INSERT INTO student
		(
		`StudentFirstName`,`StudentLastName`,`Gender`,`DateOfBirth`,`studentLoginId`)
		VALUES
		(pFName,pLName,pGender,pDOB,pStudentlogin);
		
        SELECT LAST_INSERT_ID() into pStudentId;
		select pStudentId;

        
        insert into studentclass 
        (StudentId, ClassId,  ReadingLevel)
        values
        (pStudentId, pClassId, 0);
        
        select pRollback;
IF pRollback THEN
	ROLLBACK;
ELSE
	COMMIT;
END IF;

END$$

DROP PROCEDURE IF EXISTS `AddNewTeacher`$$
CREATE  PROCEDURE `AddNewTeacher`(IN `pFName` VARCHAR(50), IN `pLName` VARCHAR(50), IN `pGender` CHAR(1), IN `pDOB` DATE, IN `pPass` VARCHAR(50), IN `pEmail` VARCHAR(50), IN `pVerified` TINYINT(1), IN `pSchoolId` INT, IN `pGradeLevel` INT)
    NO SQL
BEGIN
declare pTeacherlogin varchar(50) DEFAULT "";
declare pSchoolYear varchar(30);
declare pTeacherId int;




DECLARE pRollback BOOL DEFAULT 0;
DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET pRollback = 1;
START TRANSACTION;

		if (month(CURRENT_TIMESTAMP())>6) then 
			set pSchoolYear =concat( year(CURRENT_TIMESTAMP()),'-', year(CURRENT_TIMESTAMP())+1);
		else
			set pSchoolYear =concat( year(CURRENT_TIMESTAMP())-1,'-', year(CURRENT_TIMESTAMP()));
		end if;
		set pTeacherlogin=concat(LEFT(pFName,1),pLName,cast(year(pDOB)+18  as char(50)));
		select pTeacherlogin;
		INSERT INTO teacher
		(
		`TeacherFirstName`,`TeacherLastName`,`Gender`,`DateOfBirth`,`TeacherLoginId`)
		VALUES
		(pFName,pLName,pGender,pDOB,pTeacherlogin);

		 SELECT LAST_INSERT_ID() into pTeacherId;


		INSERT INTO member
		(`LoginId`,`Password`,`Email`,`Verified`,`Created`,`TeacherLogin`)
		VALUES
		(pTeacherlogin,pPass,pEmail,pVerified,CURRENT_TIMESTAMP (), 1);
		
        insert into class
        (teacherId, schoolId, gradeLevel, schoolYear )
        values
        (pTeacherId, pSchoolId, pGradeLevel,pSchoolYear);
        
        select pRollback;
IF pRollback THEN
	ROLLBACK;
ELSE
	COMMIT;
END IF;

END$$

DROP PROCEDURE IF EXISTS `AddQuestionsAndAnswers`$$
CREATE  PROCEDURE `AddQuestionsAndAnswers`(IN `pQText` VARCHAR(500), IN `pRelatedParagraph` VARCHAR(500), IN `pBookQuizId` INT(10), IN `pTypeOfQuestion` VARCHAR(200), IN `pAnswerText1` VARCHAR(200), IN `pCorrect1` INT(10), IN `pAnswerText2` VARCHAR(200), IN `pCorrect2` INT(10), IN `pAnswerText3` VARCHAR(200), IN `pCorrect3` INT(10), IN `pAnswerText4` VARCHAR(200), IN `pCorrect4` INT(10))
    NO SQL
BEGIN
declare pQId int;

DECLARE pRollback BOOL DEFAULT 0;
DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET pRollback = 1;

START TRANSACTION;
	
	insert into question 
	(QText, RelatedParagraph, BookQuizId, TypeOfQuestion)
	values 
	(pQText, pRelatedParagraph, pBookQuizId, pTypeOfQuestion);

	select LAST_INSERT_ID() into pQId;
    
    
    insert into answer (AnswerText,Correct,QId)
    values (pAnswerText1, pCorrect1, pQId);
    
    insert into answer (AnswerText,Correct,QId)
    values (pAnswerText2, pCorrect2, pQId);
    
    insert into answer (AnswerText,Correct,QId)
    values (pAnswerText3, pCorrect3, pQId);
    
    insert into answer (AnswerText,Correct,QId)
    values (pAnswerText4, pCorrect4, pQId);   

    select pRollback;
IF pRollback THEN
	ROLLBACK;
ELSE
	COMMIT;
END IF;
  
END$$

DROP PROCEDURE IF EXISTS `addSchool`$$
CREATE  PROCEDURE `addSchool`(IN `name` VARCHAR(200), IN `loc` VARCHAR(200))
    NO SQL
begin
insert into school (SchoolName, SchoolLocation)
values(name,  loc);
end$$

DROP PROCEDURE IF EXISTS `AddStudentQuiz`$$
CREATE  PROCEDURE `AddStudentQuiz`(IN `pBookQuizId` INT(2), IN `pSCId` INT(2))
    NO SQL
    SQL SECURITY INVOKER
BEGIN
declare varBookQuizId int;
declare pNumOfQuestions int;

set varBookQuizId=0;
set pNumOfQuestions=0;

select StudentQuizId, numCorrectQuestions
into varBookQuizId,pNumOfQuestions
from  studentquiz  
where studentquiz.SCId= pSCId and studentquiz.bookquizId=pBookQuizId;

if (varBookQuizId<>0 and pNumOfQuestions=0) then
	delete from studentquiz where studentquiz.SCId= pSCId and studentquiz.bookquizId=pBookQuizId;
	set varBookQuizId=0;
end if;

if (varBookQuizId = 0) then
		
        INSERT INTO studentquiz
		(SCId,
		DateOfQuiz,
		TimeOfQuiz,
		BookQuizId)
		VALUES
		(pSCId,
		curdate(),
		curtime(),
		pBookQuizId
		);
        select NumOfQuestions
        into pNumOfQuestions
        from bookquiz
        where bookquizId= pBookQuizId;
		SELECT LAST_INSERT_ID() as studentquizId, pNumOfQuestions as numOfQuestions;
        
else
		SELECT 0 as studentquizId, 0 as numOfQuestions;
end if;

END$$

DROP PROCEDURE IF EXISTS `AddUpdateStudnetRef`$$
CREATE  PROCEDURE `AddUpdateStudnetRef`(IN `pStudentQuizId` INT(12), IN `pQId` INT(11), IN `pReflectionId` INT(11))
    NO SQL
BEGIN
declare pExisted tinyint default 0;

select QId into pExisted 
from studentreflection
where StudentQuizId=pStudentQuizId
and QId= pQId;

if (pExisted=0) then
	insert into studentreflection  (StudentQuizId,QId,ReflectionId)
	values(pStudentQuizId,pQId,pReflectionId);
else
	update studentreflection
    set ReflectionId= pReflectionId
	where StudentQuizId=pStudentQuizId
	and QId= pQId;
end if;
END$$

DROP PROCEDURE IF EXISTS `GetAllBooksAndQuizzes`$$
CREATE  PROCEDURE `GetAllBooksAndQuizzes`()
    NO SQL
    SQL SECURITY INVOKER
BEGIN

	SELECT   b.bookId, b.bookTitle,  b.BookLevel, b.Genre, b.points as possiblePoints, b.picPath
			,concat(authorFirstName,' ',authorLastName) as authorname, b.ISBN
			,bq.BookQuizId, bq.numOfQuestions, bq.IsPractice 
	FROM book b
	LEFT JOIN bookquiz bq on b.bookId = bq.BookId
    ORDER BY b.bookTitle;
END$$

DROP PROCEDURE IF EXISTS `getAuthentication`$$
CREATE  PROCEDURE `getAuthentication`(IN `pLogin` VARCHAR(50), IN `pPassword` VARCHAR(50))
    NO SQL
select teacherLogin loginType
    from member
    where loginId=pLogin and password=pPassword$$

DROP PROCEDURE IF EXISTS `getBookInfo`$$
CREATE  PROCEDURE `getBookInfo`(IN `pBookId` INT(11))
    NO SQL
BEGIN

select bookId, bookTitle, BookLevel, Genre, points as possiblePoints, picPath,concat(authorFirstName,' ',authorLastName) as authorname, ISBN
from book
where bookId=pBookId;

END$$

DROP PROCEDURE IF EXISTS `GetBookQuiz`$$
CREATE  PROCEDURE `GetBookQuiz`(IN `pbookQuizId` INT(11))
    NO SQL
BEGIN

select bq.BookQuizId,q.Qid,AnswerId,QText, RelatedParagraph, AnswerText
from answer a
inner join question q on a.qid= q.qid
inner join bookquiz bq on q.bookquizId=bq.bookquizId
where bq.BookQuizId=pBookQuizId;
END$$

DROP PROCEDURE IF EXISTS `GetBookQuizzes`$$
CREATE  PROCEDURE `GetBookQuizzes`(IN `pBookId` INT(11))
    NO SQL
BEGIN

select bookquiz.BookQuizId, numOfQuestions,IsPractice 
from bookquiz
where BookId=pBookId ;


END$$

DROP PROCEDURE IF EXISTS `getBooksByLevel`$$
CREATE  PROCEDURE `getBooksByLevel`(IN `pReadingLevel` VARCHAR(5), IN `pOperator` VARCHAR(5), IN `SCId` INT(11))
    NO SQL
BEGIN

select book.bookId, bookTitle, BookLevel, Genre, points as possiblePoints, picPath,concat(authorFirstName,' ',authorLastName) as authorname, ISBN,famousbook

from book
left join bookquiz on bookquiz.bookId  = book. bookId and ispractice=0
left join  		
	(select book.bookId, count(distinct studentquiz.SCId) as famousbook
		from book
		inner join bookquiz on bookquiz.bookId  = book. bookId
		inner join studentquiz on bookquiz.bookQuizId  = studentquiz.bookquizId
		group by bookId
) subq on subq.bookId= book. bookId
where   (
		(pOperator = 'lt' and BookLevel < pReadingLevel) or
		(pOperator = 'lte' and BookLevel <= pReadingLevel) or
		(pOperator = 'eq' and BookLevel = pReadingLevel) or 
		(pOperator = 'gte' and BookLevel >= pReadingLevel) or
		(pOperator = 'gt' and BookLevel > pReadingLevel)
    )and bookquiz.BookQuizId  not in (select BookQuizId from studentquiz where studentquiz.SCId=SCId )
    order by  famousbook desc
    ;
    
END$$

DROP PROCEDURE IF EXISTS `GetClasses`$$
CREATE  PROCEDURE `GetClasses`()
    NO SQL
BEGIN
	select ClassId,SchoolName, GradeLevel,
    concat(TeacherLastName, TeacherFirstName) as teachername
    
    from  class c
    inner join  school s on c.schoolId= s.schoolId
    inner join teacher t on t.teacherId = c.TeacherId;
    
    
END$$

DROP PROCEDURE IF EXISTS `GetClassInfo`$$
CREATE  PROCEDURE `GetClassInfo`(IN `pClassId` INT)
    NO SQL
select ClassId,ClassName, SchoolName, GradeLevel,
    concat(TeacherLastName, TeacherFirstName) as teachername
    from  class c
    inner join  school s on c.schoolId= s.schoolId
    inner join teacher t on t.teacherId = c.TeacherId
where ClassId = pClassId$$

DROP PROCEDURE IF EXISTS `getClassStudentInfo`$$
CREATE  PROCEDURE `getClassStudentInfo`(IN `pClassId` INT(11))
    NO SQL
BEGIN
	IF pClassId > 0 
    THEN
			select  s.StudentId, s.StudentFirstName, s.StudentLastName, s.Gender, s.DateOfBirth, s.studentLoginId, 
					sc.SCId, sc.ReadingLevel, sc.CorrectnessLevel, c.SchoolId, c.GradeLevel, c.SchoolYear,                    
					 count(sq.SCId) as TotalQuizzesTaken,
					 max(sq.DateOfQuiz) as LastQuizDate
			from studentclass sc
			inner join class c on c.ClassId = sc.ClassId
			inner join student s on s.studentId = sc.StudentId
			left join studentquiz sq on sq.SCId = sc.SCId
			where sc.ClassId = pClassId
			group by s.StudentId, s.StudentFirstName, s.StudentLastName, s.Gender, s.DateOfBirth, s.studentLoginId, 
					sc.SCId, sc.ReadingLevel, sc.CorrectnessLevel, c.SchoolId, c.GradeLevel, c.SchoolYear
				;
	ELSE
			select  s.StudentId, s.StudentFirstName, s.StudentLastName, s.Gender, s.DateOfBirth, s.studentLoginId, 
					sc.SCId, sc.ReadingLevel, sc.CorrectnessLevel, c.SchoolId, c.GradeLevel, c.SchoolYear,
					count(sq.SCId) as TotalQuizzesTaken,
					max(sq.DateOfQuiz) as LastQuizDate
			from studentclass sc
			inner join class c on c.ClassId = sc.ClassId
			inner join student s on s.studentId = sc.StudentId            
			left join studentquiz sq on sc.SCId = sq.SCId
            group by s.StudentId, s.StudentFirstName, s.StudentLastName, s.Gender, s.DateOfBirth, s.studentLoginId, 
					sc.SCId, sc.ReadingLevel, sc.CorrectnessLevel, c.SchoolId, c.GradeLevel, c.SchoolYear
				;    
    END IF;
END$$

DROP PROCEDURE IF EXISTS `getClassTeacherInfo`$$
CREATE  PROCEDURE `getClassTeacherInfo`(IN `pClassId` INT(2))
    NO SQL
begin
select teacherFirstName, teacherLastName, className, GradeLevel, SchoolYear
from class c
inner join teacher t on t.teacherId= c.teacherId
where c.classId=pClassId;
end$$

DROP PROCEDURE IF EXISTS `GetQuizHistory`$$
CREATE  PROCEDURE `GetQuizHistory`(IN `pSCID` INT(11), IN `pYear` INT(11))
    NO SQL
BEGIN

 		select StudentQuizId,earnedPoints,   DateOfQuiz, TimeOfQuiz, Passed, numCorrectQuestions, numOfQuestions,
				IsPractice, BookTitle, BookLevel, Genre, Points, authorLastName, authorFirstName, picPath,
                concat(authorFirstName,' ',authorLastName) as authorname
		from studentquiz
		inner join bookquiz on bookquiz.BookQuizId  = studentquiz.BookQuizId
		inner join book on bookquiz.bookId  = book. bookId
		where scID= pSCId  and year(DateOfQuiz)= pYear
		order by DateOfQuiz desc, TimeOfQuiz desc;
	
END$$

DROP PROCEDURE IF EXISTS `GetReflection`$$
CREATE  PROCEDURE `GetReflection`()
    NO SQL
BEGIN
	select ReflectionId, ReflectionText
    from reflection;
END$$

DROP PROCEDURE IF EXISTS `GetSchools`$$
CREATE  PROCEDURE `GetSchools`()
    NO SQL
select schoolId, schoolname, SchoolLocation 
from school$$

DROP PROCEDURE IF EXISTS `GetStudentClassInfo`$$
CREATE  PROCEDURE `GetStudentClassInfo`(IN `pSCId` INT(2))
    NO SQL
begin
select s.StudentId,StudentFirstName,StudentLastName,Gender,DateOfBirth,sc.ClassId,sc.ReadingLevel,SCId,className
from studentclass sc 
inner join student s on s.studentId= sc.studentId
inner join class c on sc.classId=c.classId
where scId= pSCId ;
end$$

DROP PROCEDURE IF EXISTS `getStudentInfo`$$
CREATE  PROCEDURE `getStudentInfo`(IN `pLoginId` VARCHAR(50))
    NO SQL
if (month(curdate()) <=6) then
	select s.StudentId,StudentFirstName,StudentLastName,Gender,DateOfBirth,sc.ClassId,sc.ReadingLevel,SCId
    from student s
    inner join studentclass sc on s.studentId= sc.studentId
    inner join class c on sc.classId= c.classId
    where studentLoginId= pLoginId
		and SchoolYear like concat('%-',year(curdate())) ;
        
else
 	select s.StudentId,StudentFirstName,StudentLastName,Gender,DateOfBirth,sc.ClassId,sc.ReadingLevel,SCId
    from student s
    inner join studentclass sc on s.studentId= sc.studentId
    inner join class c on sc.classId= c.classId
    where studentLoginId= pLoginId and sc.SchoolYear like concat(year(curdate()),'-%');
end if$$

DROP PROCEDURE IF EXISTS `GetStudentQuizReflection`$$
CREATE  PROCEDURE `GetStudentQuizReflection`(IN `pStudentQuizId` INT(11))
    NO SQL
BEGIN
	-- get student reflection to a quiz
    select QId, ReflectionId   from studentreflection
    where StudentQuizId = pStudentQuizId;
END$$

DROP PROCEDURE IF EXISTS `GetStudentQuizResult`$$
CREATE  PROCEDURE `GetStudentQuizResult`(IN `pStudentQuizId` INT(11))
    NO SQL
BEGIN

SELECT sq.SCId,
    sq.EarnedPoints,
    sq.DateOfQuiz,
    sq.TimeOfQuiz,
    sq.PlaceQuizTaken,
    sq.StudentQuizId,
    sq.BookQuizId,
    sq.Passed,
    sq.numCorrectQuestions,
    b.BookTitle,
    bq.isPractice,
    bq.numOfQuestions,
    b.picPath,
    b.points as possiblePoints,
    concat(authorFirstName,' ',authorLastName) as authorname
FROM studentquiz sq
inner join bookquiz bq on bq.bookquizId = sq.bookquizId
inner join book b on bq.bookid= b.bookid
where StudentQuizId= pStudentQuizId;

END$$

DROP PROCEDURE IF EXISTS `GetStudentQuizResultAnswers`$$
CREATE  PROCEDURE `GetStudentQuizResultAnswers`(IN `pStudentQuizId` INT(11))
    NO SQL
BEGIN

select q.QId,QText,RelatedParagraph,AnswerText,a.AnswerId, Correct ,qa.AnswerId as studentAnswerId, case when (qa.AnswerId=a.AnswerId and Correct=1) then 1 else 0 end as studentpassed
from question q 
left join answer a on a.qId= q.qId
left join quizanswer qa on qa.qId= a.qId
left join studentquiz sq on qa.studentQuizId =  sq. studentQuizId and sq.bookQuizId= q.bookQuizId
where sq.studentQuizId= pStudentQuizId;

END$$

DROP PROCEDURE IF EXISTS `GetStudentSummary`$$
CREATE  PROCEDURE `GetStudentSummary`(IN `pSCId` INT(11))
    NO SQL
BEGIN
	select sc.ReadingLevel, sc.CorrectnessLevel, c.GradeLevel, c.SchoolYear, 
			sum(case when (IsPractice=0) then 1 else 0 end) as 'countOfRealQuizzes',
            count(StudentQuizId) as 'countOfQuizzes',
            sum(case when (IsPractice=0 and passed=1) then 1  else 0 end) as 'countOfPassingRealQuizzes',
            sum(case when (IsPractice=0 and passed=0) then 1  else 0 end) as 'countOfFailedRealQuizzes',
            sum(case when (IsPractice=1 and passed=1) then 1  else 0 end) as 'countOfPassingPQuizzes',
            sum(case when (IsPractice=1 and passed=0) then 1  else 0 end) as 'countOfFailedPQuizzes',
            sum(case when (IsPractice=0 and passed=1) then EarnedPoints  else 0 end) as 'EarnedPointsTotal',
            sum(case when (IsPractice=0 and passed=1) then numCorrectQuestions  else 0 end) as 'numCorrectQuestionsTotal'
    from studentclass sc
    inner join class c  on sc.classId  = c.classId
    inner join studentquiz sq on sc.scId= sq.scId
    inner join bookquiz bq on  sq.bookquizId= bq.bookquizId
    where sc.SCId= pSCId;
END$$

DROP PROCEDURE IF EXISTS `getTeacherClasses`$$
CREATE  PROCEDURE `getTeacherClasses`(IN `pTeacherID` INT(11))
    NO SQL
BEGIN

select t.TeacherId, t.TeacherFirstName, t.TeacherLastName, t.DateOfBirth, t.Gender, t.TeacherLoginId,
	   c.ClassId, c.GradeLevel, c.SchoolId, c.SchoolYear,
       s.SchoolName, s.SchoolLocation, c.ClassName
from teacher t
left join class c on t.TeacherId = c.TeacherId
left join school s on s.SchoolId = c.SchoolId
where pTeacherID = t.TeacherId 
;

END$$

DROP PROCEDURE IF EXISTS `getTeacherInfo`$$
CREATE  PROCEDURE `getTeacherInfo`(IN `pLoginId` VARCHAR(50))
    NO SQL
SELECT `TeacherId`,
    `TeacherFirstName`,
    `TeacherLastName`,
    `Gender`,
    `DateOfBirth`,
    `TeacherLoginId`
FROM `teacher`
where TeacherLoginId= pLoginId$$

DROP PROCEDURE IF EXISTS `lookupBooks`$$
CREATE  PROCEDURE `lookupBooks`(IN `pBookTitle` VARCHAR(55))
    NO SQL
BEGIN

select bookId, bookTitle, BookLevel, Genre, points as possiblePoints, picPath,
		concat(authorFirstName,' ',authorLastName) as authorname, ISBN
from book
where bookTitle like concat('%',pBookTitle,'%');

END$$

DROP PROCEDURE IF EXISTS `rptBookStatistic`$$
CREATE  PROCEDURE `rptBookStatistic`()
    NO SQL
begin
SET @row_number:=0;
SET @booklvl:='';

/*select bookTitle, Genre, authorFirstName, authorLastName , bookLevel, count1
from
(*/
    select bookTitle, Genre, authorFirstName, authorLastName , bookLevel, count1, 
    	   @row_number:=CASE WHEN @booklvl=bookLevel THEN @row_number+1 ELSE 1 END AS row_number,@booklvl:=bookLevel AS rownumber
    from(
         select bookTitle, Genre, authorFirstName, authorLastName , bookLevel, count(distinct sq.studentquizid) as count1
        from book b
        inner join bookquiz bq on bq.bookId= b.bookId
        left join studentquiz sq on sq.bookquizId= bq.bookquizId
		group by bookTitle, Genre, authorFirstName, authorLastName , bookLevel
    )subq1
    order by rownumber, count1;
/*)subq2
where rownumber in (1,2);        */
end$$

DROP PROCEDURE IF EXISTS `rptStudentAnswerDuration`$$
CREATE  PROCEDURE `rptStudentAnswerDuration`(IN `pSCId` INT(2))
    NO SQL
BEGIN
select  sum(case when (a.answerId<> qa.AnswerId) then duration else 0 end) as durationOfFailed,
        sum(case when (a.answerId<> qa.AnswerId) then 1 else 0 end) as countOfFailed,
        sum(case when (a.answerId= qa.AnswerId) then duration else 0 end) as durationOfSuccess,
        sum(case when (a.answerId= qa.AnswerId) then 1 else 0 end) as countOfSuccess
from studentquiz sq
inner join quizanswer qa on sq.studentquizId = qa.studentquizid
inner join answer a on a.QId= qa.QId
where sq.scId =pSCId;

end$$

DROP PROCEDURE IF EXISTS `rptStudentBookLevel`$$
CREATE  PROCEDURE `rptStudentBookLevel`(IN `pSCId` INT(11))
    NO SQL
BEGIN
	select BookTitle, BookLevel
    from studentclass sc
    inner join studentquiz sq on sc.scId= sq.scId
    inner join bookquiz bq on  sq.bookquizId= bq.bookquizId
    inner join book b on b.bookId= bq.bookId
    where sc.SCId= pSCId and IsPractice=0
    order by DateOfQuiz;
END$$

DROP PROCEDURE IF EXISTS `rptStudentMonthlyReading`$$
CREATE  PROCEDURE `rptStudentMonthlyReading`(IN `pSCId` INT(11))
    NO SQL
BEGIN
	select DateOfQuiz,
		   sum(case when (passed=1 and isPractice=0) then 1 else 0 end) as countPassed,
		   sum(case when (passed=0 and isPractice=0) then 1 else 0 end) as countFailed,
           sum(case when (isPractice=1) then 1 else 0 end) as countPractice
    from studentclass sc
    inner join studentquiz sq on sc.scId= sq.scId
    inner join bookquiz bq on  sq.bookquizId= bq.bookquizId
    
    where sc.SCId= pSCId
    group by DateOfQuiz
    order by DateOfQuiz desc;
END$$

DROP PROCEDURE IF EXISTS `rptStudentReadingLvl`$$
CREATE  PROCEDURE `rptStudentReadingLvl`(IN `pSCId` INT(11))
    NO SQL
BEGIN

	SELECT  DateOfQuiz, avg(BookLevel*numCorrectQuestions/numOfQuestions) as weightedavg	
	FROM studentquiz sq
	inner join bookquiz bq on bq.BookquizId = sq.BookquizId
	inner join book b on b.BookId = bq.bookId
	inner join studentclass sc on sc.SCId = sq.SCId
	where sq.SCId= pSCId 
	group by DateOfQuiz
    order by DateOfQuiz desc;  
    
END$$

DROP PROCEDURE IF EXISTS `rptStudentReflection`$$
CREATE  PROCEDURE `rptStudentReflection`(IN `pSCId` INT(2))
    NO SQL
Begin
select reflectionText,  count(qId) as counts
from studentquiz sq
inner join studentreflection sr on sr.studentquizId = sq.studentquizid
inner join reflection r on sr.reflectionId= r.reflectionId
where sq.scId =pSCId
group by reflectionText;

end$$

DROP PROCEDURE IF EXISTS `rptTeacherClass`$$
CREATE  PROCEDURE `rptTeacherClass`(IN `pClassId` INT(2))
    NO SQL
begin

select StudentFirstName, StudentLastName,sq.scId, sum(earnedPoints) as totalpoints

from studentclass sc 
inner join student s on s.studentId= sc.studentId
inner join studentquiz sq on sq.scId= sc.scId
where classId=pClassId
group by StudentFirstName, StudentLastName,sq.scId;

end$$

DROP PROCEDURE IF EXISTS `saveAnswer`$$
CREATE  PROCEDURE `saveAnswer`(IN `pStudentQuizId` INT(11), IN `pAnswerId` INT(11), IN `pQId` INT(11), IN `pDuration` INT(11))
    NO SQL
BEGIN
INSERT INTO quizanswer
		(`StudentQuizId`,
		`AnswerId`,
        `QId`,
		`Duration`)
VALUES
(	pStudentQuizId,
	pAnswerId,
    pQId,
	pDuration
);
SELECT LAST_INSERT_ID() as quizAnswer;
END$$

DROP PROCEDURE IF EXISTS `updateQuizScore`$$
CREATE  PROCEDURE `updateQuizScore`(IN `pstudentquizId` INT(11))
    NO SQL
BEGIN

declare totalcorrect int;
declare pPractice  int;
declare totalquestions int;
declare pSCId int;
declare possiblepoints DECIMAL(5,2);
declare earndpoints DECIMAL(5,2);
declare sumCorrect DECIMAL(5,2);
declare allCorrect DECIMAL(5,2);
declare correctessLvl DECIMAL(5,2);
declare medianLvl DECIMAL(5,2);

declare passed tinyint;
set totalcorrect=0;
set totalquestions=1 ;


select count(*) as totalcorrect
into totalcorrect
from quizanswer qa
inner join answer a on qa.answerId= a.answerId
where  correct= 1 and qa.StudentQuizId = pstudentquizId;

select totalcorrect;

set possiblepoints=0 ;

select Points, numOfQuestions, sq.SCId, bq.isPractice
into possiblepoints,totalquestions ,pSCId, pPractice
from studentquiz sq
inner join bookquiz bq on bq.BookquizId = sq.BookquizId
inner join book b on b.bookId= bq.bookId
where sq.StudentQuizId = pstudentquizId;


select possiblepoints;
select totalquestions;
select pSCId;

set earndpoints=0 ;
set earndpoints= (totalcorrect * possiblepoints) /(totalquestions);
select earndpoints;


if (((totalcorrect*100)/totalquestions)>=60) then
	set passed=1;
else
	set passed=0;
end if;

if (passed=1) then 
	update studentquiz
	set EarnedPoints=earndpoints,
	numCorrectQuestions=totalcorrect,
	passed= passed
	where StudentQuizId = pstudentquizId;
else
	update studentquiz
	set EarnedPoints=0,
	numCorrectQuestions=totalcorrect,
	passed= passed
	where StudentQuizId = pstudentquizId;

end if;

if (pPractice=0) then
		select sum(numCorrectQuestions) as correctQ, sum(numOfQuestions) as allQ
		into sumCorrect, allCorrect
		from studentquiz sq
		inner join bookquiz bq on bq.BookquizId = sq.BookquizId
		inner join studentclass sc on sc.SCId = sq.SCId
		where sq.SCId= pSCId;

		set correctessLvl = (sumCorrect*100)/allCorrect;

		select correctessLvl;
		/*
		SELECT avg(t1.BookLevel) ;

		into medianLvl
		FROM (
			SELECT @rownum:=@rownum+1 as `row_number`, b.BookLevel
			FROM studentquiz sq
			inner join bookquiz bq on bq.BookquizId = sq.BookquizId
			inner join book b on b.BookId = bq.bookId
			inner join studentclass sc on sc.SCId = sq.SCId
			,  (SELECT @rownum:=0) r
			where sq.SCId= pSCId and passed=1
			ORDER BY b.BookLevel
			) as t1, 
					(
					  SELECT count(*) as total_rows
					  FROM studentquiz sq
						inner join bookquiz bq on bq.BookquizId = sq.BookquizId
						inner join book b on b.BookId = bq.bookId
						inner join studentclass sc on sc.SCId = sq.SCId
						where sq.SCId= pSCId and passed=1
					) as t2
		WHERE 1
		AND t1.row_number in ( floor((total_rows+1)/2), floor((total_rows+2)/2) );*/
		 
		SELECT  avg(BookLevel*numCorrectQuestions/numOfQuestions) as weightedavg
		into medianLvl
		FROM studentquiz sq
		inner join bookquiz bq on bq.BookquizId = sq.BookquizId
		inner join book b on b.BookId = bq.bookId
		inner join studentclass sc on sc.SCId = sq.SCId
		where sq.SCId= pSCId 
		group by sq.SCId;
		 
		update studentclass
		set ReadingLevel=medianLvl,
		CorrectnessLevel=correctessLvl
		where scId= pSCId;
end if;

END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `answer`
--

DROP TABLE IF EXISTS `answer`;
CREATE TABLE IF NOT EXISTS `answer` (
  `AnswerId` int(11) NOT NULL AUTO_INCREMENT,
  `AnswerText` varchar(150) NOT NULL,
  `Correct` tinyint(4) DEFAULT NULL,
  `QId` int(11) NOT NULL,
  PRIMARY KEY (`AnswerId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=340 ;

--
-- Dumping data for table `answer`
--

INSERT INTO `answer` (`AnswerId`, `AnswerText`, `Correct`, `QId`) VALUES
(1, 'tell Charlie', 0, 1),
(2, 'make snow', 1, 1),
(3, 'go to Bloor''s', 0, 1),
(4, 'they didn''t want him to do anything', 0, 1),
(5, 'Hong Kong', 1, 2),
(6, 'London', 0, 2),
(7, 'Paris', 0, 2),
(8, 'Montreal', 0, 2),
(9, 'Amy', 0, 3),
(10, 'Charlie', 0, 3),
(11, 'Paton', 1, 3),
(12, 'Grandma Bone', 0, 3),
(13, 'She''s not endowed', 0, 4),
(14, 'Invisibility', 0, 4),
(15, 'Sends Shadow Words', 1, 4),
(16, 'Talks to plants', 0, 4),
(17, 'Count Badlock', 1, 5),
(18, 'Mr. Bloor', 0, 5),
(19, 'Grandma Bone', 0, 5),
(20, 'Asa', 0, 5),
(21, 'swarm of bees', 1, 6),
(22, 'Lysander''s spirits', 0, 6),
(23, 'troll', 0, 6),
(24, 'Tancred', 0, 6),
(25, 'Asa', 0, 7),
(26, 'Dorcas', 0, 7),
(27, 'Joshua', 1, 7),
(28, 'Manfred', 0, 7),
(29, 'True', 1, 8),
(30, 'False', 0, 8),
(31, 'Charlie', 0, 9),
(32, 'Uncle Paton', 0, 9),
(33, 'Mr. Onimous', 1, 9),
(34, 'Amy', 0, 9),
(35, 'Dr. Bloor', 0, 10),
(36, 'Manfred', 1, 10),
(37, 'Billy', 0, 10),
(38, 'Miss Crystal', 0, 10),
(39, 'Pope Pies.', 1, 11),
(61, 'September, 1850.', 0, 19),
(62, 'September, 1850.', 0, 19),
(63, 'July, 1848.', 1, 19),
(64, 'September, 1850.', 0, 20),
(65, 'July, 1848.', 1, 20),
(66, 'August, 1858.', 0, 20),
(67, 'July, 1648.', 0, 20),
(68, 'To honor the United States.', 0, 21),
(69, 'T protect  the city.', 0, 21),
(70, 'To honor the achievement of George Washington.', 1, 21),
(71, 'To be amusement  park.', 0, 21),
(72, 'Windows.', 0, 22),
(73, 'Aluminum Apex.', 1, 22),
(74, 'American Flag.', 0, 22),
(75, 'Cornerstone.', 0, 22),
(76, 'Washington State.', 0, 23),
(77, 'Washington D. C. in the District of Columbia.', 1, 23),
(78, 'Los Angeles, CA.', 0, 23),
(79, 'Rochester Hills, MI.', 0, 23),
(80, 'Pope Pies.', 1, 24),
(81, 'National Monument Society.', 0, 24),
(82, 'Know-Nothing Party.', 0, 24),
(83, 'The president of the united state.', 0, 24),
(84, 'The Rabbit next door', 0, 25),
(85, 'The Mayor', 1, 25),
(86, 'The Baker', 0, 25),
(87, 'The President', 0, 25),
(88, 'Halloween', 0, 26),
(89, 'New Years Day', 0, 26),
(90, 'The first day of summer', 0, 26),
(91, 'Groundhogs Day', 1, 26),
(92, 'He wanted to watch TV', 0, 27),
(93, 'He was having a party', 0, 27),
(94, 'He wanted to stay in bed', 1, 27),
(95, 'He was making a cake', 0, 27),
(96, 'True', 1, 28),
(97, 'False', 0, 28),
(100, 'Ate some pizza', 0, 29),
(101, 'Made a snowman', 1, 29),
(102, 'Went for a ride', 0, 29),
(103, 'Climbed a tree', 0, 29),
(104, 'Jack thinks they are no-one special.', 0, 30),
(105, 'Jack thinks they are Morgan and Merlin.', 0, 30),
(106, 'Jack thinks they are Teddy and Kathleen.', 1, 30),
(107, 'Jack thinks they are men who look like birds. ', 0, 30),
(108, 'Jack thinks they are no-one special.', 0, 31),
(109, 'Jack thinks they are Morgan and Merlin.', 0, 31),
(110, 'Jack thinks they are Teddy and Kathleen.', 1, 31),
(111, 'Jack thinks they are men who look like birds. ', 0, 31),
(112, 'Because time stands still when you are in Venice.', 0, 32),
(113, 'Because none of the clocks in Venice tell the correct time.', 0, 32),
(114, 'Because the fun never ends in Venice.', 0, 32),
(115, 'Because so much of the city and its traditions have been preserved through time.', 1, 32),
(116, 'Teddy and Kathleen ', 1, 33),
(117, 'No one', 0, 33),
(118, 'Merlin the Magician ', 0, 33),
(119, 'Morgan le Fay ', 0, 33),
(120, 'To have fun at the Carnival.', 0, 34),
(121, 'To save Venice from a terrible disaster. ', 1, 34),
(122, 'To find the correct time in Venice. ', 0, 34),
(123, 'To find out who the Grand Lady of the Lagoon is. ', 0, 34),
(124, 'They take a ride on a gondola. ', 0, 35),
(125, 'They use a rhyme to find her. ', 0, 35),
(126, 'They go on to find the painter named Tiepolo. ', 1, 35),
(127, 'They go back to the tree house. ', 0, 35),
(128, 'Mars, The Roman God of War.', 0, 36),
(129, 'The Grand Lady of the Lagoon. ', 0, 36),
(130, 'The ruler of Venice. ', 0, 36),
(131, 'Neptune, The Roman God of Sea. ', 1, 36),
(132, 'The beautiful woman resting in the painting that Lorenzos Dad is working on. ', 0, 37),
(133, 'The man dressed as a lady in the square. ', 0, 37),
(134, 'The people in the city. ', 0, 37),
(135, 'The city of Venice. ', 1, 37),
(136, 'They will need great patience and a bit of magic. ', 1, 38),
(137, 'They will need Teddy and Kathleens help. ', 0, 38),
(138, 'They will only need the research book he left them. ', 0, 38),
(139, 'They will only need the Book of Rhymes. ', 0, 38),
(140, 'The guards were asleep. ', 0, 39),
(141, 'Two clowns on stilts danced around the guards.  ', 1, 39),
(142, 'The carnival people distracted the guards. ', 0, 39),
(143, 'They just ran past the guards. ', 0, 39),
(144, 'Water is flowing down from the mountains.', 0, 40),
(145, 'There is a high tide and storms are at the sea. ', 0, 40),
(146, 'Winds are from the south. ', 0, 40),
(147, 'All of the conditions are that were listed are happening. ', 0, 40),
(148, 'Water is flowing down from the mountains.', 0, 41),
(149, 'There is a high tide and storms are at the sea. ', 0, 41),
(150, 'Winds are from the south. ', 0, 41),
(151, 'All of the conditions are that were listed are happening. ', 0, 41),
(152, 'The two clowns on stilts. ', 0, 42),
(153, 'The two men in the bird masks. ', 0, 42),
(154, 'Two bronze statues holding a club and striking the bell in the clock tower. ', 1, 42),
(155, 'The two grouchy and sleepy guards. ', 0, 42),
(156, 'The two clowns on stilts. ', 0, 43),
(157, 'The two men in the bird masks. ', 0, 43),
(158, 'Two bronze statues holding a club and striking the bell in the clock tower. ', 1, 43),
(159, 'The two grouchy and sleepy guards. ', 0, 43),
(160, 'One of the greatest painters of Venice in the 1700s. ', 1, 44),
(161, 'One of the greatest gondolier''s in Venice. ', 0, 44),
(162, 'One of the most famous costume makers in Venice. ', 0, 44),
(163, 'One of the greatest clock tower builders of Venice in the 1700s. ', 0, 44),
(164, 'It has the Grand Lady of the Lagoon. ', 0, 45),
(165, 'The waterways called canals, the shallow boats called gondolas. ', 1, 45),
(166, 'Clocks never tell the correct time. ', 0, 45),
(167, 'The Carnival at Candlelight. ', 0, 45),
(168, 'The angel pointed to the southeast, over the choppy sea. ', 1, 46),
(169, 'The angel turned slowly, pointing in all directions. ', 0, 46),
(170, 'The angel pointed to the Magic Tree House. ', 0, 46),
(171, 'The angel pointed to the city of Venice. ', 0, 46),
(172, 'They must save the painter named Tiepolo and Jack is the only one who can help. ', 0, 47),
(173, 'They must save the Ruler of the Seas and an angel of gold is the only one who can help.', 0, 47),
(174, 'They must save the Grand Lady of the Lagoon and The Ruler of the Seas is the only one who can help.  ', 1, 47),
(175, 'They must save the Grand Lady of the Lagoon and the King of the Jungle is the only one who can help. ', 0, 47),
(176, 'Stand on Water.  ', 0, 48),
(177, 'Make Metal Soft. ', 1, 48),
(178, 'Turn into Ducks. ', 0, 48),
(179, 'Make a Stone Come Alive. ', 0, 48),
(180, 'The Book of Ten Rhymes must only be used once and must last for four journeys. ', 1, 49),
(181, 'The Book of Ten Rhymes can not be used until Jack and Annie solve the riddles. ', 0, 49),
(182, 'The Book of Ten Rhymes can be used as often as Jack and Annie want. ', 0, 49),
(183, 'The Book of Ten Rhymes can be used only once per day. ', 0, 49),
(184, 'He lifts his spear and thrusts it at the waves. It pierces the surface of the water and it flows down like a drain. ', 1, 50),
(185, 'He lifts his spear to the sky and the water goes into the clouds. ', 0, 50),
(186, 'His spear pierces the water and the water just disappears. ', 0, 50),
(187, 'His spear directs the water into the lion, who drinks it all. ', 0, 50),
(188, 'Annie shouts, ''Neptune, Rise from the water! Save Venice, Neptune! Help us.'' ', 0, 51),
(189, 'Jack imagines the details of Tiepolo''s painting. ', 1, 51),
(190, 'The lion roared and and roared till he appeared. ', 0, 51),
(191, 'Neptune never appears, he isn''t real. ', 0, 51),
(192, 'The clock is the symbol of Venice. It stands for the timelessness of the city. ', 0, 52),
(193, 'The winged lion is the symbol of Venice. The lion stands for strength on both land and sea.', 1, 52),
(194, 'Neptune is the symbol of Venice. Neptune stands for the God of the Sea. ', 0, 52),
(195, 'The gongolas are the symbol of Venice. They stand for transportation. ', 0, 52),
(196, 'Water is flowing down from the mountains.', 0, 53),
(197, 'There is a high tide and storms are at the sea. ', 0, 53),
(198, 'Winds are from the south. ', 0, 53),
(199, 'All of the conditions are that were listed are happening. ', 0, 53),
(200, 'The Quarrymen. ', 1, 54),
(201, 'The Liverpool Lads. ', 0, 54),
(202, 'The Beatles. ', 0, 54),
(203, 'John and the Beatles. ', 0, 54),
(204, 'Beatlemania. ', 0, 55),
(205, 'Let It Be. ', 1, 55),
(206, 'A Hard Day''s Night. ', 0, 55),
(207, 'Sargent Pepper''s Lonely Hearts Club Band.', 0, 55),
(208, '1973 in San Francisco, California. ', 0, 56),
(209, '1966 in San Francisco, California. ', 1, 56),
(210, '1966 in New York, New York. ', 0, 56),
(211, '1966 in Liverpool, England. ', 0, 56),
(212, 'Imagine. ', 0, 57),
(213, 'I Wanna Hold Your Hand. ', 0, 57),
(214, 'Please, Please Me. ', 1, 57),
(215, 'Yesterday. ', 0, 57),
(216, 'The number one song for the longest duration. ', 0, 58),
(217, 'Most recorded song by other musicians. ', 1, 58),
(218, 'The most radio plays of any song. ', 0, 58),
(219, 'The largest selling song of all times. ', 0, 58),
(220, 'He died of cancer in 2001. ', 1, 59),
(221, 'He has NOT died. ', 0, 59),
(222, 'He was shot by Mark David Chapman in 1980. ', 0, 59),
(223, 'He was stabbed by Mark David Chapman in 1980. ', 0, 59),
(224, 'Pete Best. ', 0, 60),
(225, 'Tommy Moore ', 0, 60),
(226, 'George Harrison. ', 1, 60),
(227, 'Colin Hanton. ', 0, 60),
(228, 'George Harrison. ', 0, 61),
(229, 'Brian Epstein. ', 1, 61),
(230, 'Stu Sutcliffe. ', 0, 61),
(231, 'Alfred Lennon. ', 0, 61),
(232, 'The White Album. ', 0, 62),
(233, 'Abbey Road. ', 1, 62),
(234, 'Let It Be. ', 0, 62),
(235, 'A Hard Day''s Night.', 0, 62),
(236, 'Tommy Moore. ', 0, 63),
(237, 'Stu Sutcliffe. ', 1, 63),
(238, 'Ringo Starr. ', 0, 63),
(239, 'George Harrison. ', 0, 63),
(240, 'Quidditch ', 1, 64),
(241, 'Broom Racer ', 0, 64),
(242, 'Capture the Flag ', 0, 64),
(243, 'Magic Star ', 0, 64),
(244, 'One of the Seekers must catch the Snitch. ', 1, 65),
(245, 'A Beater must knock all of the other players from their brooms.', 0, 65),
(246, 'A player must hide the Quaffle. ', 0, 65),
(247, 'All Chasers must land their brooms successfully. ', 0, 65),
(248, 'pig snout ', 1, 66),
(249, 'bat wing ', 0, 66),
(250, 'cow horn ', 0, 66),
(251, 'frog tongue ', 0, 66),
(252, 'Paris ', 0, 67),
(253, 'Remus ', 0, 67),
(254, 'Vulcan ', 0, 67),
(255, 'Voldemort ', 1, 67),
(256, 'snowballs ', 1, 68),
(257, 'elves ', 0, 68),
(258, 'holly leaves ', 0, 68),
(259, 'mistletoe balls ', 0, 68),
(260, 'five pounds ', 0, 69),
(261, 'ten Knuts ', 0, 69),
(262, 'six silver Sickles ', 0, 69),
(263, 'seven gold Galleons ', 1, 69),
(264, 'He lived in a castle. ', 0, 70),
(265, 'He lived with Hagrid in his cottage at Hogwarts. ', 0, 70),
(266, 'He lived with the Dursleys at number 4, Privet Drive. ', 1, 70),
(267, 'He lived with his mother and father in London. ', 0, 70),
(268, 'Dumbledore ', 0, 71),
(269, 'Hagrid ', 1, 71),
(270, 'Ron Weasley ', 0, 71),
(271, 'Magic Marvin ', 0, 71),
(272, 'Harry has a small map of England on his forehead.', 0, 72),
(273, 'Harry has a large freckle on his forehead. ', 0, 72),
(274, 'He has a very thin scar shaped like a lightning bolt on his forehead. ', 1, 72),
(275, 'He has a birthmark shaped like Italy on his forehead. ', 0, 72),
(276, 'He looked like a ''professional athlete.'' ', 0, 73),
(277, 'He looked like a ''pig in a wig.'' ', 1, 73),
(278, 'He looked like a movie star. ', 0, 73),
(279, 'He looked like a college professor. ', 0, 73),
(280, 'Nonas ', 0, 74),
(281, 'Muggles ', 1, 74),
(282, 'Pimms ', 0, 74),
(283, 'Non-flyers ', 0, 74),
(284, 'A poem he has written will be published. ', 0, 75),
(285, 'He has a chance to win the lottery. ', 0, 75),
(286, 'He has won a magical vacation. ', 0, 75),
(287, 'He has a place at Hogwarts School of Witchcraft and Wizardry. ', 1, 75),
(288, 'Ravenclaw ', 0, 76),
(289, 'Gryffindor ', 1, 76),
(290, 'Slytherin ', 0, 76),
(291, 'Hufflepuff ', 0, 76),
(292, 'Hedwig ', 1, 77),
(293, 'Captain Kidd ', 0, 77),
(294, 'Montclair ', 0, 77),
(295, 'Edwina ', 0, 77),
(296, 'Professor Snape ', 0, 78),
(297, 'Draco Malfoy ', 0, 78),
(298, 'Neville Longbottom ', 0, 78),
(299, 'Professor Quirrell ', 1, 78),
(300, 'It is on a secret island. ', 0, 79),
(301, 'It is hundreds of miles under London. ', 1, 79),
(302, 'It is only in Hagrid''s imagination. ', 0, 79),
(303, 'It is inside the Ministry of Magic offices. ', 0, 79),
(304, 'Lily and James Potter ', 1, 80),
(305, 'Minerva and Edward McGonagall', 0, 80),
(306, 'Petunia and Vernon Dursley ', 0, 80),
(307, 'Minnie and Slim Pickens ', 0, 80),
(308, 'Snape''s Dark Arts Book ', 0, 81),
(309, 'Dumbledore''s Mirror of Erised ', 0, 81),
(310, 'Flamel''s Sorcerer''s Stone ', 1, 81),
(311, 'Hagrid''s motorcycle ', 0, 81),
(312, 'a toad ', 1, 82),
(313, 'a snake ', 0, 82),
(314, 'a turtle ', 0, 82),
(315, 'a frog ', 0, 82),
(316, 'Professor Snape ', 0, 83),
(317, 'Draco Malfoy and his gang ', 0, 83),
(318, 'Hermione and Ron ', 0, 83),
(319, 'Madam Hooch ', 0, 83),
(320, 'Washington, D.C.', 1, 84),
(321, 'Washington State', 0, 84),
(322, 'New York ', 0, 84),
(323, 'Upper State New York', 0, 84),
(324, 'New Year Day', 0, 85),
(325, 'Christmas day', 0, 85),
(326, 'Memorial Day', 1, 85),
(327, '4th of July', 0, 85),
(328, '1890', 0, 86),
(329, '1898', 0, 86),
(330, '1886', 0, 86),
(331, '1888', 1, 86),
(332, 'empty', 0, 87),
(333, 'very full', 0, 87),
(334, 'almost full', 1, 87),
(335, 'has a lot of empty space', 0, 87),
(336, 'unknown soliders dies in World War I ', 0, 88),
(337, 'unknown soliders dies in World War I and II', 1, 88),
(338, 'unknown soliders dies in Civil War', 0, 88),
(339, 'unknown people', 0, 88);

-- --------------------------------------------------------

--
-- Table structure for table `book`
--

DROP TABLE IF EXISTS `book`;
CREATE TABLE IF NOT EXISTS `book` (
  `BookId` int(11) NOT NULL AUTO_INCREMENT,
  `BookTitle` varchar(45) NOT NULL,
  `BookLevel` varchar(5) NOT NULL,
  `Genre` varchar(45) NOT NULL,
  `Points` varchar(3) NOT NULL,
  `picPath` varchar(45) DEFAULT NULL,
  `wordCount` int(11) DEFAULT NULL,
  `authorLastName` varchar(50) DEFAULT NULL,
  `authorFirstName` varchar(45) DEFAULT NULL,
  `ISBN` varchar(90) DEFAULT NULL,
  PRIMARY KEY (`BookId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `book`
--

INSERT INTO `book` (`BookId`, `BookTitle`, `BookLevel`, `Genre`, `Points`, `picPath`, `wordCount`, `authorLastName`, `authorFirstName`, `ISBN`) VALUES
(1, 'Charlie Bone and the Hidden King #5', '5.2', 'Fiction', '10', 'Charlie_Bone_and_the_Hidden_King.jpg', 71724, 'Nimmo', 'Jenny', '9781405225946'),
(2, 'The Washington Monument', '5.6', 'Nonfiction', '0.5', 'The_Washington_Monument.jpg', 1174, 'Gilmore', 'Frederic', '978-1-62687-203-5'),
(3, 'The Green Ember', '5.2', 'Fiction', '11', 'The_Green_Ember.jpg', 72785, 'Smith', 'S.D.', '978-0-9862235-1-8'),
(4, 'Grumpy Groundhog', '2.3', 'Fiction', '0.5', 'Grumpy_Groundhog.jpg', 448, 'Wright', 'Maureen', '978-0-545-70303-1'),
(5, 'Magic Tree House Carnival at Candlelight', '3.9', 'Fiction', '2.0', 'Carnival_at_Candlelight.jpg', 11833, 'Mary', 'Pope', '1-4156-7923-1'),
(6, 'Who were The Beatles?', '4.5', 'Nonfiction', '1.0', 'Who_were_The_Beatles.jpg', 9254, 'Geoff ', 'Edgers', '1-4156-4793-3'),
(7, 'Harry Potter and the Sorcerer''s Stone', '5.5', 'fiction', '12', 'Harry_Potter_and_the_Sorcerer_Stone.jpg', 77508, 'J.K. ', 'Rowling', '978-0-590-35340-3'),
(8, 'Emma Dilemma and the Soccer Nanny', '3.2', 'Fiction', '3.0', 'Emma_Dilemma_and_the_Soccer_Nanny.jpg', 21990, 'Hermes', 'Patricia', '978-0-7614-5301-7'),
(9, 'Arlington National Cemetery', '5.6 ', 'NonFiction', '0.5', 'Arlington_National_Cemetery.JPG', 1070, 'Temple', 'Bob ', '978');

-- --------------------------------------------------------

--
-- Table structure for table `bookquiz`
--

DROP TABLE IF EXISTS `bookquiz`;
CREATE TABLE IF NOT EXISTS `bookquiz` (
  `BookQuizId` int(11) NOT NULL AUTO_INCREMENT,
  `BookId` int(11) NOT NULL,
  `IsPractice` tinyint(1) NOT NULL,
  `numOfQuestions` int(11) DEFAULT NULL,
  PRIMARY KEY (`BookQuizId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `bookquiz`
--

INSERT INTO `bookquiz` (`BookQuizId`, `BookId`, `IsPractice`, `numOfQuestions`) VALUES
(1, 1, 1, 10),
(2, 1, 0, 10),
(3, 2, 0, 5),
(4, 4, 0, 5),
(5, 5, 0, 10),
(6, 5, 1, 12),
(7, 6, 0, 10),
(8, 7, 0, 10),
(9, 7, 1, 10),
(10, 9, 0, 5);

-- --------------------------------------------------------

--
-- Table structure for table `class`
--

DROP TABLE IF EXISTS `class`;
CREATE TABLE IF NOT EXISTS `class` (
  `ClassId` int(11) NOT NULL AUTO_INCREMENT,
  `TeacherId` int(11) NOT NULL,
  `SchoolId` int(11) NOT NULL,
  `GradeLevel` varchar(3) NOT NULL,
  `SchoolYear` varchar(45) DEFAULT NULL,
  `ClassName` varchar(145) DEFAULT NULL,
  PRIMARY KEY (`ClassId`),
  KEY `schoolId_idx` (`SchoolId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `class`
--

INSERT INTO `class` (`ClassId`, `TeacherId`, `SchoolId`, `GradeLevel`, `SchoolYear`, `ClassName`) VALUES
(1, 1, 1, '5', '2016-2017', 'Fifth Grade Class 1'),
(2, 2, 1, '2', '2016-2017', 'Second Grade Class 1'),
(3, 3, 2, '1', '2016-2017', 'First Grade Class 2'),
(4, 5, 1, '3', '2016-2017', 'Third Grade Combo Class'),
(5, 6, 2, '3', '2016-2017', 'Third Grade Class Frei');

-- --------------------------------------------------------

--
-- Table structure for table `loginattempts_ifwehavetime`
--

DROP TABLE IF EXISTS `loginattempts_ifwehavetime`;
CREATE TABLE IF NOT EXISTS `loginattempts_ifwehavetime` (
  `IP` varchar(20) NOT NULL,
  `Attempts` int(11) NOT NULL,
  `LastLogin` datetime NOT NULL,
  `Username` varchar(65) DEFAULT NULL,
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `member`
--

DROP TABLE IF EXISTS `member`;
CREATE TABLE IF NOT EXISTS `member` (
  `LoginId` varchar(65) NOT NULL DEFAULT '',
  `Password` varchar(65) NOT NULL DEFAULT '',
  `Email` varchar(65) NOT NULL,
  `Verified` tinyint(1) NOT NULL DEFAULT '0',
  `Created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `TeacherLogin` tinyint(4) NOT NULL,
  PRIMARY KEY (`LoginId`),
  UNIQUE KEY `username_UNIQUE` (`LoginId`),
  UNIQUE KEY `email_UNIQUE` (`Email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `member`
--

INSERT INTO `member` (`LoginId`, `Password`, `Email`, `Verified`, `Created`, `TeacherLogin`) VALUES
('AFairchild18', 'abc', 'tfairc@cc.us', 1, '2017-04-23 20:24:56', 0),
('BHaddad2024', '12312', 'test@test.com', 1, '2017-04-28 16:42:07', 0),
('CFrie1968', 'CFrie', 'cf@gmail.com', 1, '2017-03-23 21:21:08', 1),
('CVanHorn2024', '45645', 'CVanHorn2024@gmail.com', 1, '2012-08-03 05:00:00', 0),
('EGray2024', '56756', 'EGray2024@gmail.com', 1, '2012-08-03 05:00:00', 0),
('ESteely2028', '12312', 'es@gmail.com', 1, '2017-03-16 13:40:35', 1),
('FHaddad2024', '1245', 'fh@gmail.com', 1, '2017-03-24 02:06:11', 0),
('GFairchild2016', '12312', 'gfair@gmail.com', 1, '2017-03-24 14:26:46', 0),
('GGray2027', '12312', 'GGray2027@gmail.com', 1, '2015-08-03 05:00:00', 0),
('KHale2028', '12312', 'kh@gmail.com', 1, '2017-03-16 13:41:08', 1),
('RFairchild2010', '12312', 'rfair@gmail.com', 1, '2017-03-24 14:28:06', 0),
('RFisher2027', '23423', 'RFisher2027@gmail.com', 1, '2014-08-03 05:00:00', 0),
('SSmith2022', '12312', 'test@gmail.com', 1, '2017-04-29 17:31:05', 0),
('SStudent2019', 'abc123', 'test@gmail', 1, '2017-04-29 17:11:51', 0),
('SWayfair2027', '12312', 'studentemail@gmail.com', 1, '2017-04-28 18:01:16', 0),
('TFairchild1982', '12312', 'tf3581@att.com', 1, '2017-03-24 15:28:15', 1),
('trafai', 'trafai', 'tfairchild3@gatech.edu', 0, '2017-03-09 13:34:30', 0),
('YZalnes2027', '34534', 'YZalnes2027@gmail.com', 1, '2014-08-03 05:00:00', 0);

-- --------------------------------------------------------

--
-- Table structure for table `question`
--

DROP TABLE IF EXISTS `question`;
CREATE TABLE IF NOT EXISTS `question` (
  `QId` int(11) NOT NULL AUTO_INCREMENT,
  `QText` varchar(150) DEFAULT NULL,
  `RelatedParagraph` varchar(500) DEFAULT NULL,
  `BookQuizId` int(11) DEFAULT NULL,
  `TypeOfQuestion` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`QId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=89 ;

--
-- Dumping data for table `question`
--

INSERT INTO `question` (`QId`, `QText`, `RelatedParagraph`, `BookQuizId`, `TypeOfQuestion`) VALUES
(1, 'What did the Flames want Tancred to do?', '', 1, ''),
(2, 'Benjamin returns from a seven month trip to where? ', '', 1, ''),
(3, 'Who were the enchanted prawns meant for?', '', 1, ''),
(4, 'What is Naren''s power? ', '', 1, ''),
(5, 'Who put Amy under an enchantment?', '', 1, ''),
(6, 'What saved Charlie from being crushed by a wall?', '', 1, ''),
(7, 'Who stole the Mirror of Amoret back from Charlie?', '', 1, ''),
(8, 'Manfred changes endowments.', '', 1, ''),
(9, 'Who tried to fill in as the tenth child but failed?', '', 1, ''),
(10, 'Who tried to thwart Charlie''s plans for saving his father by knocking the King''s tears out of his hand?', '', 1, ''),
(20, 'When the corner stone of the building was put in place?', 'Chapter 1, Page 11, The base for the huge tower was built 3 feet(11 meters) down into the ground. the cornerstone of the building was put in place on <font color=''red''>July 4, 1884</font>.', 3, 'direct'),
(21, 'What was the purpose of building the monument?', 'Chapter 1, Page 4, The plan called fir a huge monument to <font color=''red''>honor the achievement of George Washington.</font>', 3, 'Memorized'),
(22, 'What did they put last on the monument?', 'Chapter 5, Page 19, Construction of the monument continues until December 6, 1884. On that date, workers placed an <font color=''red''>aluminum apex </font> on the top of the building.', 3, 'direct'),
(23, 'Where the monument is located?', 'Chapter 1, Page 1.', 3, 'direct'),
(24, 'Who is the famous figure who donated marbles and got stolen?', 'Chapter 4, Page 12, Around the same time, a special piece of black marble donated by <font color=''red''>Pope Pius IX</font> was stolen', 3, 'Memorized'),
(25, 'Who was trying to get Grumpy Groundhog to wake up?', 'In the first page of the story, the Mayor stood before Groundhogs door and heard a grumbly, tumbly snore', 4, ''),
(26, 'What was the town celebrating?', 'Throughout the book the people are holding signs that say Happy Groundhog Day', 4, ''),
(27, 'Why did Grumpy Groundhog want to stay inside?', 'Throughout the book the Groundhog wants to stay in bed.', 4, ''),
(28, 'When Grumpy Groundhog came out the door, they gave him glasses', 'True:  they gave him glasses because of the lights and the cameras glare', 4, ''),
(29, 'What did Grumpy Groundhog do with the children when he went outside?', 'Last page of the book', 4, ''),
(31, 'Who does Jack think the gondolier and the person with the latern are? ', '', 5, 'Straight Foreward'),
(32, 'Why is Venice is called a timeless city and a city frozen in time?', '', 5, 'Straight Foreward'),
(33, 'Who was waiting at the tree house for Jack and Annie?', '', 5, 'Straight Foreward'),
(34, 'What is Jack and Annie''s mission?', '', 5, 'Straight Foreward'),
(35, 'What do Jack and Annie do when they can not find the Grand Lady of the Lagoon?', '', 5, 'Straight Foreward'),
(36, 'Who does Lorenzo Tiepolo tell Jack and Annie The Ruler of the Sea is?', '', 5, 'Straight Foreward'),
(37, 'Who is the Grand Lady of the Lagoon?', '', 5, 'Straight Foreward'),
(38, 'What does Merlin write that Jack and Annie need to complete this mission?', '', 5, 'Straight Foreward'),
(39, 'How did Jack and Annie get past the guards to get into the palace?', '', 5, 'Straight Foreward'),
(40, 'Why do Jack and Annie think that water is involved with the disaster?', '', 5, 'Straight Foreward'),
(41, 'Why do Jack and Annie think that water is involved with the disaster?', '', 6, 'Straight Foreward'),
(43, 'The rhyme states At Midnight two men will tell out time, who are the men?', '', 6, 'Straight Foreward'),
(44, ' Who is Tiepolo? ', '', 6, 'Straight Foreward'),
(45, 'Why is the city of Venice one of the most popular tourist spots?', '', 6, 'Straight Foreward'),
(46, 'Where does the Angel of Gold tell Jack and Annie to go?   ', '', 6, 'Straight Foreward'),
(47, 'Jack and Annie''s mission from Merlin is to save who and who is the only one who can help?', '', 6, 'Straight Foreward'),
(48, 'While in the pozzi Jack and Annie use what rhyme to help them escape?', '', 6, 'Straight Foreward'),
(49, 'How must the Book Of Ten Rhymes be used?   ', '', 6, 'Straight Foreward'),
(50, 'How does Neptune save Venice from the flood?', '', 6, 'Straight Foreward'),
(51, 'How did Jack and Annie make Neptune appear?', '', 6, 'Straight Foreward'),
(52, 'What is the symbol of Venice, and what does it stand for?   ', '', 6, 'Straight Foreward'),
(53, 'Why do Jack and Annie think that water is involved with the disaster?', '', 6, 'Straight Foreward'),
(54, 'What was the name of Johns first band?  ', '', 7, 'Straight Foreward'),
(55, 'What was the last movie made by the Beatles? ', '', 7, 'Straight Foreward'),
(56, 'When and where was the last concert performed by the Beatles?  ', '', 7, 'Straight Foreward'),
(57, 'What was the first Beatles song released in England?   ', '', 7, 'Straight Foreward'),
(58, 'What record does the song (Yesterday) hold in the Guinness Book of World Records?  ', '', 7, 'Straight Foreward'),
(59, 'How and when did George Harrison die?   ', '', 7, 'Straight Foreward'),
(60, 'Who was NOT a drummer for the Quarrymen?  ', '', 7, 'Straight Foreward'),
(61, 'Who was the Beatles first manager in Liverpool?   ', '', 7, 'Straight Foreward'),
(62, 'What was the last album made by the Beatles? ', '', 7, 'Straight Foreward'),
(63, 'Whose idea was it to change the band''s name from the Quarrymen to the Beatles? ', '', 7, 'Straight Foreward'),
(64, 'What game do wizards play high in the air on broomsticks? ', '', 8, 'Straight Foreward'),
(65, 'What must happen to end a Quidditch match?', '', 8, 'Straight Foreward'),
(66, 'What was the pass word to get into bed that Neveille forgot? ', '', 8, 'Straight Foreward'),
(67, 'Who was the wizard who went bad?', '', 8, 'Straight Foreward'),
(68, 'What did the twins have follow Quirrell around at Christmas?', '', 8, 'Straight Foreward'),
(69, 'How much did Harry pay Mr. Ollivander for his wand?', '', 8, 'Straight Foreward'),
(70, 'Where did Harry Potter live during the first ten years of his life?', '', 8, 'Straight Foreward'),
(71, 'Who comes to rescue Harry on a flying motorbike?', '', 8, 'Straight Foreward'),
(72, 'What is the shape of the mark on Harry Potter''s forehead?', '', 8, 'Straight Foreward'),
(73, 'How does Harry describe his cousin Dudley?', '', 8, 'Straight Foreward'),
(74, ' What do wizards call non-magical people?', '', 9, 'Straight Foreward'),
(75, 'What important news does Harry receive by letter?', '', 9, 'Straight Foreward'),
(76, 'Into which house at Hogwarts is Harry sorted?', '', 9, 'Straight Foreward'),
(77, ' What is the name of Harry''s pet?', '', 9, 'Straight Foreward'),
(78, 'Who must Harry defeat to save the Sorcerer''s Stone?  ', '', 9, 'Straight Foreward'),
(79, 'Where is Gringotts located? ', '', 9, 'Straight Foreward'),
(80, 'Who are Harry''s parents?', '', 9, 'Straight Foreward'),
(81, 'According to Hermione, what is the three-headed dog guarding?', '', 9, 'Straight Foreward'),
(82, 'On the train, what did Neville say he lost?', '', 9, 'Straight Foreward'),
(83, 'Who helps Harry protect the Sorcerer''s Stone?', '', 9, 'Straight Foreward'),
(84, 'Where is Arlington National Cemetery', 'Arlington_National_Cemetery_q1.JPG', 10, 'direct'),
(85, 'Which day is the busiest day in Arlighton National Cemetery?', 'Arlington_National_Cemetery_q2.JPG', 10, 'direct'),
(86, 'Which year Memorial Day was declared as national holiday?', 'Arlington_National_Cemetery_q3.JPG', 10, 'direct'),
(87, 'How is the Cemetery today?', 'Arlington_National_Cemetery_q4.JPG', 10, 'conclusion'),
(88, 'who got this title Unknown but Not Forgetten?', 'Arlington_National_Cemetery_q5.JPG', 10, 'direct');

-- --------------------------------------------------------

--
-- Table structure for table `questiontype`
--

DROP TABLE IF EXISTS `questiontype`;
CREATE TABLE IF NOT EXISTS `questiontype` (
  `questionType` varchar(50) NOT NULL,
  `description` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`questionType`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `quizanswer`
--

DROP TABLE IF EXISTS `quizanswer`;
CREATE TABLE IF NOT EXISTS `quizanswer` (
  `StudentQuizId` int(11) NOT NULL,
  `AnswerId` int(11) NOT NULL,
  `Duration` int(11) NOT NULL,
  `QId` int(11) NOT NULL,
  PRIMARY KEY (`StudentQuizId`,`AnswerId`,`QId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `quizanswer`
--

INSERT INTO `quizanswer` (`StudentQuizId`, `AnswerId`, `Duration`, `QId`) VALUES
(1, 108, 2556, 31),
(1, 114, 106, 32),
(1, 119, 94, 33),
(1, 121, 97, 34),
(1, 125, 133, 35),
(1, 131, 102, 36),
(1, 132, 159, 37),
(1, 139, 145, 38),
(1, 143, 107, 39),
(1, 147, 170, 40),
(3, 3, 741, 1),
(3, 5, 282, 2),
(3, 11, 227, 3),
(3, 14, 193, 4),
(3, 19, 462, 5),
(3, 24, 419, 6),
(3, 26, 277, 7),
(3, 29, 200, 8),
(3, 34, 335, 9),
(3, 37, 361, 10),
(4, 84, 257, 25),
(4, 91, 89, 26),
(4, 94, 89, 27),
(4, 97, 9401, 28),
(4, 101, 125, 29),
(5, 242, 207, 64),
(5, 247, 102, 65),
(5, 251, 93, 66),
(5, 254, 106, 67),
(5, 257, 104, 68),
(5, 261, 128, 69),
(5, 266, 103, 70),
(5, 269, 111, 71),
(5, 272, 109, 72),
(5, 279, 114, 73),
(6, 84, 191, 25),
(6, 88, 142, 26),
(6, 92, 170, 27),
(6, 97, 350, 28),
(6, 101, 126, 29),
(7, 281, 599, 74),
(7, 287, 105, 75),
(7, 290, 121, 76),
(7, 294, 132, 77),
(7, 298, 132, 78),
(7, 302, 100, 79),
(7, 306, 126, 80),
(7, 309, 97, 81),
(7, 313, 104, 82),
(7, 317, 139, 83),
(8, 84, 321, 25),
(8, 91, 285, 26),
(8, 93, 155, 27),
(8, 97, 215, 28),
(8, 101, 187, 29),
(11, 2, 3685, 1),
(11, 6, 858, 2),
(11, 11, 141, 3),
(11, 15, 97, 4),
(11, 18, 115, 5),
(11, 22, 143, 6),
(11, 28, 118, 7),
(11, 30, 127, 8),
(11, 31, 102, 9),
(11, 38, 118, 10),
(12, 65, 263, 20),
(12, 71, 110, 21),
(12, 74, 97, 22),
(12, 78, 170, 23),
(12, 81, 180, 24),
(13, 201, 336, 54),
(13, 206, 154, 55),
(13, 210, 161, 56),
(13, 214, 161, 57),
(13, 219, 103, 58),
(13, 221, 141, 59),
(13, 225, 188, 60),
(13, 229, 190, 61),
(13, 235, 127, 62),
(13, 237, 122, 63),
(14, 85, 375, 25),
(14, 91, 269, 26),
(14, 94, 371, 27),
(14, 96, 240, 28),
(14, 101, 385, 29),
(15, 66, 579, 20),
(15, 70, 484, 21),
(15, 74, 370, 22),
(15, 77, 426, 23),
(15, 82, 499, 24),
(16, 320, 314, 84),
(16, 326, 205, 85),
(16, 331, 280, 86),
(16, 334, 256, 87),
(16, 337, 288, 88),
(17, 321, 217, 84),
(17, 326, 235, 85),
(17, 330, 96, 86),
(17, 333, 128, 87),
(17, 339, 152, 88),
(18, 85, 442, 25),
(18, 91, 159, 26),
(18, 94, 426, 27),
(18, 96, 172, 28),
(18, 101, 281, 29),
(19, 66, 218, 20),
(19, 69, 109, 21),
(19, 74, 100, 22),
(19, 79, 79, 23),
(19, 82, 95, 24),
(20, 240, 329, 64),
(20, 244, 558, 65),
(20, 248, 17574, 66),
(20, 255, 268, 67),
(20, 256, 8472, 68),
(20, 263, 1762, 69),
(20, 266, 482, 70),
(20, 269, 786, 71),
(20, 274, 1168, 72),
(20, 277, 804, 73),
(21, 201, 2477, 54),
(21, 204, 495, 55),
(21, 208, 923, 56),
(21, 212, 571, 57),
(21, 216, 1139, 58),
(21, 221, 484, 59),
(21, 224, 444, 60),
(21, 231, 492, 61),
(21, 235, 1454, 62),
(21, 236, 838, 63),
(22, 240, 268, 64),
(22, 244, 313, 65),
(22, 248, 227, 66),
(22, 255, 218, 67),
(22, 257, 136, 68),
(22, 260, 294, 69),
(22, 266, 150, 70),
(22, 269, 198, 71),
(22, 273, 219, 72),
(22, 278, 670, 73),
(24, 320, 241, 84),
(24, 325, 96, 85),
(24, 330, 88, 86),
(24, 334, 104, 87),
(24, 339, 77, 88),
(26, 65, 427, 20),
(26, 69, 216, 21),
(26, 74, 102, 22),
(26, 78, 108, 23),
(26, 82, 107, 24),
(27, 149, 227, 41),
(27, 157, 100, 43),
(27, 163, 101, 44),
(27, 166, 87, 45),
(27, 171, 70, 46),
(27, 172, 105, 47),
(27, 179, 102, 48),
(27, 183, 103, 49),
(27, 187, 100, 50),
(27, 190, 112, 51),
(27, 192, 111, 52),
(27, 196, 307, 53),
(28, 202, 514, 54),
(28, 204, 311, 55),
(28, 210, 202, 56),
(28, 212, 294, 57),
(28, 219, 458, 58),
(28, 222, 374, 59),
(28, 226, 548, 60),
(28, 229, 365, 61),
(28, 232, 283, 62),
(28, 236, 274, 63),
(29, 64, 722, 20),
(29, 68, 212, 21),
(29, 72, 179, 22),
(29, 76, 176, 23),
(29, 82, 312, 24),
(30, 240, 991, 64),
(30, 244, 292, 65),
(30, 248, 258, 66),
(30, 255, 416, 67),
(30, 256, 505, 68),
(30, 260, 415, 69),
(30, 264, 394, 70),
(30, 269, 273, 71),
(30, 274, 326, 72),
(30, 277, 367, 73),
(31, 320, 388, 84),
(31, 327, 414, 85),
(31, 328, 196, 86),
(31, 332, 294, 87),
(31, 339, 763, 88),
(32, 84, 388, 25),
(32, 90, 162, 26),
(32, 94, 241, 27),
(32, 96, 218, 28),
(32, 101, 385, 29),
(33, 200, 289, 54),
(33, 205, 238, 55),
(33, 208, 235, 56),
(33, 212, 198, 57),
(33, 217, 243, 58),
(33, 220, 467, 59),
(33, 224, 164, 60),
(33, 230, 225, 61),
(33, 235, 101, 62),
(33, 239, 104, 63);

-- --------------------------------------------------------

--
-- Table structure for table `reflection`
--

DROP TABLE IF EXISTS `reflection`;
CREATE TABLE IF NOT EXISTS `reflection` (
  `ReflectionId` int(11) NOT NULL AUTO_INCREMENT,
  `ReflectionText` varchar(145) DEFAULT NULL,
  PRIMARY KEY (`ReflectionId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `reflection`
--

INSERT INTO `reflection` (`ReflectionId`, `ReflectionText`) VALUES
(1, 'I do not remember that part of the story'),
(2, 'I do not know the meaning of some word(s) in the question'),
(3, 'There were 2 correct answers and I had to choose one of them'),
(4, 'I answered quickly'),
(5, 'I read the book too quickly');

-- --------------------------------------------------------

--
-- Table structure for table `school`
--

DROP TABLE IF EXISTS `school`;
CREATE TABLE IF NOT EXISTS `school` (
  `SchoolId` int(11) NOT NULL AUTO_INCREMENT,
  `SchoolName` varchar(45) NOT NULL,
  `SchoolLocation` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`SchoolId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `school`
--

INSERT INTO `school` (`SchoolId`, `SchoolName`, `SchoolLocation`) VALUES
(1, 'Hermosa Elementary School', 'Rancho Cucamonga, CA'),
(2, 'Hermitage Elementary', 'Virginia Beach, VA'),
(3, 'Stork Elementary', 'San Dimas, CA'),
(4, 'test', 'testlocation');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

DROP TABLE IF EXISTS `student`;
CREATE TABLE IF NOT EXISTS `student` (
  `StudentId` int(11) NOT NULL AUTO_INCREMENT,
  `StudentFirstName` varchar(100) NOT NULL,
  `StudentLastName` varchar(45) DEFAULT NULL,
  `Gender` char(1) NOT NULL,
  `DateOfBirth` date NOT NULL,
  `studentLoginId` varchar(45) NOT NULL,
  PRIMARY KEY (`StudentId`),
  UNIQUE KEY `studentLoginId_UNIQUE` (`studentLoginId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=34 ;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`StudentId`, `StudentFirstName`, `StudentLastName`, `Gender`, `DateOfBirth`, `studentLoginId`) VALUES
(1, 'Gabriella', 'Gray', 'F', '2009-08-11', 'GGray2027'),
(2, 'Reese', 'Fisher', 'F', '2009-06-13', 'RFisher2027'),
(3, 'Yousef', 'Zalnes', 'M', '2009-01-05', 'YZalnes2027'),
(4, 'Cayden', 'Van Horn', 'M', '2006-03-15', 'CVanHorn2024'),
(5, 'Emma', 'Gray', 'F', '2006-08-22', 'EGray2024'),
(6, 'Brayden', 'Haddad', 'F', '2006-05-29', 'BHaddad2024'),
(7, 'Traci', 'Fairchild', 'F', '2006-05-02', 'trafai'),
(23, 'Fouad', 'Haddad', 'M', '2006-09-12', 'FHaddad2024'),
(26, 'Gabby', 'Fairchild', 'F', '1998-11-30', 'GFairchild2016'),
(27, 'Rachel', 'Fairchild', 'F', '1992-09-02', 'RFairchild2010'),
(28, 'Ann', 'Fairchild', 'F', '0000-00-00', 'AFairchild18'),
(29, 'Student1', 'Wayfair', 'F', '2009-04-01', 'SWayfair2027'),
(30, 'Sample', 'Student', 'F', '2001-04-01', 'SStudent2019'),
(32, 'Sample', 'Smith', 'F', '2004-05-01', 'SSmith2022');

-- --------------------------------------------------------

--
-- Table structure for table `studentclass`
--

DROP TABLE IF EXISTS `studentclass`;
CREATE TABLE IF NOT EXISTS `studentclass` (
  `SCId` int(11) NOT NULL AUTO_INCREMENT,
  `StudentId` int(11) NOT NULL,
  `ClassId` int(11) NOT NULL,
  `SchoolYear_notneeded` varchar(10) DEFAULT NULL,
  `ReadingLevel` varchar(5) DEFAULT NULL,
  `CorrectnessLevel` varchar(10) DEFAULT NULL,
  `StudentLoginId_notneeded` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`SCId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=24 ;

--
-- Dumping data for table `studentclass`
--

INSERT INTO `studentclass` (`SCId`, `StudentId`, `ClassId`, `SchoolYear_notneeded`, `ReadingLevel`, `CorrectnessLevel`, `StudentLoginId_notneeded`) VALUES
(1, 1, 2, '2016-2017', '2.42', '46.67', 'GGray2027'),
(2, 2, 2, '2016-2017', '3.2', '100', 'RFisher2027'),
(3, 3, 2, '2016-2017', '1.5', '100', 'YZalnes2027'),
(4, 4, 1, '2016-2017', '5.8', '100', 'CVanHorn2024'),
(5, 5, 1, '2016-2017', '1.17', '21.54', 'EGray2024'),
(6, 6, 1, '2016-2017', '4.8', '100', 'BHaddad2024'),
(7, 7, 1, '2016-2017', '1.29', '17.54', 'trafai'),
(11, 23, 1, NULL, '0', NULL, NULL),
(14, 26, 1, NULL, '0', NULL, NULL),
(15, 27, 4, NULL, '0', NULL, NULL),
(18, 28, 1, NULL, '2.04', '60.00', NULL),
(19, 29, 5, NULL, '1.95', '50.00', NULL),
(20, 30, 1, NULL, '0', NULL, NULL),
(22, 32, 1, NULL, '1.28', '35.00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `studentquiz`
--

DROP TABLE IF EXISTS `studentquiz`;
CREATE TABLE IF NOT EXISTS `studentquiz` (
  `SCId` int(11) NOT NULL,
  `EarnedPoints` varchar(5) DEFAULT NULL,
  `DateOfQuiz` date NOT NULL,
  `TimeOfQuiz` time NOT NULL,
  `PlaceQuizTaken` varchar(45) DEFAULT NULL,
  `StudentQuizId` int(11) NOT NULL AUTO_INCREMENT,
  `BookQuizId` int(11) NOT NULL,
  `Passed` tinyint(4) DEFAULT NULL,
  `numCorrectQuestions` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`StudentQuizId`),
  KEY `BookQuizId_idx` (`BookQuizId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=34 ;

--
-- Dumping data for table `studentquiz`
--

INSERT INTO `studentquiz` (`SCId`, `EarnedPoints`, `DateOfQuiz`, `TimeOfQuiz`, `PlaceQuizTaken`, `StudentQuizId`, `BookQuizId`, `Passed`, `numCorrectQuestions`) VALUES
(5, '0.40', '2017-04-04', '22:06:29', NULL, 1, 5, 0, '2'),
(5, '3.00', '2017-04-05', '20:38:35', NULL, 3, 1, 0, '3'),
(5, '0.30', '2017-04-06', '20:25:17', NULL, 4, 4, 1, '3'),
(5, '2.40', '2017-04-06', '20:34:36', NULL, 5, 8, 0, '2'),
(1, '0.10', '2017-04-08', '16:20:59', NULL, 6, 4, 0, '1'),
(5, '2.40', '2017-04-08', '20:02:34', NULL, 7, 9, 0, '2'),
(7, NULL, '2017-04-09', '16:29:52', NULL, 8, 4, NULL, NULL),
(1, '0', '2017-04-23', '11:59:43', NULL, 11, 1, 0, '3'),
(1, '0', '2017-04-23', '12:34:36', NULL, 12, 3, 0, '1'),
(1, '0', '2017-04-23', '13:15:31', NULL, 13, 7, 0, '3'),
(18, '0.40', '2017-04-23', '13:29:11', NULL, 14, 4, 1, '4'),
(18, '0', '2017-04-23', '13:30:12', NULL, 15, 3, 0, '2'),
(1, '0.50', '2017-04-27', '19:22:26', NULL, 16, 10, 1, '5'),
(5, '0', '2017-04-27', '19:25:29', NULL, 17, 10, 0, '1'),
(19, '0.50', '2017-04-28', '11:01:38', NULL, 18, 4, 1, '5'),
(19, '0', '2017-04-28', '11:27:38', NULL, 19, 3, 0, '0'),
(19, '12.00', '2017-04-28', '11:37:19', NULL, 20, 8, 1, '10'),
(19, '0', '2017-04-28', '11:48:40', NULL, 21, 7, 0, '0'),
(7, '7.20', '2017-04-28', '13:26:00', NULL, 22, 8, 1, '6'),
(7, NULL, '2017-04-28', '14:36:31', NULL, 23, 1, NULL, NULL),
(7, '0', '2017-04-28', '14:36:43', NULL, 24, 10, 0, '2'),
(5, NULL, '2017-04-29', '07:57:53', NULL, 25, 7, NULL, NULL),
(5, '0', '2017-04-29', '07:59:23', NULL, 26, 3, 0, '1'),
(7, '0', '2017-04-29', '08:44:14', NULL, 27, 6, 0, '0'),
(7, '0', '2017-04-29', '08:45:56', NULL, 28, 7, 0, '2'),
(7, '0', '2017-04-29', '08:47:29', NULL, 29, 3, 0, '0'),
(1, '9.60', '2017-04-29', '10:15:41', NULL, 30, 8, 1, '8'),
(22, '0', '2017-04-29', '10:32:00', NULL, 31, 10, 0, '1'),
(22, '0', '2017-04-29', '10:33:47', NULL, 32, 4, 0, '2'),
(22, '0', '2017-04-29', '10:35:06', NULL, 33, 7, 0, '4');

-- --------------------------------------------------------

--
-- Table structure for table `studentreflection`
--

DROP TABLE IF EXISTS `studentreflection`;
CREATE TABLE IF NOT EXISTS `studentreflection` (
  `StudentQuizId` int(11) NOT NULL,
  `QId` int(11) NOT NULL,
  `ReflectionId` int(11) NOT NULL,
  PRIMARY KEY (`ReflectionId`,`QId`,`StudentQuizId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `studentreflection`
--

INSERT INTO `studentreflection` (`StudentQuizId`, `QId`, `ReflectionId`) VALUES
(11, 3, 0),
(8, 26, 0),
(1, 40, 0),
(5, 67, 0),
(31, 88, 0),
(11, 6, 1),
(4, 26, 1),
(32, 26, 1),
(16, 29, 1),
(1, 32, 1),
(5, 65, 1),
(5, 66, 1),
(22, 70, 1),
(30, 71, 1),
(32, 27, 2),
(4, 29, 2),
(13, 55, 2),
(13, 61, 2),
(24, 86, 2),
(24, 87, 2),
(24, 88, 2),
(11, 8, 3),
(12, 23, 3),
(11, 24, 3),
(1, 34, 3),
(1, 38, 3),
(17, 87, 3),
(3, 2, 4),
(7, 8, 4),
(11, 22, 4),
(12, 24, 4),
(13, 56, 4),
(17, 85, 4),
(31, 86, 4),
(12, 22, 5),
(30, 70, 5);

-- --------------------------------------------------------

--
-- Table structure for table `teacher`
--

DROP TABLE IF EXISTS `teacher`;
CREATE TABLE IF NOT EXISTS `teacher` (
  `TeacherId` int(11) NOT NULL AUTO_INCREMENT,
  `TeacherFirstName` varchar(100) NOT NULL,
  `TeacherLastName` varchar(45) DEFAULT NULL,
  `Gender` char(1) NOT NULL,
  `DateOfBirth` date NOT NULL,
  `TeacherLoginId` varchar(45) NOT NULL,
  PRIMARY KEY (`TeacherId`),
  UNIQUE KEY `TeacherLoginId_UNIQUE` (`TeacherLoginId`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `teacher`
--

INSERT INTO `teacher` (`TeacherId`, `TeacherFirstName`, `TeacherLastName`, `Gender`, `DateOfBirth`, `TeacherLoginId`) VALUES
(1, 'Emily', 'Steely', 'F', '1966-04-14', 'ESteely2028'),
(2, 'Karen', 'Hale', 'F', '1955-04-14', 'KHale2028'),
(5, 'Christine', 'Frie', 'F', '1950-02-01', 'CFrie1968'),
(6, 'Traci', 'Fairchild', 'F', '1964-05-02', 'TFairchild1982');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `class`
--
ALTER TABLE `class`
  ADD CONSTRAINT `schoolId` FOREIGN KEY (`SchoolId`) REFERENCES `school` (`SchoolId`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
