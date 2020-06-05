<?php

namespace srag\Plugins\SrUserEnrolment\Menu;

use ilAdministrationGUI;
use ILIAS\GlobalScreen\Scope\MainMenu\Provider\AbstractStaticPluginMainMenuProvider;
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
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @since   ILIAS 5.4
 */
class Menu extends AbstractStaticPluginMainMenuProvider
{

    use DICTrait;
    use SrUserEnrolmentTrait;

    const PLUGIN_CLASS_NAME = ilSrUserEnrolmentPlugin::class;


    /**
     * @inheritDoc
     */
    public function getStaticTopItems() : array
    {
        return [
            $this->mainmenu->topParentItem($this->if->identifier(ilSrUserEnrolmentPlugin::PLUGIN_ID . "_top"))->withTitle(ilSrUserEnrolmentPlugin::PLUGIN_NAME)
                ->withAvailableCallable(function () : bool {
                    return self::plugin()->getPluginObject()->isActive();
                })->withVisibilityCallable(function () : bool {
                    return true;
                })
        ];
    }


    /**
     * @inheritDoc
     */
    public function getStaticSubItems() : array
    {
        $parent = $this->getStaticTopItems()[0];

        self::dic()->ctrl()->setParameterByClass(RequestsGUI::class, RequestsGUI::GET_PARAM_REF_ID, null);
        self::dic()->ctrl()->setParameterByClass(RequestsGUI::class, RequestsGUI::GET_PARAM_REQUESTS_TYPE, RequestsGUI::REQUESTS_TYPE_OWN);

        self::dic()->ctrl()->setParameterByClass(ilSrUserEnrolmentConfigGUI::class, "ref_id", 31);
        self::dic()->ctrl()->setParameterByClass(ilSrUserEnrolmentConfigGUI::class, "ctype", IL_COMP_SERVICE);
        self::dic()->ctrl()->setParameterByClass(ilSrUserEnrolmentConfigGUI::class, "cname", "UIComponent");
        self::dic()->ctrl()->setParameterByClass(ilSrUserEnrolmentConfigGUI::class, "slot_id", "uihk");
        self::dic()->ctrl()->setParameterByClass(ilSrUserEnrolmentConfigGUI::class, "pname", ilSrUserEnrolmentPlugin::PLUGIN_NAME);

        self::dic()->ctrl()->setParameterByClass(AssistantsGUI::class, AssistantsGUI::GET_PARAM_USER_ID, self::dic()->user()->getId());
        self::dic()->ctrl()->setParameterByClass(DeputiesGUI::class, DeputiesGUI::GET_PARAM_USER_ID, self::dic()->user()->getId());

        return [
            $this->mainmenu->link($this->if->identifier(ilSrUserEnrolmentPlugin::PLUGIN_ID . "_workflows"))
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
                }),
            $this->mainmenu->link($this->if->identifier(ilSrUserEnrolmentPlugin::PLUGIN_ID . "_requests"))
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
                }),
            $this->mainmenu->link($this->if->identifier(ilSrUserEnrolmentPlugin::PLUGIN_ID . "_assistants"))
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
                }),
            $this->mainmenu->link($this->if->identifier(ilSrUserEnrolmentPlugin::PLUGIN_ID . "_deputies"))
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
                })
        ];
    }
}
