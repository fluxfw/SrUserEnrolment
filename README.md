# SrUserEnrolment ILIAS Plugin

Enrol users such with an excel file or by rules

This project is licensed under the GPL-3.0-only license

## Requirements

* ILIAS 6.0 - 7.999
* PHP >=7.2

## Installation

Start at your ILIAS root directory

```bash
mkdir -p Customizing/global/plugins/Services/UIComponent/UserInterfaceHook
cd Customizing/global/plugins/Services/UIComponent/UserInterfaceHook
git clone https://github.com/fluxfw/SrUserEnrolment.git SrUserEnrolment
```

Update, activate and config the plugin in the ILIAS Plugin Administration

## ILIAS 7 core ilCtrl patch

For make this plugin work with ilCtrl in ILIAS 7, you may need to patch the core, before you update the plugin (At your own risk)

Start at the plugin directory

```shell
./vendor/srag/dic/bin/ilias7_core_apply_ilctrl_patch.sh
```

## Description

### Cron job plugin

If you want to use "Enrol by rule" or "Enrolment workflow", you need to install the [SrUserEnrolmentCron](https://github.com/fluxfw/SrUserEnrolmentCron) plugin

### Main features

This plugin has the follow main features (Each needs to activated separated in the plugin config)

![Config](./doc/images/config.png)

![Main Features](./doc/images/main_features.png)

**Automatic enroll members to course by rules**
![Enrol by rule](./doc/images/enrol_by_rule.png)

**Enroll members to course with an excel file**
![Enrol by excel file](./doc/images/enrol_by_excel_file.png)

**Reset course members password**
![Reset password](./doc/images/reset_password.png)

**Enrolment workflow**
![Enrolment workflow](./doc/images/enrolment_workflow.png)

### Custom event plugins

If you need to do some custom requests changes, SrUserEnrolment will trigger some events, you can listen and react to this in an other custom plugin (plugin type is no matter)

First create or extend a `plugin.xml` in your custom plugin (You need to adapt `PLUGIN_ID` with your own plugin id) to tell ILIAS, your plugins wants to listen to SrUserEnrolment events (You need also to increase your plugin version for take effect)

```xml
<?php xml version = "1.0" encoding = "UTF-8"?>
<plugin id="PLUGIN_ID">
	<events>
		<event id="Plugins/SrUserEnrolment" type="listen" />
	</events>
</plugin>
```

In your plugin class implement or extend the `handleEvent` method

```php
...
require_once __DIR__ . "/../../SrUserEnrolment/vendor/autoload.php";
...
class ilXPlugin extends ...
...
	/**
	 * @inheritDoc
	 */
	public function handleEvent(/*string*/ $a_component, /*string*/ $a_event, /*array*/ $a_parameter)/*: void*/ {
		switch ($a_component) {
			case IL_COMP_PLUGIN . "/" . ilSrUserEnrolmentPlugin::PLUGIN_NAME:
				switch ($a_event) {
					case ilSrUserEnrolmentPlugin::EVENT_...:
						...
						break;

					default:
						break;
				}
				break;

			default:
				break;
		}
	}
...
```

| Event | Parameters | Purpose |
|-------|------------|---------|
| `ilSrUserEnrolmentPlugin::AFTER_REQUEST` | `request => object<Request>` | After a request is done |
| `ilSrUserEnrolmentPlugin::EVENT_COLLECT_REQUEST_STEP_FOR_OTHERS_TABLE_MODIFICATIONS` | `modifications => ArrayObject<AbstractRequestStepForOthersTableModifications>` | Collect request step for others table modifications |
| `ilSrUserEnrolmentPlugin::EVENT_COLLECT_MEMBERS_TABLE_MODIFICATIONS` | `modifications => ArrayObject<AbstractMembersTableModifications>` | Collect members table modifications |
| `ilSrUserEnrolmentPlugin::EVENT_COLLECT_MEMBER_FORM_MODIFICATIONS` | `modifications => ArrayObject<AbstractMemberFormModifications>` | Collect member form modifications |
| `ilSrUserEnrolmentPlugin::EVENT_COLLECT_REQUESTS_TABLE_MODIFICATIONS` | `modifications => ArrayObject<AbstractRequestsTableModifications>` | Collect requests table modifications |
| `ilSrUserEnrolmentPlugin::EVENT_EXTENDS_SRUSRENR` | - | Extends SrUserEnrolment |
