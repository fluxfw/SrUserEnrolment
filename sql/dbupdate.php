<#1>
<?php
\srag\Plugins\SrUserEnrolment\Config\Config::updateDB();
\srag\Plugins\SrUserEnrolment\Enroll\Enrolled::updateDB();
\srag\Plugins\SrUserEnrolment\Log\Log::updateDB();
\srag\Plugins\SrUserEnrolment\Rule\Rule::updateDB();

\srag\DIC\SrUserEnrolment\DICStatic::dic()->database()->manipulate('ALTER TABLE ' . \srag\Plugins\SrUserEnrolment\Log\Log::TABLE_NAME
	. ' MODIFY COLUMN log_id INT NOT NULL AUTO_INCREMENT'); // Using MySQL native autoincrement for performance
?>
