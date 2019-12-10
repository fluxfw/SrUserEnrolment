<?php

namespace srag\Plugins\SrUserEnrolment\Menu;

use ilAdministrationGUI;
use ILIAS\GlobalScreen\Scope\MainMenu\Provider\AbstractStaticPluginMainMenuProvider;
use ilObjComponentSettingsGUI;
use ilSrUserEnrolmentConfigGUI;
use ilSrUserEnrolmentPlugin;
use ilUIPluginRouterGUI;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\Plugins\CtrlMainMenu\Entry\ctrlmmEntry;
use srag\Plugins\CtrlMainMenu\EntryTypes\Ctrl\ctrlmmEntryCtrl;
use srag\Plugins\CtrlMainMenu\Menu\ctrlmmMenu;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Repository;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\Repository as RequestRepository;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Request\RequestsGUI;
use srag\Plugins\SrUserEnrolment\EnrolmentWorkflow\Workflow\WorkflowsGUI;
use srag\Plugins\SrUserEnrolment\Utils\SrUserEnrolmentTrait;
use Throwable;

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
     * @inheritdoc
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
     * @inheritdoc
     */
    public function getStaticSubItems() : array
    {
        $parent = $this->getStaticTopItems()[0];

        self::dic()->ctrl()->setParameterByClass(RequestsGUI::class, RequestsGUI::GET_PARAM_REF_ID, null);

        self::dic()->ctrl()->setParameterByClass(ilSrUserEnrolmentConfigGUI::class, "ref_id", 31);
        self::dic()->ctrl()->setParameterByClass(ilSrUserEnrolmentConfigGUI::class, "ctype", IL_COMP_SERVICE);
        self::dic()->ctrl()->setParameterByClass(ilSrUserEnrolmentConfigGUI::class, "cname", "UIComponent");
        self::dic()->ctrl()->setParameterByClass(ilSrUserEnrolmentConfigGUI::class, "slot_id", "uihk");
        self::dic()->ctrl()->setParameterByClass(ilSrUserEnrolmentConfigGUI::class, "pname", ilSrUserEnrolmentPlugin::PLUGIN_NAME);

        return [
            $this->mainmenu->link($this->if->identifier(ilSrUserEnrolmentPlugin::PLUGIN_ID . "_workflows"))
                ->withParent($parent->getProviderIdentification())
                ->withTitle(self::plugin()->translate("workflows", WorkflowsGUI::LANG_MODULE))
                ->withAction(self::dic()->ctrl()
                    ->getLinkTargetByClass([
                        ilAdministrationGUI::class,
                        ilObjComponentSettingsGUI::class,
                        ilSrUserEnrolmentConfigGUI::class,
                        WorkflowsGUI::class
                    ], WorkflowsGUI::CMD_LIST_WORKFLOWS))
                ->withAvailableCallable(function () : bool {
                    return self::srUserEnrolment()->enrolmentWorkflow()->isEnabled();
                })
                ->withVisibilityCallable(function () : bool {
                    return self::srUserEnrolment()->enrolmentWorkflow()->hasAccess(self::dic()->user()->getId());
                }),
            $this->mainmenu->link($this->if->identifier(ilSrUserEnrolmentPlugin::PLUGIN_ID . "_requests"))
                ->withParent($parent->getProviderIdentification())
                ->withTitle(self::plugin()->translate("requests", RequestsGUI::LANG_MODULE))
                ->withAction(self::dic()->ctrl()
                    ->getLinkTargetByClass([
                        ilUIPluginRouterGUI::class,
                        RequestsGUI::class
                    ], RequestsGUI::CMD_LIST_REQUESTS))
                ->withAvailableCallable(function () : bool {
                    return self::srUserEnrolment()->enrolmentWorkflow()->isEnabled();
                })
                ->withVisibilityCallable(function () : bool {
                    return self::srUserEnrolment()->enrolmentWorkflow()->requests()->hasAccess(self::dic()->user()->getId());
                })
        ];
    }


    /**
     * @deprecated
     */
    public static function addCtrlMainMenu()/*: void*/
    {
        try {
            include_once __DIR__ . "/../../../../../UIComponent/UserInterfaceHook/CtrlMainMenu/vendor/autoload.php";

            if (class_exists(ctrlmmEntry::class)) {
                if (count(ctrlmmEntry::getEntriesByCmdClass(str_replace("\\", "\\\\", WorkflowsGUI::class))) === 0) {
                    $entry = new ctrlmmEntryCtrl();
                    $entry->setTitle(self::plugin()->translate("workflows", WorkflowsGUI::LANG_MODULE, [], true, "en"));
                    $entry->setTranslations([
                        "en" => self::plugin()->translate("workflows", WorkflowsGUI::LANG_MODULE, [], true, "en"),
                        "de" => self::plugin()->translate("workflows", WorkflowsGUI::LANG_MODULE, [], true, "de")
                    ]);
                    $entry->setGuiClass(implode(",", [
                        ilAdministrationGUI::class,
                        ilObjComponentSettingsGUI::class,
                        ilSrUserEnrolmentConfigGUI::class,
                        WorkflowsGUI::class
                    ]));
                    $entry->setCmd(WorkflowsGUI::CMD_LIST_WORKFLOWS);
                    $entry->setPermissionType(ctrlmmMenu::PERM_SCRIPT);
                    $entry->setPermission(json_encode([
                        __DIR__ . "/../../vendor/autoload.php",
                        Repository::class,
                        "hasAccess"
                    ]));
                    $entry->setRefId(31);
                    $entry->setGetParams([
                        [
                            ctrlmmEntryCtrl::PARAM_NAME  => "ctype",
                            ctrlmmEntryCtrl::PARAM_VALUE => IL_COMP_SERVICE
                        ],
                        [
                            ctrlmmEntryCtrl::PARAM_NAME  => "cname",
                            ctrlmmEntryCtrl::PARAM_VALUE => "UIComponent"
                        ],
                        [
                            ctrlmmEntryCtrl::PARAM_NAME  => "slot_id",
                            ctrlmmEntryCtrl::PARAM_VALUE => "uihk"
                        ],
                        [
                            ctrlmmEntryCtrl::PARAM_NAME  => "pname",
                            ctrlmmEntryCtrl::PARAM_VALUE => ilSrUserEnrolmentPlugin::PLUGIN_NAME
                        ]
                    ]);
                    $entry->store();
                }
                if (count(ctrlmmEntry::getEntriesByCmdClass(str_replace("\\", "\\\\", RequestsGUI::class))) === 0) {
                    $entry = new ctrlmmEntryCtrl();
                    $entry->setTitle(self::plugin()->translate("requests", RequestsGUI::LANG_MODULE, [], true, "en"));
                    $entry->setTranslations([
                        "en" => self::plugin()->translate("requests", RequestsGUI::LANG_MODULE, [], true, "en"),
                        "de" => self::plugin()->translate("requests", RequestsGUI::LANG_MODULE, [], true, "de")
                    ]);
                    $entry->setGuiClass(implode(",", [
                        ilUIPluginRouterGUI::class,
                        RequestsGUI::class
                    ]));
                    $entry->setCmd(RequestsGUI::CMD_LIST_REQUESTS);
                    $entry->setPermissionType(ctrlmmMenu::PERM_SCRIPT);
                    $entry->setPermission(json_encode([
                        __DIR__ . "/../../vendor/autoload.php",
                        RequestRepository::class,
                        "hasAccess"
                    ]));
                    $entry->store();
                }
            }
        } catch (Throwable $ex) {
        }
    }


    /**
     * @deprecated
     */
    public static function removeCtrlMainMenu()/*: void*/
    {
        try {
            include_once __DIR__ . "/../../../../../UIComponent/UserInterfaceHook/CtrlMainMenu/vendor/autoload.php";

            if (class_exists(ctrlmmEntry::class)) {
                /**
                 * @var ctrlmmEntry $entry
                 */
                foreach (ctrlmmEntry::getEntriesByCmdClass(str_replace("\\", "\\\\", WorkflowsGUI::class)) as $entry) {
                    $entry->delete();
                }
                foreach (ctrlmmEntry::getEntriesByCmdClass(str_replace("\\", "\\\\", RequestsGUI::class)) as $entry) {
                    $entry->delete();
                }
            }
        } catch (Throwable $ex) {
        }
    }
}
