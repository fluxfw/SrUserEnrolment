<?php

namespace srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Position;

/**
 * Trait Position
 *
 * @package srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Rule\Fields\Position
 */
trait Position
{

    /**
     * @var int
     *
     * @con_has_field     true
     * @con_fieldtype     integer
     * @con_length        8
     * @con_is_notnull    true
     */
    protected $position = PositionConstants::POSITION_ALL;


    /**
     * @return int
     */
    public function getPosition() : int
    {
        return $this->position;
    }


    /**
     * @param int $position
     */
    public function setPosition(int $position)/* : void*/
    {
        $this->position = $position;
    }


    /**
     * @return string
     */
    protected function getPositionTitle() : string
    {
        if ($this->getPosition() === PositionConstants::POSITION_ALL) {
            return "";
        }

        return self::srUserEnrolment()->ruleEnrolment()->getPositions()[$this->position];
    }
}
