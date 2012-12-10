<?php 
    session_start(); 

    if (!include_once('../config.php')) {
        die('A configuration error occurred. ' 
          . 'Please report this error to the HIT requester.');
    }
	
	require_once('../lib/tempstorage.php');
	
	$store = new tempStorage();
	  
	$workerId = $store->retreive('Worker_ID');
	if ($workerId == null) {
		$workerId = '';
	} else {
		$store->clear('Worker_ID');
	}
	
	$groupName = $store->retreive('Group_Name');
	if ($groupName == null) {
		$groupName = '';
	} else {
		$store->clear('Group_Name');
	}
?>

<!--
Copyright 2012 Adam Darlow and Gideon Goldin

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
-->
<!-- Import the header -->
<?php 
    $title = 'Completion Code';
    $description = 'Generates a completion code that verifies a worker completed the survey.';
    $basePath = '../';
    require_once($basePath . 'includes/header.php'); 
?>
<script>
  function exit() {
    return "Please verify that you have saved the code on this page before leaving!";
  }

  window.onbeforeunload = exit;
</script>
<?php
    // Add the Worker ID and the Group name to the input string
    $inputString = "w[$workerId]g[$groupName]";

    // Add any key-value pairs from the GET array to the input string
    foreach ($_GET as $key => $value) {
        $inputString .= $key[0]."[$value]";
    }

    // Construct the completion code
    $completionCode = $inputString . ':' . sha1($inputString . constant('KEY'));
?>
  <div class="sixteen columns">
    <header><h1>Completion Code</h1></header>
  </div>
  <div class="sixteen columns" style="border-top: 1px solid #DDD; padding-top:10px;"> <!-- sixteen columns clearfix -->
    <p>Please copy <em>all</em> of the text from the box below into Mechanical Turk:</p>
	<?php
			$textAreaId = 'code';	
			$keepAllSelected = true;							
			require_once '../lib/autoselect.php';
    ?>	
	<div class="thirteen columns offset-by-one">
    	<textarea rows="4" id="<?php echo $textAreaId; ?>" readonly><?php echo $completionCode ?><?php echo $completionCode ?><?php echo $completionCode ?></textarea>
	</div>
  </div>
    
</body>
</html>