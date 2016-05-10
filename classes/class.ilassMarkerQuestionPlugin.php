<?php
	include_once "./Modules/TestQuestionPool/classes/class.ilQuestionsPlugin.php";
	
	/**
	* assMarkerQuestion plugin
	*
	* @author Christoph Jobst <christoph.jobst@llz.uni-halle.de>
	* @version $Id$
	* * @ingroup ModulesTestQuestionPool
	*
	*/
	class ilassMarkerQuestionPlugin extends ilQuestionsPlugin
	{
		final function getPluginName()
		{
			return "assMarkerQuestion";
		}
		
		final function getQuestionType()
		{
			return "assMarkerQuestion";
		}
		
		final function getQuestionTypeTranslation()
		{
			return $this->txt('questionType');
		}
	}
?>
