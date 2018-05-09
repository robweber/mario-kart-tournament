<?php

//final output arrays
$teams = array();
$scores = array();

$currentLevel = 0;
$currentMatch = 0;
$currentTeam = array();
$levelWrapper = array();
$currentScores = array();

foreach($matches as $aMatch){
	
	if($currentLevel == 0)
	{
		$currentLevel = $aMatch['Match']['bracket_level'];
	}
	
	if($currentMatch == 0)
	{
		$currentMatch = $aMatch['Match']['match_num'];
	}
	
	//only build teams on first level
	if($aMatch['Match']['bracket_level'] == 1)
	{
		$driverName = null;
		if($aMatch['Match']['driver_id'] != -1)
		{
			$driverName = array('name'=>$aMatch['Driver']['name'],'img'=>$aMatch['Driver']['image']);
		}
		
		//add to team
		if($currentMatch == $aMatch['Match']['match_num'])
		{
			$currentTeam[] = $driverName;
		}		
		else
		{
			//save team
			$teams[] = $currentTeam;
			$currentTeam = array($driverName);
		}
	}
	else 
	{
		if(count($currentTeam) != 2)
		{
			//we have a bye
			$currentTeam[] = null;
			$currentScores[] = null;
		}
	}

	//echo "Current Level" . $currentLevel. "<br>";
	if($aMatch['Match']['bracket_level'] != $currentLevel)
	{
		//echo "New Level" . $aMatch['Match']['bracket_level']. "<br>";
		//add entire level to scores array
		$currentLevel = $aMatch['Match']['bracket_level'];
		$currentMatch = $aMatch['Match']['match_num'];
		//echo "New Match " . $aMatch['Match']['match_num'] . "<br>";
		
		$levelWrapper[] = $currentScores;
		
		//catch for uneven brackets
		if($currentLevel == 2 && count($levelWrapper) % 2 == 1)
		{
			$levelWrapper[] = array(null,null);
		}
		
		if($currentLevel == 2)
		{
			//add the last team
			$teams[] = $currentTeam;
		}
		$scores[] = $levelWrapper;
		
		$levelWrapper = array();
		$currentScores = array();
	}
	
	//check if we have a new match
	//echo "Current Match " . $currentMatch . "<br>";
	if($aMatch['Match']['match_num'] != $currentMatch)
	{
		//echo "New Match " . $aMatch['Match']['match_num'] . "<br>";
		$currentMatch = $aMatch['Match']['match_num'];
		$levelWrapper[] = $currentScores;
		$currentScores = array();
	}
	
	if($aMatch['Match']['score'] == -1)
	{	
		$currentScores[] = null;
	}
	else
	{
		$currentScores[] = intval($aMatch['Match']['score']);		
	}
}

//add last score
$levelWrapper[] = $currentScores;
$scores[] = $levelWrapper;
$results = array('teams'=>$teams,'results'=>array($scores));
echo json_encode($results);

?>
