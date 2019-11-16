## <span class="mw-headline" id="Functional_Requirements">Functional Requirements</span>

The students in Merlion University use BIOS (BIdding Online System) to enroll for their courses. They select the courses they wish to enroll and bid for it using virtual dollars (e$). A course can have multiple sections and a section is taught by an instructor. Also, An instructor can teach one or more sections.

BIOS are used by students as well as administrators. For students the following functions are provided:

1.  Login
    *   A student will log in with his/her email ID and password.
    *   Upon success, the student should be able to see the balance e$ along with a welcome message.
    *   Upon failure, the app outputs a proper error message and requests the user to login again.
2.  Bid for a section
    *   A Student can place a bid by entering a course code, section number, and e$ amount for bidding.
    *   The bidding will be possible only during active bidding rounds.
        *   For the bidding round 1, the student can bid for the course that are offered by his/her own school.
        *   For the bidding round 2, the student can bid for any courses.
    *   The bid will be successful when all the following criteria is satisfied.
        *   A Student can bid for any section as long as they have enough e$, the class and exam timetables do not clash, and s/he has fulfilled the necessary pre-requisite courses.
        *   A student can bid at most for 5 sections.
        *   A student can only bid for one section per course.
    *   When the bid is successful, the app outputs a proper success message along with the balance e$.
    *   Otherwise, the app shows a proper error message. You can also design your application UI not to even allow impossible bids.
3.  Drop a bid
    *   A Student can drop a bid by specifying a course id and section number.
        *   When the input is valid, the app will cancel the bid and return back the full e$ credit. The updated e$ balance should be shown to the user.
        *   Droppig a bid can be done only during active bidding rounds.
4.  Drop a section
    *   After a successful bid, a student can drop a section by specifying the course id and section number.
    *   When drop is successful, and the student will get back the full e$ credit, and the app will show the e$ balance.
    *   Once you drop a successful bid, you will have to rebid for the section.
    *   Dropping of section is done when a round is active. (in other words, success bids from round 2 are final and cannot be dropped).
5.  View bidding results
    *   Show a table to show the bidding status -- the table should include the course id, section number, bid amount, status of bidding. The status will be one of the following: Pending, Success, Fail.

For administrator, the following functions are provided:

1.  Login
    *   An administrator will log in with the username "admin" and password. The admin will login from the same login page as student users.
        *   Please pick a secure password for the administrator. (**NEW**)
2.  Bootstrap
    *   The administrator can bootstrap the BIOS system with the given data.
    *   The bidding round 1 will start automatically upon the completion of the bootstrap.
3.  Starting and clearing rounds
    *   The bidding round will be manually started and cleared by the administrator (except that the round 1 starts automatically after bootstrap).
    *   Round 1
        *   When the round 1 is closed, students will be able to see the bidding results.
        *   The first round of bidding is for students to bid for courses offered by their school.
        *   The round 1 clearing logic is described [below](/is212/index.php?title=Project-2016&action=edit&redlink=1 "Project-2016 (page does not exist)") in more detail.
    *   Round 2
        *   The second round of bidding is for students to bid for courses offered by their school and courses offered by other schools.
        *   The round 2 clearing logic is described [below](/is212/index.php?title=Project-2016&action=edit&redlink=1 "Project-2016 (page does not exist)") in more detail.

For all functions, your team will need to provide the dual interfaces (Web UI and Web Services) as follows:

1.  Web UI: Provide a user-friendly and easy-to-use web UI for all features that your team needs to implement. The basic UI requirement is showing results in nicely formatted tables, text, etc. Project will be tested using the latest version of Chrome with a display resolution of 1920 x 1080 (1080p FullHD).
    *   Since the resolution is quite high, we expect your team to use appropriately sized fonts (we don't like squinting) and minimise the use of scrollbars.
    *   **Please do not output json messages for Web UI. Json message format is not designed for human users !!**
2.  Web Services: Provide JSON APIs that allow all functionalities to be queried programmatically by other machines. Refer to a separate section later for the full JSON API requirements.

Note: Even though we did not explicitly ask for a way to show a student's current timetable / bid status etc., these are all features that are very good to have. In addition, another very good to have feature is a good way to search for classes, etc.

## <span class="mw-headline" id="Details_on_Bootstrap">Details on Bootstrap</span>

The bootstrapping functionality in this project involves clearing all existing data in the database and replacing them with values supplied from a bootstrap data file. The bootstrap data file will contain the following CSV files, and you should process them in the following order:

1.  student.csv
2.  course.csv
3.  section.csv
4.  prerequisite.csv
5.  course_completed.csv
6.  bid.csv

Once the function is done, the system will show the list of errors (with filename, row number, and error message), as well as the number of records successfully loaded from each file. If there are multiple errors per row, all errors must be reported, in the specified order. See the "Sample Data" section for a sample dataset.

For each of the files, process them in order from the first row to the last. The header row is considered row 1, and the first data row is row 2\. You may assume that the above six files will all be present, the header row is correct with all the necessary fields, and each row has the same number of fields as the header row.

Upon processing, each row is run through the common validations. If there are no errors, then it is run through the file-specific validations. If there are still no errors, then the data is added to the database. Errors are reported with the filename, row number, and error message(s). If a single row has multiple errors, output all the errors in the order specified.

Additionally, if there is any whitespace at the start or end of each field, it must be removed before starting the common validations. For example if the email is “apple.hsu “ (one or more whitespace characters at the end), the whitespace must be removed before storing it into the database.

All bids made through the bootstrap files will be done with the necessary processing logic and e$ deduction for each line. Note that a student cannot place bids for multiple sections in the same course, thus if a student has bidded for the same course in a previous line, update the bid (ie cancel the first bid and place the new bid).

#### <span class="mw-headline" id="Common_Validations_for_all_data_files">Common Validations for all data files</span>

For all the fields, you need to check if the field is blank. An error message "blank [field]" is produced for each blank field, and output in the left-to-right order of the CSV fields. A row is discarded, with no need to do further file-specific validation listed below, if any of the fields is blank.

#### <span class="mw-headline" id="File-specific_Validations">File-specific Validations</span>

If one or more fields fail the file-specific validations, the row is discarded, but all the errors are returned. Errors are output in the order they are listed in the tables below.

#### <span class="mw-headline" id="course.csv">course.csv</span>

This file provides detailed information of individual courses.

<table class="wikitable">

<tbody>

<tr>

<th>Field</th>

<th>Description</th>

</tr>

<tr>

<td>course</td>

<td>Course Code</td>

</tr>

<tr>

<td>school</td>

<td>The school that offers this course</td>

</tr>

<tr>

<td>title</td>

<td>Title of the course</td>

</tr>

<tr>

<td>description</td>

<td>Describes briefly what the course is about</td>

</tr>

<tr>

<td>exam date</td>

<td>The exam day for the course. Students are not allowed to take different courses where the exam date(time included) overlaps.</td>

</tr>

<tr>

<td>exam start</td>

<td>The start time of the exam</td>

</tr>

<tr>

<td>exam end</td>

<td>The end time of the exam</td>

</tr>

</tbody>

</table>

For all date fields, do the following validations:

<table class="wikitable">

<tbody>

<tr>

<td>"invalid exam date"</td>

<td>the field must be in the format Ymd ([https://www.php.net/manual/en/function.date.php](https://www.php.net/manual/en/function.date.php))</td>

</tr>

<tr>

<td>"invalid exam start"</td>

<td>the field must be in the format H:mm (8:30, 12:00, 15:30)</td>

</tr>

<tr>

<td>"invalid exam end"</td>

<td>the field must be in the format H:mm and the end time should be later than the start time. (11:45, 15:15, 18:45)</td>

</tr>

<tr>

<td>"invalid title"</td>

<td>the title field must not exceed 100 characters.</td>

</tr>

<tr>

<td>"invalid description"</td>

<td>the description field must not exceed 1000 characters.</td>

</tr>

</tbody>

</table>

#### <span class="mw-headline" id="section.csv">section.csv</span>

This file provides details of the sections offered by the school.

<table class="wikitable">

<tbody>

<tr>

<th>Field</th>

<th>Description</th>

</tr>

<tr>

<td>course</td>

<td>Course Code</td>

</tr>

<tr>

<td>section</td>

<td>Each course has multiple sections (S1,S2 etc). The section number is a number less than 100.</td>

</tr>

<tr>

<td>day</td>

<td>The day of week</td>

</tr>

<tr>

<td>start</td>

<td>The start time of the class</td>

</tr>

<tr>

<td>end</td>

<td>The end time of the class</td>

</tr>

<tr>

<td>instructor</td>

<td>The instructor of the class</td>

</tr>

<tr>

<td>venue</td>

<td>The venue where the class is going to be conducted</td>

</tr>

<tr>

<td>size</td>

<td>The class size</td>

</tr>

</tbody>

</table>

List of validations for the section.csv:

<table class="wikitable">

<tbody>

<tr>

<td>"invalid course"</td>

<td>the course is not found in the course.csv</td>

</tr>

<tr>

<td>"invalid section"</td>

<td>The first character should be an S followed by a positive numeric number (1-99). Check only if course is valid.</td>

</tr>

<tr>

<td>"invalid day"</td>

<td>The day field should be a number between 1(inclusive) and 7 (inclusive). 1 - Monday, 2 - Tuesday, ... , 7 - Sunday.</td>

</tr>

<tr>

<td>"invalid start"</td>

<td>the field must be in the format H:mm (8:30, 12:00, 15:30)</td>

</tr>

<tr>

<td>"invalid end"</td>

<td>the field must be in the format H:mm and the end time should be later than the start time. (11:45, 15:15, 18:45)</td>

</tr>

<tr>

<td>"invalid instructor"</td>

<td>the instructor field must not exceed 100 characters.</td>

</tr>

<tr>

<td>"invalid venue"</td>

<td>the venue field must not exceed 100 characters.</td>

</tr>

<tr>

<td>"invalid size"</td>

<td>the field must be a positive numeric number.</td>

</tr>

</tbody>

</table>

#### <span class="mw-headline" id="student.csv">student.csv</span>

This file provides detail information of individual students.

<table class="wikitable">

<tbody>

<tr>

<th>Field</th>

<th>Description</th>

</tr>

<tr>

<td>userid</td>

<td>Userid of the student. This is used to login to the BIOS</td>

</tr>

<tr>

<td>password</td>

<td>Password of the student</td>

</tr>

<tr>

<td>name</td>

<td>Name of the student</td>

</tr>

<tr>

<td>school</td>

<td>The school that the student belongs to</td>

</tr>

<tr>

<td>edollar</td>

<td>The e-dollar amount for the student</td>

</tr>

</tbody>

</table>

List of validations for the student.csv:

<table class="wikitable">

<tbody>

<tr>

<td>"invalid userid"</td>

<td>the userid field must not exceed 128 characters.</td>

</tr>

<tr>

<td>"duplicate userid"</td>

<td>there is an existing user with the same userid.</td>

</tr>

<tr>

<td>"invalid e-dollar"</td>

<td>the e-dollar field must be a numeric value greater or equal to 0.0, and not more than 2 decimal places. Any other values will generate this error.</td>

</tr>

<tr>

<td>"invalid password"</td>

<td>the password field must not exceed 128 characters.</td>

</tr>

<tr>

<td>"invalid name"</td>

<td>the name field must not exceed 100 characters.</td>

</tr>

</tbody>

</table>

#### <span class="mw-headline" id="prerequisite.csv">prerequisite.csv</span>

This file provides details about the pre-requisite of the courses offered

<table class="wikitable">

<tbody>

<tr>

<th>Field</th>

<th>Description</th>

</tr>

<tr>

<td>course</td>

<td>Course Code</td>

</tr>

<tr>

<td>prerequisite</td>

<td>The pre-requisite course code</td>

</tr>

</tbody>

</table>

List of validations for prerequisite.csv:

<table class="wikitable">

<tbody>

<tr>

<td>"invalid course"</td>

<td>the course code is not found in the course.csv</td>

</tr>

<tr>

<td>"invalid prerequisite"</td>

<td>the course code is not found in the course.csv.</td>

</tr>

</tbody>

</table>

#### <span class="mw-headline" id="course_completed.csv">course_completed.csv</span>

Provides details about the courses that each student has completed

<table class="wikitable">

<tbody>

<tr>

<th>Field</th>

<th>Description</th>

</tr>

<tr>

<td>userid</td>

<td>The userid of the student</td>

</tr>

<tr>

<td>code</td>

<td>The course code</td>

</tr>

</tbody>

</table>

List of validations for the course_completed.csv. A row is discarded if any of the fields is invalid.

<table class="wikitable">

<tbody>

<tr>

<td>"invalid userid"</td>

<td>the userid is not found in student.csv</td>

</tr>

<tr>

<td>"invalid course"</td>

<td>the course code is not found in the course.csv.</td>

</tr>

</tbody>

</table>

After which, perform the following logic validation:

<table class="wikitable">

<tbody>

<tr>

<td>"invalid course completed"</td>

<td>the pre-requisite course has yet to be attempted.</td>

</tr>

</tbody>

</table>

#### <span class="mw-headline" id="bid.csv">bid.csv</span>

This file provides bidding information. It will add a bid for the student if this is the first attempt. Otherwise, if the student has bidded for the same course previously, this will update his existing bid via the same processing logic.

<table class="wikitable">

<tbody>

<tr>

<th>Field</th>

<th>Description</th>

</tr>

<tr>

<td>userid</td>

<td>The userid of the student</td>

</tr>

<tr>

<td>amount</td>

<td>The bid amount</td>

</tr>

<tr>

<td>code</td>

<td>The course code</td>

</tr>

<tr>

<td>section</td>

<td>The course section</td>

</tr>

</tbody>

</table>

List of validations for the bid.csv:

<table class="wikitable">

<tbody>

<tr>

<td>"invalid userid"</td>

<td>the userid is not found in student.csv</td>

</tr>

<tr>

<td>"invalid amount"</td>

<td>the amount must be a positive number >= e$10.00 and not more than 2 decimal places.</td>

</tr>

<tr>

<td>"invalid course"</td>

<td>the course code is not found in the course.csv</td>

</tr>

<tr>

<td>"invalid section"</td>

<td>the section code is not found in the section.csv (this is only tested for valid course code)</td>

</tr>

</tbody>

</table>

After which, perform the following list of logic validations:

<table class="wikitable">

<tbody>

<tr>

<td>"not own school course"</td>

<td>This only happens in round 1 where students are allowed to bid for modules from their own school.</td>

</tr>

<tr>

<td>"class timetable clash"</td>

<td>The class timeslot for the section clashes with that of a previously bidded section.</td>

</tr>

<tr>

<td>"exam timetable clash"</td>

<td>The exam timeslot for this section clashes with that of a previously bidded section.</td>

</tr>

<tr>

<td>"incomplete prerequisites"</td>

<td>student has not completed the prerequisites for this course.</td>

</tr>

<tr>

<td>"course completed"</td>

<td>student has already completed this course.</td>

</tr>

<tr>

<td>"section limit reached"</td>

<td>student has already bidded for 5 sections.</td>

</tr>

<tr>

<td>"not enough e-dollar"</td>

<td>student has not enough e-dollars to place the bid. If it is an update of a previous bid for the same course, account for the e$ gained back from the cancellation</td>

</tr>

</tbody>

</table>

## <span class="mw-headline" id="Details_on_the_Clearing_Logic">Details on the Clearing Logic</span>

### <span class="mw-headline" id="Round_1_Clearing_Logic">Round 1 Clearing Logic</span>

1.  Students will only be informed of their bid status after the end of the round.
2.  After the end of the round, sort the bids from the highest to lowest.
3.  Derive the minimum clearing price based on the number of vacancies, (i.e. if the class has 35 vacancies, the 35th highest bid is the clearing price.) There is a clearing price only if there are at least n or more bids for a particular section, where n is the number of vacancies.
4.  If there is only one bid at the clearing price, it will be successful. Otherwise, all bids at the clearing price will be dropped regardless of whether they can technically all be accommodated (refer to example).
5.  Students are charged based on the amount they bid.
6.  Student will be refunded e$ for unsuccessful bids.
7.  The minimum bid for round 1 is $10.

#### <span class="mw-headline" id="Example">Example</span>

Vacancies: 45

Total number of bids: 50

<table class="wikitable">

<tbody>

<tr>

<td>Ranking</td>

<td>Bid Price</td>

<td>State</td>

</tr>

<tr>

<td>1</td>

<td>70.00</td>

<td>Successful (44 vacancies left)</td>

</tr>

<tr>

<td>2</td>

<td>65.65</td>

<td>Successful</td>

</tr>

<tr>

<td>...</td>

<td>...</td>

<td>...</td>

</tr>

<tr>

<td>42</td>

<td>50.15</td>

<td>Successful</td>

</tr>

<tr>

<td>43</td>

<td>47.23</td>

<td>Successful (2 vacancies left after this point)</td>

</tr>

<tr>

<td>44</td>

<td>45.00</td>

<td>Dropped (only 2 vacancies left. 2 bids with the same clearing price. Both dropped according to logic)</td>

</tr>

<tr>

<td>45</td>

<td>45.00 (clearing price)</td>

<td>Dropped</td>

</tr>

<tr>

<td>46</td>

<td>44.00</td>

<td>Un-Successful (bid lower than clearing price)</td>

</tr>

<tr>

<td>47</td>

<td>44.00</td>

<td>Un-Successful (bid lower than clearing price)</td>

</tr>

<tr>

<td>48</td>

<td>44.05</td>

<td>Un-Successful (bid lower than clearing price)</td>

</tr>

<tr>

<td>49</td>

<td>40.11</td>

<td>Un-Successful (bid lower than clearing price)</td>

</tr>

<tr>

<td>50</td>

<td>35.14</td>

<td>Un-Successful (bid lower than clearing price)</td>

</tr>

</tbody>

</table>

Vacancies: 45

Num of remaining vacancies: 2

### <span class="mw-headline" id="Round_2_Clearing_Logic">Round 2 Clearing Logic</span>

1.  Students will be provided with real-time bid information.
2.  After every bid, the system sort the bids from the highest to the lowest and displays the following information:
    1.  Total Available Seats: Number of seats available for this section at the start of the round, after accounting for the number of seats successfully taken up during the first round. Number of seats could be updated depending if any students dropped a section that he/she bid for successfully during the first round.
    2.  Minimum bid value: This is the current minimum bid value that qualifies for a seat in the section. This value starts at 10 and never goes down.
        *   Students who attempt to bid below the minimum bid value should be validated against.
        *   Existing bids lower than the minimum bid value should be kept even when the minimum bid increases.
3.  The clearing price is the lowest successful bid that will get you a seat for this round for this section.
4.  After each bid, do the following processing to re-compute the minimum bid value:
    *   Case 1: If there are less than N bids for the section (where N is the total available seats), the Current Vacancies are (N - number of bids). The minimum bid value remains the same as there are still unfilled vacancies in this section that students can bid for using the minimum bid value.
    *   Case 2: If there are N or more bids for the section, the minimum bid value is 1 dollar more than the Nth bid. If there are other bids with the same bid price as the Nth bid and the class is unable to accommodate all of them, all bids with the same price will be unsuccessful.
        *   Note that "The price never goes down" condition precedes the case 2 condition. That is, in case that the current minimum bid value is higher than the (Nth bid value + 1), the minimum bid value stays the same.
5.  Your system should reflect the real-time status of the bids. I.e., if your bid is no longer successful (because other people have outbid you), this has to be reflected along with the new minimum bid price. This allows students to dynamically re-bid for the class using a higher e-dollar amount.
6.  The student's e$ balance will also be replenished with the e$ from the unsuccessful bid only after the round is cleared or the student drops the bid explicitly.
7.  At the end of the round, all successful bids will be confirmed.

#### <span id="Example_(Case_1):_More_Vacancies_than_Bids"></span><span class="mw-headline" id="Example_.28Case_1.29:_More_Vacancies_than_Bids">Example (Case 1): More Vacancies than Bids</span>

Total Available Seats: 5

Total number of bids: 3

<table class="wikitable">

<tbody>

<tr>

<td>Ranking</td>

<td>Bid Price</td>

<td>State</td>

</tr>

<tr>

<td>1</td>

<td>10.00</td>

<td>Successful</td>

</tr>

<tr>

<td>2</td>

<td>10.00</td>

<td>Successful</td>

</tr>

<tr>

<td>3</td>

<td>10.00</td>

<td>Successful</td>

</tr>

<tr>

<td>4</td>

<td></td>

<td></td>

</tr>

<tr>

<td>5</td>

<td></td>

<td></td>

</tr>

</tbody>

</table>

The minimum bid price is still $10 since total available seats is more than total number of bids.

#### <span id="Example_(Case_2):_More_Bids_than_Vacancies"></span><span class="mw-headline" id="Example_.28Case_2.29:_More_Bids_than_Vacancies">Example (Case 2): More Bids than Vacancies</span>

Total Available Seats: 3

Total number of bids: 5

<table class="wikitable">

<tbody>

<tr>

<th>Ranking</th>

<th>Bid Price</th>

<th>State</th>

</tr>

<tr>

<td>1</td>

<td>70.00</td>

<td>Successful</td>

</tr>

<tr>

<td>2</td>

<td>64.00</td>

<td>Successful</td>

</tr>

<tr>

<td>3</td>

<td>63.00</td>

<td>Successful</td>

</tr>

<tr>

<td>4</td>

<td>56.00</td>

<td>Unsuccessful. Bid too low.</td>

</tr>

</tbody>

</table>

Assume that this is what the student sees when they are trying to bid for this course. The minimum bid value is $64 (1 dollar more than the lowest current successful bid price).

After the student bids with $64, the new minimum bid amount will be $65\. There are now two bids with this value (bid 2 in the table above and the bid that was just made). Since both can be accommodated with the present number of vacancies, both bids will be successful.

<table class="wikitable">

<tbody>

<tr>

<th>Ranking</th>

<th>Bid Price</th>

<th>State</th>

</tr>

<tr>

<td>1</td>

<td>70.00</td>

<td>Successful</td>

</tr>

<tr>

<td>2</td>

<td>64.00</td>

<td>Successful</td>

</tr>

<tr>

<td>3</td>

<td>64.00</td>

<td>Successful</td>

</tr>

<tr>

<td>4</td>

<td>63.00</td>

<td>Unsuccessful. Bid too low.</td>

</tr>

<tr>

<td>5</td>

<td>56.00</td>

<td>Unsuccessful. Bid too low.</td>

</tr>

</tbody>

</table>

Now assume that the student decides to bid $70 for the class. There are 2 tied bids at the 3rd and 4th ranking. Displace both the bids and set the minimum bid price to $65\.

<table class="wikitable">

<tbody>

<tr>

<th>Ranking</th>

<th>Bid Price</th>

<th>State</th>

</tr>

<tr>

<td>1</td>

<td>70.00</td>

<td>Successful</td>

</tr>

<tr>

<td>2</td>

<td>70.00</td>

<td>Successful</td>

</tr>

<tr>

<td>3</td>

<td>64.00</td>

<td>Unsuccessful</td>

</tr>

<tr>

<td>4</td>

<td>64.00</td>

<td>Unsuccessful</td>

</tr>

<tr>

<td>5</td>

<td>63.00</td>

<td>Unsuccessful. Bid too low.</td>

</tr>

<tr>

<td>6</td>

<td>56.00</td>

<td>Unsuccessful. Bid too low.</td>

</tr>

</tbody>

</table>

Finally, assume that students with bids @ $64 drops their bids. The bid at $63 will be successful, as it is within the N bids for N vacancies. The minimum bid value, however, does not drop, therefore remaining at $65, even though (Nth bid's value + 1) is $64 instead.

<table class="wikitable">

<tbody>

<tr>

<th>Ranking</th>

<th>Bid Price</th>

<th>State</th>

</tr>

<tr>

<td>1</td>

<td>70.00</td>

<td>Successful</td>

</tr>

<tr>

<td>2</td>

<td>70.00</td>

<td>Successful</td>

</tr>

<tr>

<td>3</td>

<td>63.00</td>

<td>Successful</td>

</tr>

<tr>

<td>4</td>

<td>56.00</td>

<td>Unsuccessful. Bid too low.</td>

</tr>

</tbody>

</table>

## <span class="mw-headline" id="Web_Service_Requirements">Web Service Requirements</span>

**Please note that all json deployment url is clarified and standardized. Please use the revised url for all your json services!!!**

### <span class="mw-headline" id="Overview">Overview</span>

BIOS provides an API using the JSON format. All API responses are an JSON object, and include a 'status' value. The two possible status values are success and error.

Requests use a simple REST-style HTTP GET/POST. The format of a request is as follows:

<pre>http://<host>/app/json/<service>?token=tokenValue&paramA=valueA&paramB=valueB
</pre>

The request query parameters may vary across different services. For this project, all requests (except for the authenticate service) require the sending of the token obtained via the authenticate service.

Note that as defined in the HTTP specification, in any GET or POST request, the order of parameters in query string or request body is unimportant. Therefore, '?date=2014-03-29&time=12:30:00' and '?time=12:30:00&date=2014-03-29' are equivalent.

### <span class="mw-headline" id="JSON_Basics">JSON Basics</span>

Below are some clarification about the JSON language. When in doubt, refer to the [JSON Specification](http://json.org/). If still unsure, test it out with the json checker provided: [JSON Checker](https://github.com/SMU-IS212/json_checker).

#### <span class="mw-headline" id="JSON_Values">JSON Values</span>

A JSON values can be a/an

*   number (integer or floating point). To indicate a floating number, always put a decimal place. For example, 12.0 instead of 12.
*   string (in double quotes)
*   Boolean (true or false)
*   array (in square brackets). array values are ordered.
*   object (in curly brackets)
*   null

Note that strings are case-sensitive.

#### <span class="mw-headline" id="Ordering">Ordering</span>

*   An array is an ordered collection of values.
*   An object is an unordered set of name/value pairs.

#### <span class="mw-headline" id="Whitespace">Whitespace</span>

*   JSON generally ignores any whitespace around or between syntactic elements (values and punctuation), but not within a string value.

### <span class="mw-headline" id="Common_Validations_for_JSON_requests">Common Validations for JSON requests</span>

For all the input fields, you need to check

1.  if the mandatory field is missing
2.  if the field is blank
3.  if the token is invalid

Valid Request:

<pre>http://<host>/app/json/update-bid.php?r={"userid": "ada.goh.2012", "amount": 11.00, "course": "IS100", "section": "S1"}
</pre>

Invalid request:

<pre>http://<host>/app/json/update-bid.php?r={"userid": "ada.goh.2012", "amount": 11.00, "course":""}
</pre>

The response return should be:

Note:

*   Sort all error messages (i.e. be it common validation or logical validation) in **alphabetical order.** (eg. amount first, then course, section, userid). For JSON common validation, sort the error messages by the field name (not by the full error message) as in the example. If the blank field checks fail, you can return the error and do not have to perform any other field validity checks or any other logical validations.
*   Only bootstrap's web service's error messages need not be in alphabetical order, following the order based on the tables at bootstrap functionality is fine.

(**NEW** comma was missing below. Thank you Jeddy for pointing this out. apologies everyone!)

<pre>{
  "status": "error",
  "message": [ "blank course","missing section" ]
}
</pre>

For every JSON web service (except authenticate), you need to verify that the token is valid: the JWTUtility.verify() method must return a non-null string and not throw any exceptions.

Note:

*   The error messages are "blank [field]", "invalid [field]" or "missing [field]", where the field name is as specified below in each function.
*   If there are any common validation errors (missing/blank fields or invalid token), you must return the error and should not perform any other field validity checks, any other logical validations, or any other processing.
*   All missing/blank field or invalid token errors must be returned.
*   Sort all common validation error messages in ascending order.
*   For fields marked "(optional)", it is not necessary to check if they are missing. However, if they are present and blank, then the "blank [field]" error is still triggered as usual.

### <span id="Function-Specific_Input/Output_and_Validations"></span><span class="mw-headline" id="Function-Specific_Input.2FOutput_and_Validations">Function-Specific Input/Output and Validations</span>

#### <span class="mw-headline" id="Authenticate">Authenticate</span>

This web service allows an administrator to authenticate himself/herself. A successful response will return a token pertaining the admin. Request:

<pre>http://<host>/app/json/authenticate.php
</pre>

Request **POST** Parameters and Possible Errors:

<table class="wikitable">

<tbody>

<tr>

<th>field</th>

<th>description</th>

<th>error message</th>

</tr>

<tr>

<td>username</td>

<td>the user's username should match a record in the database</td>

<td>"invalid username"</td>

</tr>

<tr>

<td>password</td>

<td>the user's password should match this particular user's record</td>

<td>"invalid password"</td>

</tr>

</tbody>

</table>

Response:

If authentication is successful, a JSON web token is returned:

<pre>{
    "status": "success",
    "token": "eyJhbGciOiJIUzI1NiJ9.eyJleHAiOjE0MDk3MTIxNTMsImlhdCI6MTQwOTcwODU1M30.h66rOPHh992gpEPtErfqBP3Hrfkh_nNxYwPG0gcAuCc"
}
</pre>

*   *   Details of how to create a shared secret token in PHP will be provided**

#### <span class="mw-headline" id="Bootstrap">Bootstrap</span>

Request:

<pre>http://<host>/app/json/bootstrap.php
</pre>

Bootstrapping is done by sending a HTTP multipart/form-data POST request ([http://www.faqs.org/rfcs/rfc1867.html](http://www.faqs.org/rfcs/rfc1867.html)) to the server. You can assume that the request is sent with these fields:

Request **POST** Parameters:

<table class="wikitable">

<tbody>

<tr>

<th>field</th>

<th>description</th>

</tr>

<tr>

<td>token</td>

<td>a valid token</td>

</tr>

<tr>

<td>bootstrap-file</td>

<td>a zip file</td>

</tr>

</tbody>

</table>

While you may assume that the bootstrap-file is present, can be unzipped, etc., the common validations must still be applied for token: missing token, blank token, invalid token. As usual, if any of these common validations fail, return those error messages and no further processing is required.

You can simulate the above POST parameters by using this HTML snippet:

<pre><form action="http://<host>/json/bootstrap"  method="post" enctype="multipart/form-data">
  File:
  <input type="file" name="bootstrap-file" /><br />
  <input type='text' name='token' value='eyJhbGciOiJIUzI1NiJ9.eyJleHAiOjE0MDk3MTIxNTMsImlhdCI6MTQwOTcwODU1M30.h66rOPHh992gpEPtErfqBP3Hrfkh_nNxYwPG0gcAuCc' />
  <!-- substitute the above value with a valid token -->
  <input type="submit" value="Bootstrap" />
</form>
</pre>

Note:

1.  You must clear all existing data before bootstrapping.
2.  After bootstrapping, round 1 is automatically started.

Response: If the bootstrapping is successful, the following response is returned. The num-record-loaded array is sorted by filename.

<pre>{
  "status": "success",
  "num-record-loaded":
     [
        { "bid.csv": 100},
        { "course.csv": 20 },
        { "course_completed.csv": 87 },
        { "prerequisite.csv": 47 },
        { "section.csv": 87  },
        { "student.csv": 12 }
     ]
}
</pre>

Note: "num-record-loaded" indicates the number of records loaded(i.e. processed) successfully (no error) into your system for each of file in the zipped file. For bid updates, both rows should be counted.

If the bootstrap is unsuccessful, return the following message with the appropriate errors (the full list of errors are listed below).

The errors are ordered by file (alphabetical), then by line number. Within each array of error messages, the messages are also ordered.

*   For missing field validations, follow the left-to-right order of the CSV field list specified for each file.
*   For other validations, follow the order specified in the lists provided above.

<pre>{
  "status": "error",
  "num-record-loaded":
     [
        { "bid.csv": 99},
        { "course.csv": 20 },
        { "course_completed.csv": 3 },
        { "prerequisite.csv": 47 },
        { "section.csv": 87  },
        { "student.csv": 12 }
     ],
  "error":
     [
        {
          "file" : "bid.csv",
          "line" : 2,
          "message" : ["invalid amount", "invalid course"]
        },
        {
          "file" : "bid.csv",
          "line" : 3,
          "message" : ["exam timetable clash", "incomplete prerequisites"]
        },
        {
          "file" : "course_completed.csv",
          "line" : 3,
          "message" : ["invalid course"]
        },
     ]
}
</pre>

#### <span class="mw-headline" id="Dump_Table">Dump Table</span>

1.  This web service will allow an administrator to retrieve details of the courses, sections, students, prerequisites, completed courses, bids, and section-students information (see below on details on what this is) using the format stated below.
2.  Only the bid details for the current round should be shown in the bid records. If the current round is round 2, list the last bid made by each user in each section. If there is no active round, the bids (whether successful or unsuccessful) for the most recently concluded round should be shown. The system does not need to maintain a history of bidding results from previous bidding rounds.
3.  The section-student output (shown below) stores the records of students who have successfully won a bid for a section (in previous round).
4.  You should not clear your database after the dump.

Request:

<pre>http://<host>/app/json/dump.php
</pre>

If the dump is successful, the following response is returned:

<pre>{
   "status": "success",
   "course": [
      {
         "course": "IS100",
         "school": "SIS",
         "title": "Calculus",
         "description": "The basic objective of Calculus is to relate small-scale (differential) quantities to large-scale (integrated) quantities. This is accomplished by means of the Fundamental Theorem of Calculus. Students should demonstrate an understanding of the integral as a cumulative sum, of the derivative as a rate of change, and of the inverse relationship between integration and differentiation.",
         "exam date": "20101119",
         "exam start": "830",
         "exam end": "1145"
      },
      {
         "course": "IS101",
         "school": "SIS",
         "title": "Advanced Calculus",
         "description": "This is a second course on calculus. It is more advanced definitely.",
         "exam date": "20101118",
         "exam start": "1200",
         "exam end": "1515"
      }
   ],
   "section": [
      {
         "course": "IS100",
         "section": "S1",
         "day": "Monday",
         "start": "830",
         "end": "1145",
         "instructor": "Albert KHOO",
         "venue": "Seminar Rm 2-1",
         "size": 10
      },
      {
         "course": "IS101",
         "section": "S1",
         "day": "Tuesday",
         "start": "930",
         "end": "1130",
         "instructor": "Benjamin BEE",
         "venue": "Seminar Rm 3-4",
         "size": 10
      }
   ],
   "student": [
      {
         "userid": "ada.goh.2012",
         "password": "qwerty128",
         "name": "Ada GOH",
         "school": "SIS",
         "edollar": 200.0
      },
      {
         "userid": "joyce.hsu.2011",
         "password": "qwerty123",
         "name": "Joyce HSU",
         "school": "SIS",
         "edollar": 150.0
      }
   ],
   "prerequisite": [
      {
         "course": "IS101",
         "prerequisite": "IS100"
      }
   ],
   "bid": [
      {
         "userid": "ada.goh.2012",
         "amount": 11.0,
         "course": "IS101",
         "section": "S1"
      }
   ],
   "completed-course": [
      {
         "userid": "ada.goh.2012",
         "course": "IS100"
      }
   ],
   "section-student": [
      {
         "userid": "joyce.hsu.2011",
         "course": "IS100",
         "section": "S1",
         "amount": 12.0
      }
   ]
}
</pre>

_Order matters for each array_! The results for each table should be ordered as such:

**course**

1.  Alphabetical order of the course prefix (eg. ACCTxxx, ECONxxx, ISxxx)
2.  Numerical order of the course code. eg (IS100, IS101, IS200, IS306)

(The two orderings above combined will be subsequently referred to as "order of the course code")

**student**

1.  Alphabetical order of the userid

**section**

1.  Order of the course code.
2.  Numerical order of the section number. eg (S01, S02... S09, S10, S11)

**prerequisite**

1.  Order of the course code.
2.  Order of the prerequisite code.

**course_completed**

1.  Order of the course code.
2.  Alphabetical order of the userid.

**bid**

1.  Order of the course code.
2.  order of the section code (S1, S2, S3 ..)
3.  Highest bid to Lowest bid
4.  username

**section-student**

1.  Order of the course code.
2.  Alphabetical order of the students' userid

If the dump is un-successful (should not happen),

<pre>{
  "status" : "error"
}
</pre>

#### <span class="mw-headline" id="Start_round">Start round</span>

This web service will allow an administrator to start a bidding round and enable users to place bids.

Request:

<pre>http://<host>/app/json/start.php
</pre>

Response: If the round is started successfully (or already started), the following response is returned:

<pre>{
 "status": "success",
 "round": 1
}
</pre>

Response (unsuccessful): If round 2 (the maximum possible) has already ended:

<pre>{
 "status": "error",
 "message": [ "round 2 ended" ]
}
</pre>

if the round is not started successfully for some reason (should not happen),

<pre>{
 "status": "error",
 "message": [ <message details what error> ]
}
</pre>

#### <span class="mw-headline" id="Stop_round">Stop round</span>

This web service will allow an administrator to stop and cease all bidding for the current bidding round. This does not start the next bidding round automatically. Students who have placed successful bids will be enrolled into the respective sections.

Request:

<pre>http://<host>/app/json/stop.php
</pre>

Response (successful): The service will returns a success status if there is an active bidding round currently.

<pre>{
 "status": "success"
}
</pre>

Response (unsuccessful):

<pre>{
 "status": "error",
 "message": [ "round already ended" ]
}
</pre>

If the active round cannot be stopped for some reason (should not happen):

<pre>{
 "status": "error",
 "message": [ <message details what error> ]
}
</pre>

#### <span class="mw-headline" id="Update_Bid">Update Bid</span>

This web service will allow an administrator to add a bid for the student if this is the first attempt. Otherwise, if the student has bidded for the same course previously, this will update his existing bid via the same processing logic. If a student has already enrolled in the course (ie, won a successful bid) from previous bidding rounds, the class must first be dropped before placing a bid for another section.

Request:

<pre>http://<host>/app/json/update-bid.php?r={
   "userid": "ada.goh.2012",
   "amount": 11.0,
   "course": "IS100",
   "section": "S1"
}
</pre>

Response (successful):

<pre>{
 "status": "success"
}
</pre>

Response(unsuccessful):

<pre>{
 "status": "error",
 "message":["invalid amount","invalid userid"]
}
</pre>

Below is the list of possible errors:

Input validation:

<table class="wikitable">

<tbody>

<tr>

<td>"invalid amount"</td>

<td>the amount must be a positive number >= e$10.00 and not more than 2 decimal places.</td>

</tr>

<tr>

<td>"invalid course"</td>

<td>the course code is not found in the system records</td>

</tr>

<tr>

<td>"invalid section"</td>

<td>the section code is not found in the system records. Only check if the course code is valid.</td>

</tr>

<tr>

<td>"invalid userid"</td>

<td>the userid is not found in the system records</td>

</tr>

</tbody>

</table>

Logical Validation (only check if input validation is passed). All errors should be returned (the exception is when the round is ended. only that error will be output in that case).

<table class="wikitable">

<tbody>

<tr>

<td>"bid too low"</td>

<td>the amount must be more than the minimum bid (only applicable for round 2)</td>

</tr>

<tr>

<td>"insufficient e$"</td>

<td>student has not enough e-dollars to place the bid. If it is an update of a previous bid, account for the extra e$ gained back from the cancellation of the previous bid first.</td>

</tr>

<tr>

<td>"class timetable clash"</td>

<td>The class timeslot for the section clashes with that of a previously bidded section.</td>

</tr>

<tr>

<td>"exam timetable clash"</td>

<td>The exam timeslot for this section clashes with that of a previously bidded section.</td>

</tr>

<tr>

<td>"incomplete prerequisites"</td>

<td>student has not completed the prerequisites for this course.</td>

</tr>

<tr>

<td>"round ended" (if this is the error, only this error should be output)</td>

<td>there is no active round.</td>

</tr>

<tr>

<td>"course completed"</td>

<td>student has already completed this course.</td>

</tr>

<tr>

<td>"course enrolled"</td>

<td>Student has already won a bid for a section in this course in a previous round.</td>

</tr>

<tr>

<td>"section limit reached"</td>

<td>student has already bidded for 5 sections. If it is an update of a previous bid, account for the cancellation of the previous bid.</td>

</tr>

<tr>

<td>"not own school course"</td>

<td>This only happens in round 1 where students are allowed to bid for modules from their own school.</td>

</tr>

<tr>

<td>"no vacancy"</td>

<td>there is 0 vacancy for the section that the user is bidding.</td>

</tr>

</tbody>

</table>

#### <span class="mw-headline" id="Delete_Bid">Delete Bid</span>

This web service will allow an administrator to delete a bid from the current active round. User will receive e$ refund for the deleted bids.

Request:

<pre>http://<host>/app/json/delete-bid.php?r={
   "userid": "ada.goh.2012",
   "course": "IS100",
   "section": "S1"
}
</pre>

Response (successful):

<pre>{
 "status": "success"
}
</pre>

Response:

<pre>{
 "status": "error",
 "message": [ "no such bid" ]
}
</pre>

List of possible errors:

<table class="wikitable">

<tbody>

<tr>

<td>"invalid course"</td>

<td>Course code does not exist in the system's records</td>

</tr>

<tr>

<td>"invalid userid"</td>

<td>userid does not exist in the system's records</td>

</tr>

<tr>

<td>"invalid section"</td>

<td>No such section ID exists for the particular course. Only check if course is valid</td>

</tr>

<tr>

<td>"round ended"</td>

<td>The current bidding round has already ended.</td>

</tr>

<tr>

<td>"no such bid"</td>

<td>No such bid exists in the system's records. Check only if there is an (1) active bidding round, and (2) course, userid and section are valid and (3)the round is currently active.</td>

</tr>

</tbody>

</table>

#### <span class="mw-headline" id="Drop_Section">Drop Section</span>

This web service will allow an administrator to drop a user's enrollment in a section. User will receive e$ refund. This web service allows the administrator to drop the section of a user.

Request:

<pre>http://<host>/app/json/drop-section.php?r={
   "userid": "ada.goh.2012",
   "course": "IS100",
   "section": "S1"
}
</pre>

Response (successful):

<pre>{
 "status": "success"
}
</pre>

Response (unsuccessful - with appropriate error message):

<pre>{
 "status": "error",
 "message": [ "invalid course" ]
}
</pre>

List of possible errors:

<table class="wikitable">

<tbody>

<tr>

<td>"invalid course"</td>

<td>Course code does not exist in the system's records</td>

</tr>

<tr>

<td>"invalid userid"</td>

<td>userid does not exist in the system's records</td>

</tr>

<tr>

<td>"invalid section"</td>

<td>No such section ID exists for the particular course. Only check if course is valid</td>

</tr>

<tr>

<td>"round not active"</td>

<td>There is currently no active round.</td>

</tr>

</tbody>

</table>

#### <span id="Dump_(User)"></span><span class="mw-headline" id="Dump_.28User.29">Dump (User)</span>

This web service will allow an administrator to retrieve the information of a specific user.

Request:

<pre>http://<host>/app/json/user-dump.php?r={
   "userid": "ada.goh.2012"
}
</pre>

Response (successful):

<pre>{
   "status": "success",
    "userid": "ada.ng.2012",
    "password": "qwerty128",
    "name": "Ada NG",
    "school": "SIS",
    "edollar": 200.0
}
</pre>

Response (unsuccessful):

<pre>{
 "status": "error",
  "message": [ "invalid userid" ]
}
</pre>

List of possible errors:

<table class="wikitable">

<tbody>

<tr>

<td>"invalid userid"</td>

<td>The student id does not exist</td>

</tr>

</tbody>

</table>

#### <span id="Dump_(Bid)"></span><span class="mw-headline" id="Dump_.28Bid.29">Dump (Bid)</span>

This web service will allow an administrator to retrieve the bidding information of a specific section for the current bidding round. If no bidding rounds are active, the information for the most recently concluded round is dumped.

Request:

<pre>http://<host>/app/json/bid-dump.php?r={
         "course": "IS100",
         "section": "S1"
}
</pre>

Response:

*   Note: sorted by the bid (highest to lowest), followed by userid (a to z)
*   Result: During a round, '-' - unconfirmed. If there is no active round, 'in' - Got in the class, 'out' - Unsuccessful bid. Logic as detailed in "Round 1/2 Clearing Logic"

<pre>{
    "status": "success",
    "bids": [
        {
            "row": 1,
            "userid": "ben.ng.2012",
            "amount": 12.0,
            "result": "in"
        },
        {
            "row": 2,
            "userid": "ada.goh.2012",
            "amount": 11.0,
            "result": "out"
        }

    ]
}
</pre>

Response (unsuccessful):

<pre>{
    "status": "error",
    "message": ["invalid course"]
}
</pre>

List of possible errors:

<table class="wikitable">

<tbody>

<tr>

<td>"invalid course"</td>

<td>Course code does not exist in the system's records</td>

</tr>

<tr>

<td>"invalid section"</td>

<td>No such section ID exists for the particular course. Only check if course is valid</td>

</tr>

</tbody>

</table>

#### <span id="Dump_(Section)"></span><span class="mw-headline" id="Dump_.28Section.29">Dump (Section)</span>

This web service will allow an administrator to retrieve the information for a section, and it's enrolled students. During round 2, this should return the enrolled students bidded successfully in round 1\. After round 2 is closed, this should return the enrolled students who bidded successfully in round 1 & 2.

Request:

<pre>http://<host>/app/json/section-dump.php?r={
         "course": "IS100",
         "section": "S1"
}
</pre>

Response (Students should be displayed in alphabetical order of their userid):

<pre>{
    "status": "success",
    "students": [
        {
            "userid": "ada.goh.2012",
            "amount": 11.0,
        },
        {
            "userid": "ben.ng.2012",
            "amount": 12.0
        }
    ]
}
</pre>

Response (unsuccessful):

<pre>{
    "status": "error",
    "message": [ "invalid section" ]
}
</pre>

List of possible errors:

<table class="wikitable">

<tbody>

<tr>

<td>"invalid course"</td>

<td>Course code does not exist in the system's records</td>

</tr>

<tr>

<td>"invalid section"</td>

<td>No such section ID exists for the particular course. Only check if course is valid</td>

</tr>

</tbody>

</table>

#### <span id="Bid_Status_(**NEW**)"></span><span class="mw-headline" id="Bid_Status_.28.2A.2ANEW.2A.2A.29">Bid Status (**NEW**)</span>

#### <span class="mw-headline" id="This_will_NOT_be_tested_in_the_UAT._This_will_be_tested_after_final_submission.">This will NOT be tested in the UAT. This will be tested after final submission.</span>

Your clients were happy to see your team's nice online course bidding application during UAT! While they have been testing JSON interfaces, they would like to have a new JSON interface to check the real-time bidding status (in round 2 in particular) more conveniently.

This new web service will allow an administrator to retrieve a comprehensive bid information given a section and a course, i.e., vacancy, the minimum bid price, and all the bids with (userid, bid amount, e-dollor balance, status). The status is one of {pending, success, fail}.

*   During round 1:
    *   Vacancy: the total available seats as all the bids are still pending.
    *   Minimum bid price: when #bid is less than the #vacancy, report the lowest bid amount. Otherwise, set the price as the clearing price. When there is no bid made, the minimum bid price will be 10.0 dollars.
    *   Bids: report (userid, bid amount, e-dollar balance, status) for all the bids made so far during round 1\. Status should be "pending".
    *   Balance: follow the round 1 logic.
*   After Round 1 ended (and before round 2 is started):
    *   Vacancy: (the total available seats) - (number of successful bid during round 1).
    *   Minimum bid price: report the lowest successful bid. If there was no bid made (or no successful bid) during round 1, the value will be 10.0 dollars.
    *   Bids: report (userid, bid amount, e-dollor balance, status) for all the bids. Status should be either "success" or "fail" according to the round 1 clearing logic.
    *   Balance: follow the clearing round 1 logic.
*   During Round 2:
    *   Vacancy: follow the round 2 logic. (put the total available vacancies as the round is not over)
    *   Minimum bid price: follow the round 2 logic.
    *   Bids: report (userid, bid amount, e-dollor balance, status) for all the bids made during round 2\. Status should be either "success" or "fail" reflecting the real-time bidding status.
    *   Balance: follow the round 2 logic.
*   After round 2 is closed:
    *   Vacancy: (the total available seats) - (number of successfully enrolled students in round 1 and 2).
    *   Minimum bid price: the minimum successful bid amount during round 2\. If there was no bid made (or no successful bid) during round 2, the value will be 10.0 dollars.
    *   Bids: report (userid, bid amount, e-dollor balance, status) for all the successful bids made in round 1 and 2\. Do not include failed bids.
    *   Balance: the e-dollor left after deducting all successful bid amounts in round 1 and 2.

Request:

<pre>http://<host>/app/json/bid-status?r={
         "course": "IS100",
         "section": "S1"
}&token=[tokenValue]
</pre>

Response (Students should be sorted in the reverse order by the bid amount -- from the highest amount to the lowest. When bid amounts are the same, sort by userid in an ascending order):

<pre>{
    "status": "success",
    "vacancy": 2,
    "min-bid-amount": 13.0,
    "students": [
        {
            "userid": "funny",
            "amount": 30.0,
            "balance": 20.0, 
            "status": "success"
        },
        {
            "userid": "apple",
            "amount": 12.0,
            "balance": 38.0, 
            "status": "success"
        }
    ]
}
</pre>

Response (unsuccessful):

<pre>{
    "status": "error",
    "message": [ "invalid section" ]
}
</pre>

List of possible errors:

<table class="wikitable">

<tbody>

<tr>

<td>"invalid course"</td>

<td>Course code does not exist in the system's records</td>

</tr>

<tr>

<td>"invalid section"</td>

<td>No such section ID exists for the particular course. Only check if course is valid</td>

</tr>

</tbody>

</table>
