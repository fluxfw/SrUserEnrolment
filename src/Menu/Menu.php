<?php

namespace srag\Plugins\SrUserEnrolment\Menu;

use ilAdministrationGUI;
use ilDBConstants;
use ILIAS\GlobalScreen\Scope\MainMenu\Factory\AbstractBaseItem;
use ILIAS\GlobalScreen\Scope\MainMenu\Provider\AbstractStaticMainMenuPluginProvider;
use ILIAS\UI\Component\Symbol\Icon\Standard;
use ilObjComponentSettingsGUI;
use ilSrUserEnrolmentConfigGUI;
use ilSrUserEnrolmentPlugin;
use ilUIPluginRouterGUI;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Assistant\AssistantsGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Deputy\DeputiesGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\RequestsGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Workflow\WorkflowsGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;

/**
 * Class Menu
 *
 * @package srag\Plugins\SrUserEnrolment\Menu
 */
class Menu extends AbstractStaticMainMenuPluginProvider
{

    use DICTrait;
    use SrUserEnrolmentTrait;

    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;


    /**
     * @inheritDoc
     */
    public function getStaticSubItems() : array
    {
        $parent = $this->getStaticTopItems()[0];

        self::dic()->ctrl()->setParameterByClass(RequestsGUI::class, RequestsGUI::GET_PARAM_REF_ID, null);
        self::dic()->ctrl()->setParameterByClass(RequestsGUI::class, RequestsGUI::GET_PARAM_REQUESTS_TYPE, RequestsGUI::REQUESTS_TYPE_OWN);

        self::dic()
            ->ctrl()
            ->setParameterByClass(ilSrUserEnrolmentConfigGUI::class, "ref_id", self::dic()
                                                                                   ->database()
                                                                                   ->queryF('SELECT ref_id FROM object_data INNER JOIN object_reference ON object_data.obj_id=object_reference.obj_id WHERE type=%s',
                                                                                       [ilDBConstants::T_TEXT], ["cmps"])
                                                                                   ->fetchAssoc()["ref_id"]);
        self::dic()->ctrl()->setParameterByClass(ilSrUserEnrolmentConfigGUI::class, "ctype", IL_COMP_SERVICE);
        self::dic()->ctrl()->setParameterByClass(ilSrUserEnrolmentConfigGUI::class, "cname", "UIComponent");
        self::dic()->ctrl()->setParameterByClass(ilSrUserEnrolmentConfigGUI::class, "slot_id", "uihk");
        self::dic()->ctrl()->setParameterByClass(ilSrUserEnrolmentConfigGUI::class, "pname", ilSrUserEnrolmentPlugin::PLUGIN_NAME);

        self::dic()->ctrl()->setParameterByClass(AssistantsGUI::class, AssistantsGUI::GET_PARAM_USER_ID, self::dic()->user()->getId());
        self::dic()->ctrl()->setParameterByClass(DeputiesGUI::class, DeputiesGUI::GET_PARAM_USER_ID, self::dic()->user()->getId());

        return [
            $this->symbol($this->mainmenu->link($this->if->identifier(ilSrUserEnrolmentPlugin::PLUGIN_ID . "_workflows"))
                ->withParent($parent->getProviderIdentification())
                ->withTitle(self::plugin()->translate("workflows", WorkflowsGUI::LANG_MODULE))
                ->withAction(str_replace("\\", "%5C", self::dic()->ctrl()
                    ->getLinkTargetByClass([
                        ilAdministrationGUI::class,
                        ilObjComponentSettingsGUI::class,
                        ilSrUserEnrolmentConfigGUI::class,
                        WorkflowsGUI::class
                    ], WorkflowsGUI::CMD_LIST_WORKFLOWS)))
                ->withAvailableCallable(function () : bool {
                    return self::srUserEnrolment()->enrolmentWorkflow()->isEnabled();
                })
                ->withVisibilityCallable(function () : bool {
                    return self::srUserEnrolment()->enrolmentWorkflow()->hasAccess(self::dic()->user()->getId());
                })),
            $this->symbol($this->mainmenu->link($this->if->identifier(ilSrUserEnrolmentPlugin::PLUGIN_ID . "_requests"))
                ->withParent($parent->getProviderIdentification())
                ->withTitle(self::plugin()->translate("requests", RequestsGUI::LANG_MODULE))
                ->withAction(str_replace("\\", "%5C", self::dic()->ctrl()
                    ->getLinkTargetByClass([
                        ilUIPluginRouterGUI::class,
                        RequestsGUI::class
                    ], RequestsGUI::CMD_LIST_REQUESTS)))
                ->withAvailableCallable(function () : bool {
                    return self::srUserEnrolment()->enrolmentWorkflow()->isEnabled();
                })
                ->withVisibilityCallable(function () : bool {
                    return self::srUserEnrolment()->enrolmentWorkflow()->requests()->hasAccess(self::dic()->user()->getId());
                })),
            $this->symbol($this->mainmenu->link($this->if->identifier(ilSrUserEnrolmentPlugin::PLUGIN_ID . "_assistants"))
                ->withParent($parent->getProviderIdentification())
                ->withTitle(self::plugin()->translate("my_assistants", AssistantsGUI::LANG_MODULE))
                ->withAction(str_replace("\\", "%5C", self::dic()->ctrl()
                    ->getLinkTargetByClass([
                        ilUIPluginRouterGUI::class,
                        AssistantsGUI::class
                    ], AssistantsGUI::CMD_EDIT_ASSISTANTS)))
                ->withAvailableCallable(function () : bool {
                    return self::srUserEnrolment()->enrolmentWorkflow()->assistants()->isEnabled();
                })
                ->withVisibilityCallable(function () : bool {
                    return self::srUserEnrolment()->enrolmentWorkflow()->assistants()->hasAccess(self::dic()->user()->getId());
                })),
            $this->symbol($this->mainmenu->link($this->if->identifier(ilSrUserEnrolmentPlugin::PLUGIN_ID . "_deputies"))
                ->withParent($parent->getProviderIdentification())
                ->withTitle(self::plugin()->translate("my_deputies", DeputiesGUI::LANG_MODULE))
                ->withAction(str_replace("\\", "%5C", self::dic()->ctrl()
                    ->getLinkTargetByClass([
                        ilUIPluginRouterGUI::class,
                        DeputiesGUI::class
                    ], DeputiesGUI::CMD_EDIT_DEPUTIES)))
                ->withAvailableCallable(function () : bool {
                    return self::srUserEnrolment()->enrolmentWorkflow()->deputies()->isEnabled();
                })
                ->withVisibilityCallable(function () : bool {
                    return self::srUserEnrolment()->enrolmentWorkflow()->deputies()->hasAccess(self::dic()->user()->getId());
                }))
        ];
    }


    /**
     * @inheritDoc
     */
    public function getStaticTopItems() : array
    {
        return [
            $this->symbol($this->mainmenu->topParentItem($this->if->identifier(ilSrUserEnrolmentPlugin::PLUGIN_ID . "_top"))->withTitle(ilSrUserEnrolmentPlugin::PLUGIN_NAME)
                ->withAvailableCallable(function () : bool {
                    return self::plugin()->getPluginObject()->isActive();
                })->withVisibilityCallable(function () : bool {
                    return true;
                }))
        ];
    }


    /**
     * @param AbstractBaseItem $entry
     *
     * @return AbstractBaseItem
     */
    protected function symbol(AbstractBaseItem $entry) : AbstractBaseItem
    {
        $entry = $entry->withSymbol(self::dic()->ui()->factory()->symbol()->icon()->standard(Standard::USR, ilSrUserEnrolmentPlugin::PLUGIN_NAME)->withIsOutlined(true));

        return $entry;
    }
}
