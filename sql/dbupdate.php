<#1>
<?php
\srag\Plugins\SrUserEnrolment\Config\Config::updateDB();
\srag\Plugins\SrUserEnrolment\Enroll\Enrolled::updateDB();
\srag\Plugins\SrUserEnrolment\Log\Log::updateDB();
\srag\Plugins\SrUserEnrolment\Rule\Rule::updateDB();

\srag\DIC\SrUserEnrolment\DICStatic::dic()->database()
	->createAutoIncrement(\srag\Plugins\SrUserEnrolment\Log\Log::TABLE_NAME, "log_id"); // Using MySQL native autoincrement for performance
?>
