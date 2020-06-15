<?php

namespace srag\Plugins\SrUserEnrolment\ExcelImport;

use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\ExcelImport\User\UserExcelImport;
use srag\Plugins\SrUserEnrolment\ExcelImport\User\UserExcelImportFormGUI;
use srag\Plugins\SrUserEnrolment\ExcelImport\User\UserExcelImportGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class Factory
 *
 * @package srag\Plugins\SrUserEnrolment\ExcelImport
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Factory
{

    use DICTrait;
    use SrUserEnrolmentTrait;

    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    /**
     * @var self|null
     */
    protected static $instance = null;


    /**
     * Factory constructor
     */
    private function __construct()
    {

    }


    /**
     * @return self
     */
    public static function getInstance() : self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * @param ExcelImportGUI $parent
     *
     * @return ExcelImportFormGUI
     */
    public function newFormInstance(ExcelImportGUI $parent) : ExcelImportFormGUI
    {
        $form = new ExcelImportFormGUI($parent);

        return $form;
    }


    /**
     * @param ExcelImportGUI $parent
     *
     * @return ExcelImport
     */
    public function newImportInstance(ExcelImportGUI $parent) : ExcelImport
    {
        $excel_import = new ExcelImport($parent);

        return $excel_import;
    }


    /**
     * @param UserExcelImportGUI $parent
     *
     * @return UserExcelImportFormGUI
     */
    public function newUserFormInstance(UserExcelImportGUI $parent) : UserExcelImportFormGUI
    {
        $form = new UserExcelImportFormGUI($parent);

        return $form;
    }


    /**
     * @param ExcelImportGUI $parent
     *
     * @return UserExcelImport
     */
    public function newUserImportInstance(ExcelImportGUI $parent) : UserExcelImport
    {
        $excel_import = new UserExcelImport($parent);

        return $excel_import;
    }
}
