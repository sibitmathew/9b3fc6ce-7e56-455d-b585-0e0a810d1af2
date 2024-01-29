Assesment Reporting System
===================

Description
===========

It is a CLI app to generate perforamace of students



Installation Instructions
=========================


System requirements
===================

1. php 8+ (Developed on php 8.3 with MacOs 13.6)

2. composer (version 1.10.10 on mac )

   There shouldn't be any problem with other unix based operating systems.
   

Installation
============

1. Clone the repository

2. cd to the folder `cd 9b3fc6ce-7e56-455d-b585-0e0a810d1af2`

3. Install composer if not already installed from [this link](https://getcomposer.org/)

4. Run `composer install`



Starting the app
================

1. Open a new Terminal and go to  `9b3fc6ce-7e56-455d-b585-0e0a810d1af2` folder.

2. Run `php start`.

3. It will prompt to the required details

4. Enter the student_id and choose options

5. This will validate, and generate reports

6. To stop run `stop`.


Database
========

1. Data stored in the memory as json as required


To run tests
============

For tests using `phpunit7`

1. Open a terminal and go to cash_process folder by `cd cash_process`

2. Run `vendor/bin/phpunit tests`

Note : Please make sure `cron` and `consumer` running on other terminal windows.


To run tests with testdox
=========================

For tests using `phpunit7`

1. Open a terminal and go to cash_process folder by `cd cash_process`

2. Run `vendor/bin/phpunit --testdox tests`

Note : Please make sure `cron` and `consumer` running on other terminal windows.

Other notes
===========

Added few test files for the file input `tests/data` folder.
A Test database also included in `tests/data/db` folder.
All the source code is included in src folder.
All the test code are included on tests folder.

Design considerations and standards
===================================

1. Object oriented approach for the design.

2. Factory pattern.

3. SOLID Principles.

4. PSR-4 

5. Tried to decouple the module to make sure each component perform independently.


Structure
=========

The complete modules is divided into following parts:

1. Processors : Process the request.

3. Services : Performs services for Processors


Assumptions
===========

1. It is a CLI app as the instructions clearly says not to use GUI.



Developer contact details
=========================

Email : sibimathewtkv@gmail.com







