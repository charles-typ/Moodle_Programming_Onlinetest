# Moodle_Programming_Onlinetest
## Motivation
  >Having only paper-test for students who take programming courses isn't a fair way to evaluate their studies.The Moodle system with an OJ is widely used in evaluating students' programming homework.This repository aims to build an online testing system based on Moodle using simply data-driven method.
## Basic idea
  >The basic idea is that, in the ordinary Moodle system, we have courses and course categories, and under each course we have several questions for students to answer.Under this method, a lot of students share the same course and give answers to the same questions.In my method, i changed the course category into course and change the courses into students, therefore, under each students there are a number of problems which are their online test problems.All you have to do is to mimicry the way Moodle add courses, section, modules, context and add them automatically,which is what i called data-driven.
## Build the system
  >To build this system, simply add the files in the moodle resource path and use the calling methods to call the php pages.<br />
  There are basically three modules among the files.First, upload the students' information and initialize the course.Then students finish the test and the system calculates their score and give feedback to teachers.Finally, the teacher finish the course and delete the information in the Moodle database.
## Warnings
  Here are some warnings if you want to use it:<br />
* Some parts of the system are written in Chinese(cause we actually use it in our school).<br />
* In the main php files there are lots of useless things just for debug, so in practice, please delete them.<br />
* I've done nothing with the interface, so you might want to decorate it before using.<br />
* Be sure to screen a lot of function of Moodle because you are writing a system for a test and you don't want anyone to cheat.For example, you need to erase the function which a student can see all the courses(which is  the "student" rather than "courses" in our system), because you definitely don't want a student to answer others' test problems.<br />
## Contact
>If you are really interested in the system or you want to really use it, or maybe you want to ask something about the Moodle system's architecture, please contact me at charles.typ.xjtu@google.com or charlestyp@stu.xjtu.edu.cn
