<?php

namespace srag\Plugins\SrUserEnrolment\Access;

use ilObjCourse;
use ilObjRole;
use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Exception\SrUserEnrolmentException;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class Courses
 *
 * @package srag\Plugins\SrUserEnrolment\Access
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Courses
{

    use DICTrait;
    use SrUserEnrolmentTrait;
    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;
    /**
     * @var self
     */
    protected static $instance = null;


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
     * Courses constructor
     */
    private function __construct()
    {

    }


    /**
     * @param ilObjCourse $course
     * @param int         $user_id
     * @param string      $user_fullname
     *
     * @throws SrUserEnrolmentException
     */
    public function enrollMemberToCourse(ilObjCourse $course, int $user_id, string $user_fullname)/*: void*/
    {
        if ($this->isAssigned($course, $user_id)) {
            throw new SrUserEnrolmentException("User " . $user_fullname . " already assigned as role "
                . implode(", ", array_map(function (int $role_id) : string {
                    return ilObjRole::_getTranslation(self::dic()->objDataCache()->lookupTitle($role_id));
                }, $course->getMembersObject()->getAssignedRoles($user_id))) . " in course " . $course->getTitle());
        }

        $course->getMembersObject()->add($user_id, IL_CRS_MEMBER);
    }


    /**
     * @param ilObjCourse $course
     * @param int         $user_id
     *
     * @return bool
     */
    public function isAssigned(ilObjCourse $course, int $user_id) : bool
    {
        return $course->getMembersObject()->isAssigned($user_id);
    }


    /**
     * @param ilObjCourse $course
     * @param int         $user_id
     *
     * @return bool
     */
    public function isMember(ilObjCourse $course, int $user_id) : bool
    {
        return $course->getMembersObject()->isMember($user_id);
    }
}
