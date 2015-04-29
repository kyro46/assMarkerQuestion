<#1>
<?php
	//Add Paint Question Type
	$res = $ilDB->queryF("SELECT * FROM qpl_qst_type WHERE type_tag = %s",
		array('text'),
		array('assPaintQuestion')
	);
	if ($res->numRows() == 0)
	{
		$res = $ilDB->query("SELECT MAX(question_type_id) maxid FROM qpl_qst_type");
		$data = $ilDB->fetchAssoc($res);
		$max = $data["maxid"] + 1;

		$affectedRows = $ilDB->manipulateF("INSERT INTO qpl_qst_type (question_type_id, type_tag, plugin) VALUES (%s, %s, %s)", 
			array("integer", "text", "integer"),
			array($max, 'assPaintQuestion', 1)
		);
	}
?>
<#2>
<?php
	//Save Backgroundimage
	$fields = array(
			'question_fi'	=> array('type' => 'integer', 'length' => 4, 'notnull' => true ),
			'image_file' 	=> array('type' => 'text', 'length' => 200, 'fixed' => false, 'notnull' => true )
	);
	$ilDB->createTable("il_qpl_qst_paint_image", $fields);
	$ilDB->addPrimaryKey("il_qpl_qst_paint_image", array("question_fi"));	
?>
<#3>
<?php
	//Enable Colourselection, Brushsize, Canvassize definition
	$fields = array(
			'question_fi'	=> array('type' => 'integer', 'length' => 4, 'notnull' => true ),
			'line' 			=> array('type' => 'integer', 'length' => 1),
			'color' 		=> array('type' => 'integer', 'length' => 1),
			'radio_option' 	=> array('type' => 'text', 'length' => 16, 'notnull' => true, 'fixed' => false, 'default' => 'radioImageSize'),		
			'width' 		=> array('type' => 'integer', 'length' => 8, 'default' => 100 ),
			'height' 		=> array('type' => 'integer', 'length' => 8, 'default' => 100 ),
			'geojson' 		=> array('type' => 'text', 'length' => 1000, 'notnull' => true  ),
			//not needed, text is stored in geojson
			'opttext'		=> array('type' => 'text', 'length' => 1000 )
	);
	$ilDB->createTable("il_qpl_qst_paint_check", $fields);
	$ilDB->addPrimaryKey("il_qpl_qst_paint_check", array("question_fi"));	
?>
<#4>
<?php
	// change type of geojson from char(1000) to longtext
	$ilDB->manipulate("ALTER TABLE `il_qpl_qst_paint_check` CHANGE `geojson` `geojson` LONGTEXT NULL default NULL");
	//$ilDB->addTableColumn('il_qpl_qst_paint_check','geojson2', array("type" => "text", "length" => 20000));
	//$ilDB->manipulate("UPDATE il_qpl_qst_paint_check SET geojson2 = geojson");
	//$ilDB->dropTableColumn('il_qpl_qst_paint_check','geojson');
	//$ilDB->renameTableColumn('il_qpl_qst_paint_check','value2','geojson');
?>
<#5>
<?php
	//add Levenshteindistance as Option
	$ilDB->addTableColumn('il_qpl_qst_paint_check','levenshtein', array("type" => "integer", 'length' => 4, "default" => 3, 'notnull' => true));
	//add Ordergrading as Option
	$ilDB->addTableColumn('il_qpl_qst_paint_check','gradeorder', array('type' => 'integer', 'length' => 1, 'default' => 0));
	//prevent changing of input  for participants
	$ilDB->addTableColumn('il_qpl_qst_paint_check','preventchanges', array('type' => 'integer', 'length' => 1, 'default' => 0));
?>