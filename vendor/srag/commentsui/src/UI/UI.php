<?php

namespace srag\CommentsUI\SrUserEnrolment\UI;

use ilTemplate;
use srag\CommentsUI\SrUserEnrolment\Ctrl\CtrlInterface;
use srag\CommentsUI\SrUserEnrolment\Utils\CommentsUITrait;
use srag\CustomInputGUIs\SrUserEnrolment\Template\Template;
use srag\DIC\SrUserEnrolment\DICTrait;
use srag\DIC\SrUserEnrolment\Plugin\PluginInterface;
use srag\DIC\SrUserEnrolment\Version\PluginVersionParameter;

/**
 * Class UI
 *
 * @package srag\CommentsUI\SrUserEnrolment\UI
 */
class UI implements UIInterface
{

    use DICTrait;
    use CommentsUITrait;

    /**
     * @var bool
     */
    protected static $init = false;
    /**
     * @var CtrlInterface
     */
    protected $ctrl_class;
    /**
     * @var string
     */
    protected $id = "";
    /**
     * @var PluginInterface|null
     */
    protected $plugin = null;


    /**
     * UI constructor
     */
    public function __construct()
    {

    }


    /**
     * @inheritDoc
     */
    public function getPlugin() : PluginInterface
    {
        return $this->plugin;
    }


    /**
     * @inheritDoc
     */
    public function render() : string
    {
        $tpl = new Template(__DIR__ . "/../../templates/commentsui.html", false, false);

        $tpl->setVariableEscaped("ID", $this->id);

        $tpl->setVariable("READONLY", json_encode($this->ctrl_class->getIsReadOnly()));

        $tpl->setVariable("ASYNC_BASE_URL", json_encode($this->ctrl_class->getAsyncBaseUrl()));

        $this->initJs($tpl);

        return self::output()->getHTML($tpl);
    }


    /**
     * @inheritDoc
     */
    public function withCtrlClass(CtrlInterface $ctrl_class) : UIInterface
    {
        $this->ctrl_class = $ctrl_class;

        return $this;
    }


    /**
     * @inheritDoc
     */
    public function withId(string $id) : UIInterface
    {
        $this->id = $id;

        return $this;
    }


    /**
     * @inheritDoc
     */
    public function withPlugin(PluginInterface $plugin) : self
    {
        $this->plugin = $plugin;

        return $this;
    }


    /**
     * @return array
     */
    protected function getLanguageStrings() : array
    {
        return array_map(function (string $key) : string {
            return self::comments()->getPlugin()->translate($key, self::LANG_MODULE_COMMENTSUI);
        }, [
            "deleteText"              => "delete",
            "editText"                => "edit",
            "editedText"              => "edited",
            "noCommentsText"          => "no_comments",
            "newestText"              => "newest",
            "oldestText"              => "oldest",
            "saveText"                => "save",
            "sendText"                => "save",
            "shareText"               => "share_comment_for_user",
            "textareaPlaceholderText" => "comment",
            //"hideRepliesText" => "Hide replies",
            //"popularText" => "Popular",
            //"replyText" => "Reply",
            //"viewAllRepliesText" => "View all __replyCount__ replies",
            //"youText" => "You"
        ]);
    }


    /**
     * @param ilTemplate $tpl
     */
    private function initJs(ilTemplate $tpl) : void
    {
        if (self::$init === false) {
            self::$init = true;

            $version_parameter = PluginVersionParameter::getInstance();
            if ($this->plugin !== null) {
                $version_parameter = $version_parameter->withPlugin($this->plugin);
            }

            $dir = __DIR__;
            $dir = "./" . substr($dir, strpos($dir, "/Customizing/") + 1);

            self::dic()->ui()->mainTemplate()->addJavaScript($version_parameter->appendToUrl($dir . "/../../node_modules/jquery-comments/js/jquery-comments.js"));
            self::dic()->ui()->mainTemplate()->addCss($version_parameter->appendToUrl($dir . "/../../node_modules/jquery-comments/css/jquery-comments.css"));

            self::dic()->ui()->mainTemplate()->addJavaScript($version_parameter->appendToUrl($dir . "/../../js/commentsui.min.js", $dir . "/../../js/commentsui.js"));
            self::dic()->ui()->mainTemplate()->addCss($version_parameter->appendToUrl($dir . "/../../css/commentsui.css"));

            $tpl->setCurrentBlock("init");

            $tpl->setVariable("LANGUAGES", json_encode($this->getLanguageStrings()));

            $tpl->setVariable("PROFILE_IMAGE_URL", json_encode(self::dic()->user()->getPersonalPicturePath("big")));
        }
    }
}
