<#1>
<?php
\srag\Plugins\SrUserEnrolment\Config\Config::updateDB();
try {
    \srag\Plugins\SrUserEnrolment\Enroll\Enrolled::updateDB();
} catch (\Throwable $ex) {
    // Fix Call to a member function getName() on null (Because not use ILIAS sequence)
}
try {
    \srag\Plugins\SrUserEnrolment\Log\Log::updateDB();
} catch (\Throwable $ex) {
    // Fix Call to a member function getName() on null (Because not use ILIAS sequence)
}
\srag\Plugins\SrUserEnrolment\Rule\Rule::updateDB();

\srag\DIC\SrUserEnrolment\DICStatic::dic()->database()
    ->createAutoIncrement(\srag\Plugins\SrUserEnrolment\Log\Log::TABLE_NAME, "log_id"); // Using MySQL native autoincrement for performance
?>
