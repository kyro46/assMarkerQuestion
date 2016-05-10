# MarkerQuestion

Questiontypeplugin for ILIAS 5.1.x

For the buggy prototype developed for ILIAS 4.4 see the [**Releases**](https://github.com/kyro46/assMarkerQuestion/releases)

Based on OpenLayers 3

### Questiontype that allows placing and labelling markers on a freely zoomable image ###

This plugin is using [**OpenLayers 3**](http://openlayers.org) and the [**OL3-Contextmenu**](https://github.com/jonataswalker/ol3-contextmenu) to display even very large images in a "google maps"-style.

As examiner:
* create and label polygons to describe areas of interest
* set points to grant for the correct label and/or the correct position of a marker
* set levenshtein distance for labels

As examinee:
* create and label marker
* drag&drop marker
* edit/remove marker

The question supports automatic scoring. The Examiner can choose whether points should be granted if:
* correct labelled marker on correct position
* arbitrary marker at a position
* correct label on a marker, ignoring the position


### Usage ###

Install the plugin

```bash
mkdir -p Customizing/global/plugins/Modules/TestQuestionPool/Questions  
cd Customizing/global/plugins/Modules/TestQuestionPool/Questions
git clone https://github.com/kyro46/assMarkerQuestion.git
```
and activate it in the ILIAS-Admin-GUI.  

### Known Issues ###

* PDF-generation for the "Test Archive File" does not work, just like for all other questions in ILIAS.

### Credits ###
* Developed by Christoph Jobst, University Halle, 2015/2016

