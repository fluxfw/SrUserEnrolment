# Changelog

## [3.23.0]
- Clean up

## [3.22.3]
- Move `ilias7_core_apply_ilctrl_patch.sh`

## [3.22.2]
- Twig PHP 7.4 patch

## [3.22.1]
- Twig PHP 7.4 patch

## [3.22.0]
- Switched to main branch
- ILIAS 7 support
- Remove ILIAS 5.4 support
- Fix create course date
- Min PHP 7.2

## [3.21.0]
- Sort rules by "Enroll as"

## [3.20.7]
- Config for process empty udf values

## [3.20.6]
- Update lp status after enroll

## [3.20.5]
- Fix run rules from course gui

## [3.20.4]
- Change utils url

## [3.20.3]
- Update urls

## [3.20.2]
- Fix logs end date filter

## [3.20.1]
- Fix possible wrong read cache of continue on hang/crash for enrol by rule cron job

## [3.20.0]
- Config for continue on hang/crash for enrol by rule cron job
- Log errors for whole rule
- Move config "Keep old logs maximal for" to cron job self

## [3.19.3]
- Ignore not supported languages

## [3.19.2]
- Update readme

## [3.19.1]
- Update project url

## [3.19.0]
- Multiple values in UDF rule

## [3.18.0]
- Delete old logs cron job

## [3.17.7]
- Supports update login field
- Update readme

## [3.17.6]
- Ping after each object for not ILIAS auto set inactive cron job if during longer

## [3.17.5]
- Not load objects on cron table

## [3.17.4]
- Fix workflow object actions on newer ILIAS versions

## [3.17.3]
- Fix workflow object actions on newer ILIAS versions

## [3.17.2]
- Fix workflow object actions on newer ILIAS versions

## [3.17.1]
- Fix sort icons in workflow tables in ILIAS 6

## [3.17.0]
- Possibility sort rules

## [3.16.6]
- No mapping found error message

## [3.16.5]
- Main menu icon

## [3.16.4]
- Cache ilias objects

## [3.16.3]
- `Ilias7PreWarn`

## [3.16.2]
- Dev tools

## [3.16.1]
- Fix set user object title

## [3.16.0]
- Excel import: Map exists users by each field

## [3.15.1]
- Fix PHP 7.0

## [3.15.0]
- Excel import: Map exists users by matriculation number

## [3.14.0]
- UDF rule value type date
- "Enroll by rule" settings for unenroll and update "Enroll as"
- ILIAS 6 support
- Remove ILIAS 5.3 support

## [3.13.0]
- Assign role by rule

## [3.12.1]
- Fix

## [3.12.0]
- Excel import
  - Create users direct in a global or local roles
  - Log user which executed

## [3.11.2]
- Assign global roles

## [3.11.1]
- Also for user management

## [3.11.0]
- "Enrol by excel file"
    - Config for enable in course or local user administration
    - Config for replace "Import user" in local user administration

## [3.10.1]
- Some fixes and changes

## [3.10.0]
- 'Enroll by rules' type

## [3.9.5]
- Some fixes and changes

## [3.9.4]
- Some fixes and changes

## [3.9.3]
- Some fixes and changes

## [3.9.2]
- Some fixes and changes

## [3.9.1]
- Some fixes and changes

## [3.9.0]
- Some fixes and changes

## [3.8.0]
- Some fixes and changes

## [3.7.0]
- Members view
- Rule 'Total requests'

## [3.6.1]
- Request user is not allowed to accept steps or be a request responsible user

## [3.6.0]
- Comments for request
- Performance in multi search

## [3.5.0]
- Rule 'Current user is assigned as responsible user'
- Filter recursive and UDF in action 'Assign responsible users'
- Fixes

## [3.4.5]
- Update Notifications4Plugin
- 'Run next actions' config

## [3.4.4]
- Static link to request

## [3.4.3]
- Assign only responsible users which has no requests at self

## [3.4.2]
- Fix run action rules

## [3.4.1]
- Fix users autocomplete not used search term

## [3.4.0]
- Requests table: All, own, open
- Send notification option to responsible users and deputies
- Add responsible users to request
- 'User definied field of supervisors' rule

## [3.3.0]
- Improve user selection in assistants and deputies
- Request new users from requests table
- Request create user and accept time/user
- Some changes in request info

## [3.2.0]
- Enrolment workflow deputies
- Support required data for multiple requests
- Manage assistants for each user also on administration (like deputies)
- Cron job for remove inactive users as assistsants/deputies
- Fixes

## [3.1.0]
- 'No responsible users assigned' rule
- Enrolment workflow assistants
- Some other improvments and fixes

## [3.0.6]
- Some improvments

## [3.0.5]
- Fix update org unit log
- Optimized failed logs

## [3.0.4]
- Select languages in user language rule
- Fix position check in org unit user type rule

## [3.0.3]
- Fix move request in create course action

## [3.0.2]
- Fix select fields on create course action
- Fix create course action

## [3.0.1]
- Fix accept request without required fields

## [3.0.0]
- Enrolment workflow
- Course rule enrolment supports enrolment workflow rules too
- Excel import configuration can be hidden in course (only in plugin config)
- Other improvments and fixes

## [2.3.3]
- Fix local user administration permission check

## [2.3.2]
- Fix ILIAS 5.3

## [2.3.1]
- Show logs in 'Enrol by excel file'
- Log user data on enrolment
- Fix may occured error `User default field org_unit_position not found!`

## [2.3.0]
- Improve user update (Separate user creation/update and enrolment)

## [2.2.1]
- Fix install

## [2.2.0]
- Format date on password fields (Experimental)

## [2.1.0]
- Direct local user administration

## [2.0.1]
- Fix SQL query (Missing space)

## [2.0.0]
- Import and enrol users with excel file
- Reset password from course member list
- Update description

## [1.0.0]
- First version
