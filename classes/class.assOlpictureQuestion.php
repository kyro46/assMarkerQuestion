<?php

include_once "./Modules/TestQuestionPool/classes/class.assQuestion.php";
include_once "./Modules/Test/classes/inc.AssessmentConstants.php";

/**
 * Class for TemplateQuestion Question
 *
 * @author Christoph Jobst <christoph.jobst@llz.uni-halle.de>
 * @version	$Id:  $
 * @ingroup ModulesTestQuestionPool
 */
class assOlpictureQuestion extends assQuestion
{
	private $plugin = null;	
	// backgroundimage	
	var $image_filename = "";

	//###################################################################
	// geojson
	var $geojson = "";
	// opttext
	var $opttext = "";

	var $levenshtein = 3;
	var $gradeorder = 0;
	var $preventchanges = 0;
	// END ###################################################################
	
	/**
	* assOlpictureQuestion constructor
	*
	* The constructor takes possible arguments an creates an instance of the assOlpictureQuestion object.
	*
	* @param string $title A title string to describe the question
	* @param string $comment A comment string to describe the question
	* @param string $author A string containing the name of the questions author
	* @param integer $owner A numerical ID to identify the owner/creator
	* @param string $question The question string of the single choice question
	* @access public
	* @see assQuestion:assQuestion()
	*/
	function __construct(
		$title = "",
		$comment = "",
		$author = "",
		$owner = -1,
		$question = ""	
	)
	{
		// needed for excel export
		$this->getPlugin()->loadLanguageModule();
		
		parent::__construct($title, $comment, $author, $owner, $question);		
	}
	
	/**
	 * @return object The plugin object
	 */
	public function getPlugin() {
		if ($this->plugin == null)
		{
			include_once "./Services/Component/classes/class.ilPlugin.php";
			$this->plugin = ilPlugin::getPluginObject(IL_COMP_MODULE, "TestQuestionPool", "qst", "assOlpictureQuestion");			
		}
		return $this->plugin;
	}
	
	/**
	 * Returns true, if the question is complete
	 *
	 * @return boolean True, if the question is complete for use, otherwise false
	 */
	public function isComplete()
	{
		// Please add here your own check for question completeness
		// The parent function will always return false
		if(($this->title) and ($this->author) and ($this->question) and ($this->getMaximumPoints() >= 0))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function getImageFilename()
	{
		// backgroundimage
		return $this->image_filename;
	}
	
	function deleteImage()
	{
		$file = $this->getImagePath() . $this->getImageFilename();
		@unlink($file); // delete image from folder
		$this->image_filename = "";
	}

	// new Getter/Setter #############################################
	
	function setGeoJSON($value)
	{
		$this->geojson = $value;
	}
	
	function getGeoJSON()
	{
		return $this->geojson;
	}
	
	function setOptText($value)
	{
		$this->opttext = $value;
	}
	
	function getOptText()
	{
		return $this->opttext;
	}
	
	function setLevenshtein($value)
	{
		$this->levenshtein = $value;
	}
	
	function getLevenshtein()
	{
		return $this->levenshtein;
	}
	
	function setGradeorder($value)
	{
		$this->gradeorder = $value;
	}
	
	function getGradeorder()
	{
		return $this->gradeorder;
	}
	
	function setPreventchanges($value)
	{
		$this->preventchanges = $value;
	}
	
	function getPreventchanges()
	{
		return $this->preventchanges;
	}
	//END #######################################################
	
	
	/**
	 * Set the image file name
	 *
	 * @param string $image_file name.
	 * @access public
	 * @see $image_filename
	 */
	function setImageFilename($image_filename, $image_tempfilename = "") 
	{		
		if (!empty($image_filename)) 
		{
			$image_filename = str_replace(" ", "_", $image_filename);
			$this->image_filename = $image_filename;
		}
		if (!empty($image_tempfilename)) 
		{
			$imagepath = $this->getImagePath();
			if (!file_exists($imagepath)) 
			{
				ilUtil::makeDirParents($imagepath);
			}
			//** TODO  hier kommt noch eine Fehlermeldung, obwohl das Bild am Ende im richtigen Ornder liegt
			
			/*if (!ilUtil::moveUploadedFile($image_tempfilename, $image_filename, $imagepath.'/'.$image_filename))
			{
				$this->ilias->raiseError("The image could not be uploaded!", $this->ilias->error_obj->MESSAGE);
			}*/
			move_uploaded_file($image_tempfilename, $imagepath.'/'.$image_filename);			
		}
	}
	
	/**
	 * Loads a question object from a database
	 * This has to be done here (assQuestion does not load the basic data)!
	 *
	 * @param integer $question_id A unique key which defines the question in the database
	 * @see assQuestion::loadFromDb()
	 */
	public function loadFromDb($question_id)
	{
		global $ilDB;
                
		// load the basic question data
		$result = $ilDB->query("SELECT qpl_questions.* FROM qpl_questions WHERE question_id = "
				. $ilDB->quote($question_id, 'integer'));

		$data = $ilDB->fetchAssoc($result);
		$this->setId($question_id);
		$this->setTitle($data["title"]);
		$this->setComment($data["description"]);
		$this->setSuggestedSolution($data["solution_hint"]);
		$this->setOriginalId($data["original_id"]);
		$this->setObjId($data["obj_fi"]);
		$this->setAuthor($data["author"]);
		$this->setOwner($data["owner"]);
		$this->setPoints($data["points"]);



		include_once("./Services/RTE/classes/class.ilRTE.php");
		$this->setQuestion(ilRTE::_replaceMediaObjectImageSrc($data["question_text"], 1));
		$this->setEstimatedWorkingTime(substr($data["working_time"], 0, 2), substr($data["working_time"], 3, 2), substr($data["working_time"], 6, 2));			

		// load backgroundImage
		$resultImage= $ilDB->queryF("SELECT image_file FROM il_qpl_qst_olpic_image WHERE question_fi = %s", array('integer'), array($question_id));
		if($ilDB->numRows($resultImage) == 1)
		{
			$data = $ilDB->fetchAssoc($resultImage);
			$this->image_filename = $data["image_file"];
		}		
		
		//###################################
		//Angepasste SQL-Abfrage
		$resultCheck= $ilDB->queryF("SELECT geojson, opttext, levenshtein, gradeorder, preventchanges FROM il_qpl_qst_olpic_check WHERE question_fi = %s", array('integer'), array($question_id));
		//END ###################################
		if($ilDB->numRows($resultCheck) == 1)
		{
			$data = $ilDB->fetchAssoc($resultCheck);
			//#########################################################
			$this->setGeoJSON($data["geojson"]);
			$this->setOptText($data["opttext"]);
			$this->setLevenshtein($data["levenshtein"]);
			$this->setGradeorder($data["gradeorder"]);
			$this->setPreventchanges($data["preventchanges"]);
			//END #####################################################
		}
				
		try
		{
			$this->setAdditionalContentEditingMode($data['add_cont_edit_mode']);
		}
		catch(ilTestQuestionPoolException $e)
		{
		}

		// loads additional stuff like suggested solutions
		parent::loadFromDb($question_id);
	}	

	/**
	* Saves a assOlpictureQuestion object to a database
	*
	* @access public
	*/
	function saveToDb($original_id = "")
	{
		global $ilDB, $ilLog;
		$this->saveQuestionDataToDb($original_id);			
		// save background image
		$affectedRows = $ilDB->manipulateF("DELETE FROM il_qpl_qst_olpic_image WHERE question_fi = %s", 
			array("integer"),
			array($this->getId())
		);
		// save image		
		if (!empty($this->image_filename))
		{
			$affectedRows = $ilDB->manipulateF("INSERT INTO il_qpl_qst_olpic_image (question_fi, image_file) VALUES (%s, %s)", 
				array("integer", "text"),
				array(
					$this->getId(),
					$this->image_filename
				)
			);
		}
		// save line and color option
		$affectedRows = $ilDB->manipulateF("DELETE FROM il_qpl_qst_olpic_check WHERE question_fi = %s", 
			array("integer"),
			array($this->getId())
		);
		$affectedRows = $ilDB->manipulateF("INSERT INTO il_qpl_qst_olpic_check (question_fi, geojson, opttext, levenshtein, gradeorder, preventchanges) VALUES (%s, %s, %s, %s, %s, %s)", 
				array("integer", "text", "text", "integer", "integer", "integer"),
				array(
					$this->getId(),
					$this->geojson,
					$this->opttext,
					$this->levenshtein,
					$this->gradeorder,
					$this->preventchanges				
				)
		);
			
		parent::saveToDb();
	}

	/**
	* Returns the maximum points, a learner can reach answering the question
	* @access public
	* @see $points
	*/
	function getMaximumPoints()
	{		
		$maxpoints = 0;

		$sampleSolution = $this->geojson;
		$solution = json_decode($sampleSolution, TRUE);

		foreach($solution['features'] as $item) {
			$points_answer = $item['properties']['points_answer'];
			$points_position = $item['properties']['points_position'];

			$maxpoints += floatval($points_answer);
			$maxpoints += floatval($points_position);			
		}

		return $maxpoints;
	}

/**
* Duplicates an assOlpictureQuestion
*
* @access public
*/
	function duplicate($for_test = true, $title = "", $author = "", $owner = "", $testObjId = null)
	{
		if ($this->id <= 0)
		{
			// The question has not been saved. It cannot be duplicated
			return;
		}
		// duplicate the question in database
		$this_id = $this->getId();
		
		if( (int)$testObjId > 0 )
		{
			$thisObjId = $this->getObjId();
		}
		
		$clone = $this;
		include_once ("./Modules/TestQuestionPool/classes/class.assQuestion.php");
		$original_id = assQuestion::_getOriginalId($this->id);
		$clone->id = -1;
		
		if( (int)$testObjId > 0 )
		{
			$clone->setObjId($testObjId);
		}
		
		if ($title)
		{
			$clone->setTitle($title);
		}
		if ($author)
		{
			$clone->setAuthor($author);
		}
		if ($owner)
		{
			$clone->setOwner($owner);
		}
		if ($for_test)
		{
			$clone->saveToDb($original_id);
		}
		else
		{
			$clone->saveToDb();
		}

		// copy question page content
		$clone->copyPageOfQuestion($this_id);
		// copy XHTML media objects
		$clone->copyXHTMLMediaObjectsOfQuestion($this_id);
		// duplicate the image
		$clone->duplicateImage($this_id, $thisObjId);
		
		$clone->onDuplicate($thisObjId, $this_id, $clone->getObjId(), $clone->getId());
		
		return $clone->id;
	}

	/**
	* Copies an assOlpictureQuestion object
	*
	* @access public
	*/
	function copyObject($target_questionpool_id, $title = "")
	{
		if ($this->id <= 0)
		{
			// The question has not been saved. It cannot be duplicated
			return;
		}
		// duplicate the question in database
		$clone = $this;
		include_once ("./Modules/TestQuestionPool/classes/class.assQuestion.php");
		$original_id = assQuestion::_getOriginalId($this->id);
		$clone->id = -1;
		$source_questionpool_id = $this->getObjId();
		$clone->setObjId($target_questionpool_id);
		if ($title)
		{
			$clone->setTitle($title);
		}
		$clone->saveToDb();

		// copy question page content
		$clone->copyPageOfQuestion($original_id);
		// copy XHTML media objects
		$clone->copyXHTMLMediaObjectsOfQuestion($original_id);
		// duplicate the image
		$clone->copyImage($original_id, $source_questionpool_id);
		
		$clone->onCopy($source_questionpool_id, $original_id, $clone->getObjId(), $clone->getId());
		
		return $clone->id;
	}

	function duplicateImage($question_id, $objectId = null)
	{
		$imagepath = $this->getImagePath();
		$imagepath_original = str_replace("/$this->id/images", "/$question_id/images", $imagepath);
		
		if( (int)$objectId > 0 )
		{
			$imagepath_original = str_replace("/$this->obj_id/", "/$objectId/", $imagepath_original);
		}
		
		if (!file_exists($imagepath)) {
			ilUtil::makeDirParents($imagepath);
		}
		$filename = $this->getImageFilename();
		if (!copy($imagepath_original . $filename, $imagepath . $filename)) {
			print "image could not be duplicated!!!! ";
		}
	}

	function copyImage($question_id, $source_questionpool)
	{
		$imagepath = $this->getImagePath();
		$imagepath_original = str_replace("/$this->id/images", "/$question_id/images", $imagepath);
		$imagepath_original = str_replace("/$this->obj_id/", "/$source_questionpool/", $imagepath_original);
		if (!file_exists($imagepath)) 
		{
			ilUtil::makeDirParents($imagepath);
		}
		$filename = $this->getImageFilename();
		if (!copy($imagepath_original . $filename, $imagepath . $filename)) 
		{
			print "image could not be copied!!!! ";
		}
	}
	
	/**
	 * Calculates if a point is inside a polygon (inkl. borders)
	 * Altered Code, Original from Stackoverflow
	 * see http://stackoverflow.com/questions/8040671/point-in-polygon-php-errors
	 *
	 * @access public
	 */
	function isPointInPolygon($point_, $polygon_) {
		$result =FALSE;
		$point = array("x" => $point_[0], "y" => $point_[1]);
				
		$vertices = array();
		for ($row = 0; $row < count($polygon_); $row++)	
		{
			$vertices[] = array("x" => $polygon_[$row][0], "y" =>  $polygon_[$row][1]);
		}
			
		// Check if the point is inside the polygon or on the boundary
		$intersections = 0;
		$vertices_count = count($vertices);
		for ($i=1; $i < $vertices_count; $i++)
		{
			$vertex1 = $vertices[$i-1];
			$vertex2 = $vertices[$i];
			if ($vertex1['y'] == $vertex2['y'] and $vertex1['y'] == $point['y'] and $point['x'] > min($vertex1['x'], $vertex2['x']) and $point['x'] < max($vertex1['x'], $vertex2['x']))
			{
				// This point is on an horizontal polygon boundary
				$result = TRUE;
				// set $i = $vertices_count so that loop exits as we have a boundary point
				$i = $vertices_count;
			}
			if ($point['y'] > min($vertex1['y'], $vertex2['y']) and $point['y'] <= max($vertex1['y'], $vertex2['y']) and $point['x'] <= max($vertex1['x'], $vertex2['x']) and $vertex1['y'] != $vertex2['y'])
			{
				$xinters = ($point['y'] - $vertex1['y']) * ($vertex2['x'] - $vertex1['x']) / ($vertex2['y'] - $vertex1['y']) + $vertex1['x'];
				if ($xinters == $point['x'])
					{ 	// This point is on the polygon boundary (other than horizontal)
						$result = TRUE;
						// set $i = $vertices_count so that loop exits as we have a boundary point
						$i = $vertices_count;
					}
				if ($vertex1['x'] == $vertex2['x'] || $point['x'] <= $xinters)
				{
					$intersections++;
				}
			}
		}
		
		// If the number of edges we passed through is even, then it's in the polygon.
		// Have to check here also to make sure that we haven't already determined that a point is on a boundary line
		if ($intersections % 2 != 0 && $result == FALSE)
		{
			$result = TRUE;
		}
			
		return $result;
	}
	
	
	/**
	 * Returns the points, a learner has reached answering the question
	 * The points are calculated from the given answers including checks
	 * for all special scoring options in the test container.
	 *
	 * @param integer $user_id The database ID of the learner
	 * @param integer $test_id The database Id of the test containing the question
	 * @param boolean $returndetails (deprecated !!)
	 * @access public
	 */
	function calculateReachedPoints($active_id, $pass = NULL, $authorizedSolution = true, $returndetails = false)
	{
		/* Documentation
		 *
		 * the participents input gives us
		 * - coordinates
		 * - label
		 * - order of creation
		 *
		 * we take from the samplesolution
		 * - coordinates
		 * - label
		 * - points for label
		 * - points for position
		 * - order of creation
		 *
		 * so we have two arrays:
		 * ###Participentinput:
		 * Array (
		 * 		[0] => Array (
		 * 			[0] => Kermit
		 * 			[1] => Array ( [0] => -316.71092748642 [1] => -20.906252861023 ) )
		 * 		[1] => Array (
		 * 			[0] => Handpuppe
		 * 			[1] => Array ( [0] => -52.33592748642 [1] => 21.281247138977 ) ) )
		 *
		 * ###Samplesolution:
		 * Array (
		 * 	[0] => Array (
		 * 		[0] => Handpuppe
		 * 		[1] => 3 // Points for label
		 * 		[2] => 1 // Points for position
		 * 		[3] => Array (
		 * 			[0] => Array (
		 * 				[0] => Array ( [0] => -256.85155391693 [1] => 292.28907108307 )
		 * 				[1] => Array ( [0] => -262.47655391693 [1] => 64.476571083069 )
		 * 				[2] => Array ( [0] => -119.03905391693 [1] => 70.101571083069 )
		 * 				[3] => Array ( [0] => -119.03905391693 [1] => 295.10157108307 )
		 * 				[4] => Array ( [0] => -256.85155391693 [1] => 292.28907108307 )
		 * 			)
		 * 		)
		 * 	)
		 * 	[1] => Array (
		 * 		[0] => Kermit der Frosch
		 * 		[1] => 1
		 * 		[2] => 1
		 * 		[3] => Array (
		 * 			[0] => Array (
		 * 				[0] => Array ( [0] => -383.41405391693 [1] => 30.726571083069 )
		 * 				[1] => Array ( [0] => -383.41405391693 [1] => -239.27342891693 )
		 * 				[2] => Array ( [0] => -214.66405391693 [1] => -242.08592891693 )
		 * 				[3] => Array ( [0] => -201.31518386621 [1] => 44.789071083069 )
		 * 				[4] => Array ( [0] => -313.81518386621 [1] => 50.414071083069 )
		 * 				[5] => Array ( [0] => -383.41405391693 [1] => 30.726571083069 )
		 * 			)
		 * 		)
		 * 	)
		 * )
		 *
		 * we compare the labels considering levenshtein distance
		 *
		 * levenshtein distance of labels in this case:
		 * 9 	Kermit 		-> Handpuppe
		 * 11 	Kermit 		-> Kermit der Frosch
		 * 0 	Handpuppe 	-> Handpuppe
		 * 16 	Handpuppe	-> Kermit der Frosch
		 *
		 * if correct -> we give the points for label
		 * and test if point in polygon
		 * 
		 * if point in polygon -> we give the points for position and add them to the labelpoints
		 * return points
		 */
		
		/* Additional feature for export
		 * 
		 * we save each input in an new array, stating
		 * - labelinput
		 * - points for label
		 * - points for position
		 * to create a datastructure for the excelexport detailsheet
		 * for EACH pass - not just the best/latest
		 * it is stored in the yet unused 'value2'
		 * 
		 */
		
		global $ilDB;
		
		if (is_null($pass))
		{
			$pass = $this->getSolutionMaxPass($active_id);
		}
		
		$result = $ilDB->queryF("SELECT * FROM tst_solutions WHERE active_fi = %s AND question_fi = %s AND pass = %s",
			array(
				"integer", 
				"integer",
				"integer"
			),
			array(
				$active_id,
				$this->getId(),
				$pass
			)
		);
		
		$sampleSolution = $this->geojson;
		
		$data = $ilDB->fetchAssoc($result);
		$inputstring = $data['value1'];
		// not needed yet
		//$value2 = $data['value2'];

		$solution = json_decode($sampleSolution, TRUE);
		$input = json_decode($inputstring, TRUE);
		
		$solutionarray = array();
		$inputarray = array();
		
		// prepare grading of labels
		// get solution
		foreach($solution['features'] as $item) {
			$name = $item['properties']['name'];
			$points_answer = $item['properties']['points_answer'];
			$points_position = $item['properties']['points_position'];
			$coordinates = $item['geometry']['coordinates'];
	
			$row = array($name, $points_answer, $points_position, $coordinates);
			//$solutionarray[] = $row;
			array_push($solutionarray, $row);
		}	
			
		
		// get input
		foreach($input['features'] as $item) {
		$name = $item['properties']['name'];
				$coordinates =$item['geometry']['coordinates'];
				$row = array($name, $coordinates);
				array_push($inputarray, $row);
		}

		$points = 0;
		$gradingarray = array(); // separate datastructure to store points/labels for export
			
		//for every input made...
		for ($rowInput = 0; $rowInput < count($inputarray); $rowInput++) {
			$inputLabel = $inputarray[$rowInput][0];
			$gradingarray[$rowInput]['InputLabel'] = $inputLabel;
				
			//... a comparison with every solution availible
			for ($rowSolution = 0; $rowSolution < count($solutionarray); $rowSolution++) {
	
				$distance = levenshtein($solutionarray[$rowSolution][0],$inputLabel);
	
				if ($distance <= $this->levenshtein) {
				/* got ya! 
				 * - grant point - check
				 * - delete from list - check: unset, array_values
				 * - add to separate coordinatechecklist: testForCoordinatesArray
				 */
					$points += floatval($solutionarray[$rowSolution][1]);
					//error_log("A user got ".$solutionarray[$rowSolution][1]." points for labeling ".$solutionarray[$rowSolution][0]." as ".$inputLabel, 0);
					$gradingarray[$rowInput]['GrantedPointsforLabel'] = $solutionarray[$rowSolution][1];
					
					// Check coordinates if label matched
					$pointInPolygon = $this->isPointInPolygon($inputarray[$rowInput][1],$solutionarray[$rowSolution][3][0]);
					
					//add points for position if point in polygon
					if ($pointInPolygon)
					{
						$points += floatval($solutionarray[$rowSolution][2]);
						$gradingarray[$rowInput]['GrantedPointsforPosition'] = $solutionarray[$rowSolution][2];
						//error_log("A user got ".$solutionarray[$rowSolution][2]." points for placing ".$inputLabel, 0);
					} 
					else {
						//error_log("A user placed ".$inputLabel." wrong.", 0);
					}

					unset($solutionarray[$rowSolution]);
					$solutionarray = array_values($solutionarray);
					$rowSolution = 0;
				}
			}
			
		}
		
		//Save complete gradingsheme to DB for export
		$ilDB->update("tst_solutions", array(
        "value2" =>        array("clob", json_encode($gradingarray))),
        array(
						"active_fi" =>		array("integer", $active_id),
						"question_fi" =>	array("integer", $this->getId()),
						"pass" =>        	array("integer", $pass)
		));
		
		//no negative points
		if ($points < 0) {
			$points = 0;
			error_log("A user got negative points - set points to 0.", 0);
		}

		return $points;
	}
	
    /**
	* Returns the filesystem path for file uploads
	*/
	protected function getFileUploadPath($test_id, $active_id)
	{
		$question_id = $this->getId();
		return CLIENT_WEB_DIR . "/assessment/tst_$test_id/$active_id/$question_id/files/";
	}
	
	/**
	* Saves the learners input of the question to the database
	*
	* @param integer $test_id The database id of the test containing this question
    * @return boolean Indicates the save status (true if saved successful, false otherwise)
	* @access public
	* @see $answers
	*/
	function saveWorkingData($active_id, $pass = NULL, $authorized = true)
	{
		global $ilDB;
		global $ilUser;
		if (is_null($pass))
		{
			include_once "./Modules/Test/classes/class.ilObjTest.php";
			$pass = ilObjTest::_getPass($active_id);
		}

		$affectedRows = $ilDB->manipulateF("DELETE FROM tst_solutions WHERE active_fi = %s AND question_fi = %s AND pass = %s",
			array(
				"integer", 
				"integer",
				"integer"
			),
			array(
				$active_id,
				$this->getId(),
				$pass
			)
		);

		$entered_values = false;		
		//$value = $_POST['answerImage'];		
		
		// new #######################
		
		$geojsonfromdoc = $_POST['geojson'];
		
		// end #######################
		
		$result = $ilDB->queryF("SELECT test_fi FROM tst_active WHERE active_id = %s",
			array('integer'),
			array($active_id)
		);
		$test_id = 0;
		if ($result->numRows() == 1)
		{
			$row = $ilDB->fetchAssoc($result);
			$test_id = $row["test_fi"];
		}
		
		//START #############
		//if (strlen($value) > 0)
		if (strlen($geojsonfromdoc) > 0)
		// END ##############
		{
			$filename = $this->getFileUploadPath($test_id, $active_id).time()."_OlpictureTask.png";
			$entered_values = true;
			$next_id = $ilDB->nextId("tst_solutions");
			$affectedRows = $ilDB->insert("tst_solutions", array(
				"solution_id" => array("integer", $next_id),
				"active_fi" => array("integer", $active_id),
				"question_fi" => array("integer", $this->getId()),
				// START ####################
				"value1" => array("clob", $geojsonfromdoc),
				//"value1" => array("clob", 'path'),
				//"value2" => array("clob", $filename),
				// END ######################
				"pass" => array("integer", $pass),
				"tstamp" => array("integer", time())
			));
			
			/*
			if (!@file_exists($this->getFileUploadPath($test_id, $active_id))) 
				ilUtil::makeDirParents($this->getFileUploadPath($test_id, $active_id));
			
			// Grab all files from the desired folder
			$files = glob( $this->getFileUploadPath($test_id, $active_id).'*.png' );
			if (count($files) == 3)
			{
				unlink($files[0]);
			}						
			$imageInfo = $value;
			$image = fopen($imageInfo, 'r');
			file_put_contents($filename, $image);
			fclose($image);
			*/
		}
		
		if ($entered_values)
		{
			include_once ("./Modules/Test/classes/class.ilObjAssessmentFolder.php");
			if (ilObjAssessmentFolder::_enabledAssessmentLogging())
			{
				$this->logAction($this->lng->txtlng("assessment", "log_user_entered_values", ilObjAssessmentFolder::_getLogLanguage()), $active_id, $this->getId());				
			}
		}
		else
		{
			include_once ("./Modules/Test/classes/class.ilObjAssessmentFolder.php");
			if (ilObjAssessmentFolder::_enabledAssessmentLogging())
			{
				$this->logAction($this->lng->txtlng("assessment", "log_user_not_entered_values", ilObjAssessmentFolder::_getLogLanguage()), $active_id, $this->getId());
			}
		}		
		return true;
	}
	
	/**
	 * Reworks the allready saved working data if neccessary
	 *
	 * @abstract
	 * @access protected
	 * @param integer $active_id
	 * @param integer $pass
	 * @param boolean $obligationsAnswered
	 */
	protected function reworkWorkingData($active_id, $pass, $obligationsAnswered)
	{
		// nothing to rework!		
	}

	/**
	* Returns the question type of the question
	*
	* @return integer The question type of the question
	* @access public
	*/
	function getQuestionType()
	{
		return "assOlpictureQuestion";
	}
	
	/**
	* Returns the name of the additional question data table in the database
	*
	* @return string The additional table name
	* @access public
	*/
	function getAdditionalTableName()
	{
		return "";
	}
	
	/**
	* Returns the name of the answer table in the database
	*
	* @return string The answer table name
	* @access public
	*/
	function getAnswerTableName()
	{
		return "";
	}

	/**
	* Collects all text in the question which could contain media objects
	* which were created with the Rich Text Editor
	*/
	function getRTETextWithMediaObjects()
	{
		$text = parent::getRTETextWithMediaObjects();
		return $text;
	}
	
	/**
	* Creates an Excel worksheet for the detailed cumulated results of this question
	*
	* @param object $worksheet Reference to the parent excel worksheet
	* @param object $startrow Startrow of the output in the excel worksheet
	* @param object $active_id Active id of the participant
	* @param object $pass Test pass
	* @param object $format_title Excel title format
	* @param object $format_bold Excel bold format
	* @param array $eval_data Cumulated evaluation data
	* @access public
	*/
	public function setExportDetailsXLS(&$worksheet, $startrow, $active_id, $pass, &$format_title, &$format_bold)
	{	
		include_once ("./Services/Excel/classes/class.ilExcelUtils.php");
		$maxpass = $this->getSolutionMaxPass($active_id);
		
		$worksheet->writeString($startrow, 0, ilExcelUtils::_convert_text($this->lng->txt($this->getQuestionType())), $format_title);
		$worksheet->writeString($startrow, 1, ilExcelUtils::_convert_text($this->getTitle()), $format_title);
		$worksheet->writeString($startrow, 2, "Label", $format_title);
		$worksheet->writeString($startrow, 3, "Labelpoints", $format_title);
		$worksheet->writeString($startrow, 4, "Positionpoints", $format_title);
		

		$i = $startrow + 1;
		
		for( $passNr = 0; $passNr <= $maxpass; $passNr++)
		{
			$solution = $this->getSolutionValues($active_id, $passNr);
			$gradingarray = json_decode($solution[0]["value2"], true);
			//error_log($gradingarray[0]["InputLabel"]);
			
			//Col 1: passNr
			$worksheet->writeString($i, 0, "Pass ".($passNr+1), $format_bold);
		
			//Col 2-4: label and points
			for( $input = 0; $input < count($gradingarray); $input++)
			{
				$worksheet->writeString($i, 2, $gradingarray[$input]["InputLabel"], $format_bold);
				$worksheet->writeNumber($i, 3, $gradingarray[$input]["GrantedPointsforLabel"]);
				$worksheet->writeNumber($i, 4, $gradingarray[$input]["GrantedPointsforPosition"]);
				
				$i++;
			}
			$i++;
		}
		return $i + 1;
	}

	/**
	* Creates a question from a QTI file
	*
	* Receives parameters from a QTI parser and creates a valid ILIAS question object
	*
	* @param object $item The QTI item object
	* @param integer $questionpool_id The id of the parent questionpool
	* @param integer $tst_id The id of the parent test if the question is part of a test
	* @param object $tst_object A reference to the parent test object
	* @param integer $question_counter A reference to a question counter to count the questions of an imported question pool
	* @param array $import_mapping An array containing references to included ILIAS objects
	* @access public
	*/
	function fromXML(&$item, &$questionpool_id, &$tst_id, &$tst_object, &$question_counter, &$import_mapping)
	{
		$this->getPlugin()->includeClass("import/qti12/class.assOlpictureQuestionImport.php");
		$import = new assOlpictureQuestionImport($this);
		$import->fromXML($item, $questionpool_id, $tst_id, $tst_object, $question_counter, $import_mapping);
	}
	
	/**
	* Returns a QTI xml representation of the question and sets the internal
	* domxml variable with the DOM XML representation of the QTI xml representation
	*
	* @return string The QTI xml representation of the question
	* @access public
	*/
	function toXML($a_include_header = true, $a_include_binary = true, $a_shuffle = false, $test_output = false, $force_image_references = false)
	{
		$this->getPlugin()->includeClass("export/qti12/class.assOlpictureQuestionExport.php");
		$export = new assOlpictureQuestionExport($this);
		return $export->toXML($a_include_header, $a_include_binary, $a_shuffle, $test_output, $force_image_references);
	}
}
?>
