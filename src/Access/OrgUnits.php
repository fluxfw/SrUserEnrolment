<?php

namespace srag\Plugins\SrUserEnrolment\Access;

use ilDBConstants;
use ilObjUser;
use ilOrgUnitPosition;
use ilOrgUnitUserAssignment;
use ilSrUserEnrolmentPlugin;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\Rule\Rule;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;
use stdClass;

/**
 * Class OrgUnits
 *
 * @package srag\Plugins\SrUserEnrolment\Access
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class OrgUnits
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
     * OrgUnits constructor
     */
    private function __construct()
    {

    }


    /**
     * @param int $user_id
     * @param int $org_unit_ref_id
     * @param int $position_id
     */
    public function assignOrgUnit(int $user_id, int $org_unit_ref_id, int $position_id)/*: void*/
    {
        ilOrgUnitUserAssignment::findOrCreateAssignment($user_id, $position_id, $org_unit_ref_id);
    }


    /**
     * @param Rule $rule
     *
     * @return ilObjUser[]
     */
    public function getOrgUnitUsers(Rule $rule) : array
    {
        $wheres = ["type=%s"];
        $types = [ilDBConstants::T_TEXT];
        $values = ["orgu"];

        switch ($rule->getOrgUnitType()) {
            case Rule::ORG_UNIT_TYPE_TITLE:
                switch ($rule->getOperator()) {
                    case Rule::OPERATOR_EQUALS:
                        if ($rule->isOperatorCaseSensitive()) {
                            if ($rule->isOperatorNegated()) {
                                $wheres[] = "UPPER(title)!=UPPER(%s)";
                            } else {
                                $wheres[] = "UPPER(title)=UPPER(%s)";
                            }
                        } else {
                            if ($rule->isOperatorNegated()) {
                                $wheres[] = "title!=%s";
                            } else {
                                $wheres[] = "title=%s";
                            }
                        }
                        $types[] = ilDBConstants::T_TEXT;
                        $values[] = $rule->getTitle();
                        break;

                    case Rule::OPERATOR_STARTS_WITH:
                        if ($rule->isOperatorCaseSensitive()) {
                            if ($rule->isOperatorNegated()) {
                                $wheres[] = "UPPER(title) NOT LIKE UPPER(%s)";
                            } else {
                                $wheres[] = "UPPER(title) LIKE UPPER(%s)";
                            }
                        } else {
                            if ($rule->isOperatorNegated()) {
                                $wheres[] = "title NOT LIKE %s";
                            } else {
                                $wheres[] = "title LIKE %s";
                            }
                        }
                        $types[] = ilDBConstants::T_TEXT;
                        $values[] = $rule->getTitle() . "%";
                        break;

                    case Rule::OPERATOR_CONTAINS:
                        if ($rule->isOperatorCaseSensitive()) {
                            if ($rule->isOperatorNegated()) {
                                $wheres[] = "UPPER(title) NOT LIKE UPPER(%s)";
                            } else {
                                $wheres[] = "UPPER(title) LIKE UPPER(%s)";
                            }
                        } else {
                            if ($rule->isOperatorNegated()) {
                                $wheres[] = "title NOT LIKE %s";
                            } else {
                                $wheres[] = "title LIKE %s";
                            }
                        }
                        $types[] = ilDBConstants::T_TEXT;
                        $values[] = "%" . $rule->getTitle() . "%";
                        break;

                    case Rule::OPERATOR_ENDS_WITH:
                        if ($rule->isOperatorCaseSensitive()) {
                            if ($rule->isOperatorNegated()) {
                                $wheres[] = "UPPER(title) NOT LIKE UPPER(%s)";
                            } else {
                                $wheres[] = "UPPER(title) LIKE UPPER(%s)";
                            }
                        } else {
                            if ($rule->isOperatorNegated()) {
                                $wheres[] = "title NOT LIKE %s";
                            } else {
                                $wheres[] = "title LIKE %s";
                            }
                        }
                        $types[] = ilDBConstants::T_TEXT;
                        $values[] = "%" . $rule->getTitle();
                        break;

                    case Rule::OPERATOR_REG_EX:
                        if ($rule->isOperatorCaseSensitive()) {
                            if ($rule->isOperatorNegated()) {
                                $wheres[] = "UPPER(title) NOT REGEXP %s";
                            } else {
                                $wheres[] = "UPPER(title) REGEXP %s";
                            }
                        } else {
                            if ($rule->isOperatorNegated()) {
                                $wheres[] = "title NOT REGEXP %s";
                            } else {
                                $wheres[] = "title REGEXP %s";
                            }
                        }
                        $types = [ilDBConstants::T_TEXT];
                        $values = [$rule->getTitle()];
                        break;

                    default:
                        return [];
                }
                break;

            case Rule::ORG_UNIT_TYPE_TREE:
                $wheres[] = "ref_id=%s";
                $types[] = ilDBConstants::T_INTEGER;
                $values[] = $rule->getRefId();

                // TODO: ($this->rule->getOperator() === Rule::OPERATOR_EQUALS_SUBSEQUENT)
                break;

            default:
                return [];
        }

        if ($rule->getPosition() !== Rule::POSITION_ALL) {
            $wheres[] = "position_id=%s";
            $types[] = ilDBConstants::T_INTEGER;
            $values[] = $rule->getPosition();
        }

        $array = self::dic()->database()->fetchAllCallback(self::ilias()
            ->getObjectFilterStatement($wheres, $types, $values, ["user_id"], 'INNER JOIN il_orgu_ua ON object_reference.ref_id=il_orgu_ua.orgu_id'), function (stdClass $data) : ilObjUser {
            return new ilObjUser($data->user_id);
        });

        return $array;
    }


    /**
     * @param string $position
     *
     * @return int|null
     */
    public function getPositionIdByTitle(string $position)/*: ?int*/
    {
        /**
         * @var ilOrgUnitPosition|null $position
         */
        $position = ilOrgUnitPosition::where([
            "title" => $position
        ])->first();

        if ($position !== null) {
            return $position->getId();
        } else {
            return null;
        }
    }


    /**
     * @return array
     */
    public function getPositions() : array
    {
        return array_map(function (ilOrgUnitPosition $position) : string {
            return $position->getTitle();
        }, ilOrgUnitPosition::get());
    }
}
