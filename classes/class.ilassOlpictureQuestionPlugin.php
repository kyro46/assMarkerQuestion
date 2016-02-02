<?php
	include_once "./Modules/TestQuestionPool/classes/class.ilQuestionsPlugin.php";
	
	/**
	* assOlpictureQuestion plugin
	*
	* @author Yves Annanias <yves.annanias@llz.uni-halle.de>
	* @version $Id$
	* * @ingroup ModulesTestQuestionPool
	*
	*/
	class ilassOlpictureQuestionPlugin extends ilQuestionsPlugin
	{
		final function getPluginName()
		{
			return "assOlpictureQuestion";
		}
		
		final function getQuestionType()
		{
			return "assOlpictureQuestion";
		}
		
		final function getQuestionTypeTranslation()
		{
			return $this->txt('questionType');
		}
	}
?>
