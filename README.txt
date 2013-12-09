Thunderbird-Topics course format

This is an attempt at a course format derived from the standard Moodle 'topics' course format,
but where topic 0 is pre-populated with course specific information from the SIS system.

Topic 0 section name is always the "Course full name"  ($course->fullname),
At the start of topic 0, we always show the 'Course summary" ($course->summary), followed by a horizonta rule element.
(The $course->summary field is populated from our SIS via IMS enrolment)

Apart from these small changes, this course format behaves like the standard Moodle 'topics' format.

Note: if you auto-populate the course full name, and course summary fields from your SIS or otherwise (eg. web service)
you should carefully consider what roles (eg. teacher) have the capabilities to change these fields!
Look at the following permissions: moodle/course:changefullname, moodle/course:changesummary

INSTALLATION:

- Copy these files to <html>/course/format/topics_tbird/
- Enable as usual from the admin notification page
- Change course format to 'Topics Thunderbird' as needed, either on each course settings page,
  or from default course settings ( Admin => Courses => Course default settings)


COPYRIGHT LICENSE:
This module is Copyright(c) 2013 onward, Thunderbird School of Global Management, with portions
contributed/copyrighted by many others (see the Moodle Developer credits and the Moodle source code itself)
and all of it is provided under the terms of the GPL.

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License
as published by the Free Software Foundation, version 3, dated 29 June 2007.

See the LICENSE.txt included for specific terms.

WARRANTY:
This module is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty
of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License included in LICENSE.txt for
more details.
