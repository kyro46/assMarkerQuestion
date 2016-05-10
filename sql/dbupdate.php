<#1>
<?php
	//Add Marker Question Type
	$res = $ilDB->queryF("SELECT * FROM qpl_qst_type WHERE type_tag = %s",
		array('text'),
		array('assMarkerQuestion')
	);
	if ($res->numRows() == 0)
	{
		$res = $ilDB->query("SELECT MAX(question_type_id) maxid FROM qpl_qst_type");
		$data = $ilDB->fetchAssoc($res);
		$max = $data["maxid"] + 1;

		$affectedRows = $ilDB->manipulateF("INSERT INTO qpl_qst_type (question_type_id, type_tag, plugin) VALUES (%s, %s, %s)", 
			array("integer", "text", "integer"),
			array($max, 'assMarkerQuestion', 1)
		);
	}
?>
<#2>
<?php
	//Backgroundimage
	$fields = array(
			'question_fi'	=> array('type' => 'integer', 'length' => 4, 'notnull' => true ),
			'image_file' 	=> array('type' => 'text', 'length' => 200, 'fixed' => false, 'notnull' => true )
	);
	$ilDB->createTable("il_qpl_qst_marker_img", $fields);
	$ilDB->addPrimaryKey("il_qpl_qst_marker_img", array("question_fi"));	
?>
<#3>
<?php
	//Options
	$fields = array(
			'question_fi'	=> array('type' => 'integer', 'length' => 4, 'notnull' => true ),
			'geojson' 		=> array('type' => 'text', 'length' => 1000, 'notnull' => true  ),
			'opttext'		=> array('type' => 'text', 'length' => 1000 ),
			'levenshtein'	=> array("type" => "integer", 'length' => 4, "default" => 3, 'notnull' => true),
			'gradeorder'	=> array('type' => 'integer', 'length' => 1, 'default' => 0),
			'preventchanges'	=> array('type' => 'integer', 'length' => 1, 'default' => 0)
	);
	$ilDB->createTable("il_qpl_qst_marker_data", $fields);
	$ilDB->addPrimaryKey("il_qpl_qst_marker_data", array("question_fi"));	
?>
<#4>
<?php
	// change type of geojson from char(1000) to longtext
	$ilDB->manipulate("ALTER TABLE `il_qpl_qst_marker_data` CHANGE `geojson` `geojson` LONGTEXT NULL default NULL");
?>