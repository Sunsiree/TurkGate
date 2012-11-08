<?php
/*

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

*/

	if(isset($_POST['downloadCLTFile'])) {
	
		// Forces browser to download instead of open files
		header("Content-Type: application/octet-stream");
		
		// Required for base URL
		$installed = @include('config.php');
		
		//All of the variables in the files that need to be substitute
		$substitutions = array( '[[[TurkGate URL]]]' => constant('BASE_URL'));
		$substitutions['[[[Survey URL]]]'] = $_POST['externalSurveyURL'];
		$substitutions['[[[Group Name]]]'] = $_POST['groupName'];	
			
		// File name pulled from submit button values
		$fileName = $_POST['downloadCLTFile'];
		$file = 'resources/CLTHIT/' . $fileName;
		
		header("Content-Disposition: attachment; filename=" . $fileName);   
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");
		header("Content-Description: File Transfer");            
		header("Content-Length: " . filesize($file));
		flush(); // this doesn't really matter.
		$fp = fopen($file, "r");
		while (!feof($fp)) {
			
			$text = fread($fp, 65536);
			
			// Perform the specified substitutions
			// NOTE: Could fail with files larger than buffer size!
			foreach ($substitutions as $original => $new) {
			    $text = str_replace($original, $new, $text);
			}

			echo $text;
			
			// this is essential for large downloads
		    flush(); 
		} 
		
		fclose($fp);
		exit;
	}

	// Create a string for the HTML code
	$webTemplateString = "";
	
	// Get the form values
	$externalSurveyURL = isset($_POST['externalSurveyURL']) ? $_POST['externalSurveyURL'] : "";
	$groupName = isset($_POST['groupName']) ? $_POST['groupName'] : "";	

	// Check if TurkGate is installed
	$installed = @include('config.php');
	if(!installed) {
		echo '<p>TurkGate does not appear to be install. See your administrator.</p><p>Go <a href="index.php">back</a>.</p>';
		echo '<h5>Powered by <a href=http://gideongoldin.github.com/TurkGate/">TurkGate</a></h5>';
		exit;
	} else {
		if(isset($_POST['generateHTMLCode'])) {
				// Modify the web template
				// First read the entire file
				$webTemplateString = file_get_contents('resources/WebHIT/webTemplate.html');

				// Make the necessary changes
				$webTemplateString = str_replace('[[[Survey URL]]]', $_POST['externalSurveyURL'], $webTemplateString);
				$webTemplateString = str_replace('[[[Group Name]]]', $_POST['groupName'], $webTemplateString);
				$webTemplateString = str_replace('[[[TurkGate URL]]]', constant('BASE_URL'), $webTemplateString);
				$copyright = "<!-- Copyright (c) 2012 Adam Darlow and Gideon Goldin. For more info, see http://gideongoldin.github.com/TurkGate/ -->\n";
				$webTemplateString = preg_replace('/<!--[^>]*-->/', $copyright, $webTemplateString, 1);
		}
	}	
?>

<!DOCTYPE HTML>
<html>
  <head>
    <title>TurkGate</title>
  </head>
  <body>
  	<h1>TurkGate</h1>
  	<h2>HIT Generation Page</h2>

	<p>
		From here you may generate the HTML code for your Web Interface HIT, or download files for use with the Command Line Tool.
	</p>
	
	<form method="post" action="index.php">
		
		<p>
			Please fill out the form below, and press generate.
		</p>
	
		<p>
			<label for="externalSurveyURL">External Survey URL:</label>
			<input type="text" name="externalSurveyURL" value=<?php echo "'$externalSurveyURL'"; ?> autofocus="autofocus" required="required">
		</p>

		<p>
			<label for="groupName">Group Name:</label>
			<input type="text" name="groupName" value=<?php echo "'$groupName'"; ?> autofocus="autofocus" required="required">
		</p>

		<h3>For Mechanical Turk Web Interface: </h3>
		
		<p>Generate the HTML code to paste into your HIT using the values specified above.
		Full instructions are on the <a href="http://gideongoldin.github.com/TurkGate/" target="blank">TurkGate Wiki</a>.</p>
	
		<input type="submit" name="generateHTMLCode" value="Generate HTML code">

		<?php
			// Generate a text area with the HTML code
			if(strlen($webTemplateString) > 0) {
				echo '<p>Copy and paste the code below into the source code for your HIT:';
				echo '<p><textarea rows="15" cols="80">';
				echo $webTemplateString;
				echo '</textarea></p>';
			}
		?>
		
		<h3>For Mechanical Turk Command Line Tools: </h3>
		
		<p>Download the files for creating your HIT using the values specified above.
		Full instructions are on the <a href="https://github.com/gideongoldin/TurkGate/wiki/Command-Line-Tools" target="blank">TurkGate Wiki</a>.</p>
		
		<input type="submit" name="downloadCLTFile" value="survey.input">
		<input type="submit" name="downloadCLTFile" value="survey.properties">
		<input type="submit" name="downloadCLTFile" value="survey.question">
	</form>
	
    <h5>
      Powered by <a href='http://gideongoldin.github.com/TurkGate/'>TurkGate</a>
    </h5>
  </body>
</html>