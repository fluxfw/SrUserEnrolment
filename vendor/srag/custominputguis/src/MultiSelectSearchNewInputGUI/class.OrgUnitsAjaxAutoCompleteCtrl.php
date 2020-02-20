<?php

namespace srag\CustomInputGUIs\SrUserEnrolment\MultiSelectSearchNewInputGUI;

use ilObjOrgUnit;

/**
 * Class OrgUnitsAjaxAutoCompleteCtrl
 *
 * @package srag\CustomInputGUIs\SrUserEnrolment\MultiSelectSearchNewInputGUI
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class OrgUnitsAjaxAutoCompleteCtrl extends AbstractAjaxAutoCompleteCtrl
{

    /**
     * @var int
     */
    protected $parent_ref_id;


    /**
     * OrgUnitsAjaxAutoCompleteCtrl constructor
     *
     * @param int|null $parent_ref_id
     */
    public function __construct(/*?*/ int $parent_ref_id = null)
    {
        parent::__construct();

        $this->parent_ref_id = $parent_ref_id ?? ilObjOrgUnit::getRootOrgRefId();
    }


    /**
     * @inheritDoc
     */
    public function searchOptions(string $search = null) : array
    {
        $org_units = [];

        foreach (
            array_filter(self::dic()->tree()->getSubTree(self::dic()->tree()->getNodeData($this->parent_ref_id)), function (array $item) use ($search): bool {
                return (stripos($item["title"], $search) !== false);
            }) as $item
        ) {
            $org_units[$item["child"]] = $item["title"];
        }

        return $org_units;
    }


    /**
     * @inheritDoc
     */
    public function fillOptions(array $ids) : array
    {
        return array_combine($ids, array_map(function (int $org_unit_ref_id) : string {
            return self::dic()->objDataCache()->lookupTitle(self::dic()->objDataCache()->lookupObjId($org_unit_ref_id));
        }, $ids));
    }
}
