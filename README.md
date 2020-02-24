This is an OpenSource project by studer + raimann ag, CH-Burgdorf (https://studer-raimann.ch)

## Description
See in [doc/DESCRIPTION.md](./doc/DESCRIPTION.md)

## Documentation
See in [doc/DOCUMENTATION.md](./doc/DOCUMENTATION.md)

## Installation

### Install SrUserEnrolment-Plugin
Start at your ILIAS root directory
```bash
mkdir -p Customizing/global/plugins/Services/UIComponent/UserInterfaceHook
cd Customizing/global/plugins/Services/UIComponent/UserInterfaceHook
git clone https://github.com/studer-raimann/SrUserEnrolment.git SrUserEnrolment
```
Update, activate and config the plugin in the ILIAS Plugin Administration

Please also install and enable [SrUserEnrolmentCron](https://github.com/studer-raimann/SrUserEnrolmentCron).

### Custom event plugins
If you need to do some custom requests changes, SrUserEnrolment will trigger some events, you can listen and react to this in a other custom plugin (plugin type is no matter)

First create or extend a `plugin.xml` in your custom plugin (You need to adapt `PLUGIN_ID` with your own plugin id) to tell ILIAS, your plugins wants to listen to SrUserEnrolment events (You need also to increase your plugin version for take effect)

```xml
<?php xml version = "1.0" encoding = "UTF-8"?>
<plugin id="PLUGIN_ID">
	<events>
		<event type="listen" id="Plugins/SrUserEnrolment" />
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
| `ilSrUserEnrolmentPlugin::EVENT_COLLECT_ASSISTANTS_REQUESTS_TABLE_MODIFICATIONS` | `modifications => &array<AbstractAssistantsRequestsTableModifications>` | Collect assistants requests table modifications (Please note `modifications` is a reference variable, if it should not works) |
| `ilSrUserEnrolmentPlugin::EVENT_COLLECT_MEMBERS_TABLE_MODIFICATIONS` | `modifications => &array<AbstractMembersTableModifications>` | Collect memmbers table modifications (Please note `modifications` is a reference variable, if it should not works) |
| `ilSrUserEnrolmentPlugin::EVENT_COLLECT_REQUESTS_TABLE_MODIFICATIONS` | `modifications => &array<AbstractRequestsTableModifications>` | Collect requests table modifications (Please note `modifications` is a reference variable, if it should not works) |
| `ilSrUserEnrolmentPlugin::EVENT_EXTENDS_SRUSRENR` | - | Extends SrUserEnrolment |

### Requirements
* ILIAS 5.3 or ILIAS 5.4
* PHP >=7.0

### Adjustment suggestions
* External users can report suggestions and bugs at https://plugins.studer-raimann.ch/goto.php?target=uihk_srsu_PLUSE
* Adjustment suggestions by pull requests via github
* Customer of studer + raimann ag: 
	* Adjustment suggestions which are not yet worked out in detail by Jira tasks under https://jira.studer-raimann.ch/projects/PLUSE
	* Bug reports under https://jira.studer-raimann.ch/projects/PLUSE

### ILIAS Plugin SLA
Wir lieben und leben die Philosophie von Open Source Software! Die meisten unserer Entwicklungen, welche wir im Kundenauftrag oder in Eigenleistung entwickeln, stellen wir öffentlich allen Interessierten kostenlos unter https://github.com/studer-raimann zur Verfügung.

Setzen Sie eines unserer Plugins professionell ein? Sichern Sie sich mittels SLA die termingerechte Verfügbarkeit dieses Plugins auch für die kommenden ILIAS Versionen. Informieren Sie sich hierzu unter https://studer-raimann.ch/produkte/ilias-plugins/plugin-sla.

Bitte beachten Sie, dass wir nur Institutionen, welche ein SLA abschliessen Unterstützung und Release-Pflege garantieren.
