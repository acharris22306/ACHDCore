<?php
// If it's going to need the database, then it's 
// probably smart to require it before we start.
namespace ACHD\Core
class FormCreator{
	public $obj;
	public $formName;
	public $formAction;
	public $redirectOnIncomplete;
	public $redirectOnSuccess;
	public $requiredFields=array();
	public $emails=array();
	public $phones=array();
	public $fieldsWithMaxLengths=array();
	public $fieldsWithMinLengths=array();
	public $isNew=true;
	public $passwords=array();
	public $numerics=array();
	public $dates=array();
	public $confirmRequired=array();
	public $customeLiveValidationCheck=array();
	public $buttonSets=array();
	public $fields=array();
	private $fieldTypes = array();
	private $selectVals = array();
	private $radioCheckboxVals = array();

	function __construct( $formName,$formAction, $redirectOnIncomplete, $redirectOnSuccess, $fields, $requiredFields,  $isNew=true, $emails=array(), $phones=array(), $fieldsWithMaxLengths=array(), $fieldsWithMinLengths=array(), $passwords=array(), $numerics=array(), $dates=array(), $confirmRequired=array(), $buttonSets=array(), $customeLiveValidationCheck=array()){
		$this->formName=$formName;
		$this->formAction=$formAction;
		$this->redirectOnIncomplete=$redirectOnIncomplete;
		$this->redirectOnSuccess=$redirectOnSuccess;
		$this->fields=$fields;
		$this->requiredFields=$requiredFields;
		$this->isNew=$isNew;
		$this->emails=$emails;
		$this->phones=$phones;
		$this->fieldsWithMaxLengths=$fieldsWithMaxLengths;		
		$this->fieldsWithMinLengths=$fieldsWithMinLengths;
		$this->passwords=$passwords;
		$this->numerics=$numerics;
		$this->dates=$dates;
		$this->confirmRequired=$confirmRequired;
		$this->buttonSets=$buttonSets;
		$this->customeLiveValidationCheck=$customeLiveValidationCheck;
		$this->fieldTypes = isset($_SESSION['fieldTypes'])?$_SESSION['fieldTypes']:array();
		$this->selectVals = isset($_SESSION['selectVals'])?$_SESSION['selectVals']:array();
		$this->radioCheckboxVals = isset($_SESSION['radioCheckboxVals'])?$_SESSION['radioCheckboxVals']:array();
	}
	public function beginForm($pageHeading, $isPost=true, $hasFiles=false){
		echo "<form id=\"mainForm\" action=\"".$this->formAction."\" method=\"";
		if($isPost==true){
			echo "post";
		}else{
			echo "get";
		}
		echo "\" name=\"".$this->formName."\" ";
		if($hasFiles==true){
			echo "enctype=\"multipart/form-data\">";
		}else{
			echo ">";
		}
  		echo "\n <label id=\"pageHeading\">".$pageHeading."</label>\n <br/>\n";
		if ($isPost == true){
			echo "<input type=\"hidden\" name=\"formStartTime\" value=\"" . time() . "\">";
		}
	}
	public function formSectionHeading($sectionText){
		echo("<label class=\"formSectionTitle\">".$sectionText."</label>\n <br/>\n");
	}
	public function endForm($titleOfSubmit="Submit"){
		csrfTokenTag();
		echo "<input type=\"submit\" value=\"".$titleOfSubmit."\"  name=\"submit\" id=\"submit\" >\n  </form>\n";
		$this->outputJQueryButtonSets();
		$this->outputLiveValidations();
		$_SESSION['fieldTypes']=$this->fieldTypes;
		$_SESSION['selectVals']=$this->selectVals;
		$_SESSION['radioCheckboxVals']=$this->radioCheckboxVals;		
	}
	public function outputLiveValidations(){
		echo "<script type=\"text/javascript\">\n";
		foreach($this->fields as $field){
			echo " var ".$field." = new LiveValidation (\"".$field."\", { validMessage: \"√\" });\n";
		}		
		foreach($this->requiredFields as $field){
			echo $field.".add( Validate.Presence, {  failureMessage: \"Required\" } );\n";
		}
		foreach($this->phones as $field){
			echo $field.".add( Validate.Format, { pattern: /^((\(\d{3}\))|(\d{3}))([- ]?)\d{3}([- ]?)\d{4}$/, failureMessage: \"Must be in (xxx)-xxx-xxxx or xxxxxxxxxx or xxx xxx xxxx or xxx-xxx-xxxx format\" } );\n";
		}
		foreach($this->emails as $field){
			echo $field.".add( Validate.Email, {failureMessage: \"Invalid Email\"} );\n";
		}
		foreach($this->dates as $field){
			echo $field.".add( Validate.Format, {pattern: /\d\d\/\d\d\/\d\d\d\d/, failureMessage: \"Must be in mm/dd/yyyy format\"} );\n";
		}
		foreach($this->fieldsWithMaxLengths as $field => $max){
			echo $field.".add( Validate.Length, { maximum: ".$max.", tooLongMessage: \"Must be less than ".$max." characters long\"} );\n";
		}
		foreach($this->fieldsWithMinLengths as $field => $min){
			echo $field.".add( Validate.Length, { minimum: ".$min.", tooShortMessage: \"Must be more than ".$min." characters long\"} );\n";
		}
		foreach($this->confirmRequired as $field => $matchingField){
			$matchingFieldTitle=fieldnameAsText($matchingField);
			echo $field.".add( Validate.Confirmation, { match: '".$matchingField."' , failureMessage: \"Must Match ".$matchingFieldTitle."\"} );\n";
		}
		foreach($this->customeLiveValidationCheck as $field => $customValidation){
			echo $field.".add( Validate.".$customValidation."\"} );\n";
		}
		foreach($this->passwords as $field){
			echo $field.".add( Validate.Format, { pattern: /^\S{8,}$/, failureMessage: \"Password is not strong enough. Increase the length, add numbers, or add special characters (such as $, %, !, # or ?)\" } );\n";
		}
		foreach($this->numerics as $field=>$restrictions){
			echo $field.".add( Validate.Numericality, { ".$restrictions." } );\n";
		}
	
		echo "\n</script>";
	}
	
	public function insertTextInput($fieldName, $isRequired=true, $type = "text",  $saveAndRestore = true, $callbackFunctions = " ", $otherAttributes=" "){
		$label=fieldnameAsText($fieldName);
		$this->fieldTypes[$fieldName] = $type;
		echo "<label for=\"".$fieldName."\" class=\"formItemLabel\">".$label;
		if($isRequired==true){
			echo "<span class=\"requiredIndicator\">*</span>";
		}
		echo "</label> \n <input type=\"".$type."\" class=\"inputText\" name=\"".$fieldName."\" id=\"".$fieldName."\" autocomplete=\"on\" ".$callbackFunctions." value=\"";
		if ($saveAndRestore ==true){
			if($this->isNew==true){
				echo $_POST[$fieldName];
			}else if($this->isNew==false){
				echo $this->obj->$fieldName;
				
			}
		}
		echo "\" ".$otherAttributes." >\n <br/>\n";
		//if($type == "number"){
//			echo "<script>\n $( function() { \n    $( \"#".$fieldName."\" ).spinner({ \n  step: 0.01,\n  numberFormat: \"n\"\n  });\n });\n </script> \n";
//		}
	}
	public function insertTextInputAltField($fieldName, $altField, $isRequired=true, $type = "text",  $saveAndRestore = true, $callbackFunctions = " ", $otherAttributes=" "){
		$label=fieldnameAsText($altField);
		$this->fieldTypes[$fieldName] = $type;
		echo "<label for=\"".$fieldName."\" class=\"formItemLabel\">".$label;
		if($isRequired==true){
			echo "<span class=\"requiredIndicator\">*</span>";
		}
		echo "</label> \n <input type=\"".$type."\" class=\"inputText\" name=\"".$fieldName."\" id=\"".$fieldName."\" autocomplete=\"on\" ".$callbackFunctions." value=\"";
		if ($saveAndRestore ==true){
			if($this->isNew==true){
				echo $_POST[$fieldName];
			}else if($this->isNew==false){
				echo $this->obj->$altField;
				
			}
		}
		echo "\" ".$otherAttributes." >\n <br/>\n";
		//if($type == "number"){
//			echo "<script>\n $( function() { \n    $( \"#".$fieldName."\" ).spinner({ \n  step: 0.01,\n  numberFormat: \"n\"\n  });\n });\n </script> \n";
//		}
	}
	public function insertTextInputWithConfirm($fieldName, $isRequired=true, $type = "text",  $saveAndRestore = true, $callbackFunctions = " ", $preConfirmBlock=" ", $otherAttributes = " "){
		$confirmLabel = "Confirm ". fieldnameAsText($fieldName);
		$confirmFieldName = "confirm".ucwords($fieldName);
		$this->fieldTypes[$fieldName] = $type;
		echo $preConfirmBlock;
		$this->insertTextInput($fieldName, $isRequired, $type, $saveAndRestore, $callbackFunctions, $otherAttributes );
		$this->insertTextInput($confirmFieldName, $isRequired, $type, false, " ", $otherAttributes);
	}
	public function additionalInfo($info){
		echo "<p class=\"additionalFormInfo\">".$info."</p><br/>\n";
	}
	public function insertDate($fieldName, $isRequired=true, $doLimit=false, $yearRange ="-78:-18", $maxDate = "-18y" ){
		$label=fieldnameAsText($fieldName);
		$this->fieldTypes[$fieldName] = "date";
		echo "<label for=\"".$fieldName."\" class=\"formItemLabel\">".$label;
		if($isRequired==true){
			echo "<span class=\"requiredIndicator\">*</span>";
		}
		echo "</label> \n <input type=\"text\" class=\"inputText\" name=\"".$fieldName."\" id=\"".$fieldName."\" autocomplete=\"on\" value=\"";
		if($this->isNew==true){
			echo $_POST[$fieldName];
		}else if($this->isNew==false){
			echo $this->obj->$fieldName;
		}else{
			echo "00/00/0000";
		}
		echo "\">\n <br/>\n <script type=\"text/javascript\">\n $(function() { $( \"#".$fieldName."\" ).datepicker({ \n 
			onClose: function(){ \n 
			var e = $.Event(\"keyup\"); \n
                e.keyCode = 13; \n
                $(this).trigger(e); \n
			}, \n
			changeMonth: true, \n
			changeYear: true, \n
			constrainInput: true, \n
			dateFormat: \"mm/dd/yy\", \n";
		if($doLimit==true){
			echo "yearRange: \"".$yearRange."\", \n maxDate: \"".$maxDate."\", \n";
		}
		echo "showOtherMonths: true, \n
			selectOtherMonths: true \n
    		}); \n 
			}); \n
			</script> \n
 			<br/> \n";
	}
	public function insertFile($fieldName, $fileFormats="application/pdf,application/msword,application/rtf,text/plain,image/*", $maxSize=MAX_FILE_UPLOAD_SIZE, $isRequired=true){
		$label=fieldnameAsText($fieldName);
		$this->fieldTypes[$fieldName] = "file";
		$sizeInMb = round(($maxSize/1048576),2);
		echo "<div class=\"fileUploads\">\n <label for=\"".$fieldName."\" class=\"formItemLabel\">".$label;
		if($isRequired==true){
			echo "<span class=\"requiredIndicator\">*</span>";
		}
		echo "</label> \n <br/> \n <input type=\"hidden\" name=\"MAX_FILE_SIZE_".$fieldName."\" value=\"".$maxSize."\"> \n <input type=\"file\" name=\"".$fieldName."\" id=\"".$fieldName."\" accept=\"".$fileFormats."\" value=\"";
		if($this->isNew==true){
			echo $_POST[$fieldName];
		}else if($this->isNew==false){
			echo $this->obj->$fieldName;
		}
		echo "\">\n </div>\n";
		$this->additionalInfo("NOTE: File size must be under ".$sizeInMb."MB");
	}
	public function insertSelect($fieldName, $values, $valuesAsLabels = true, $isRequired = true, $callbackFunctions = " ",$allowedVals = "specified"){
		$label=fieldnameAsText($fieldName);
		$this->fieldTypes[$fieldName] = "select";
		$this->selectVals[$fieldName]['allowed']=$allowedVals;
		echo "<label for=\"".$fieldName."\" class=\"formItemLabel\">".$label;
		if($isRequired==true){
			echo "<span class=\"requiredIndicator\">*</span>";
		}
		
		echo "</label> \n <select name=\"".$fieldName."\" ".$callbackFunctions."  id=\"".$fieldName."\"> \n <option label=\"Select One\"></option> \n";
		if ($valuesAsLabels == true){
			$this->selectVals[$fieldName]['vals']=$values;
			foreach ($values as $value){
				$valTitle=parseCamelCaseUC($value);
				echo "<option value=\"".$value."\" ";
				if($this->isNew==true){
					if ($_POST[$fieldName] == $value) { echo 'selected'; };
				}else if($this->isNew==false){
					if ($this->obj->$fieldName == $value) { echo 'selected'; };
				}
				echo " >".$valTitle."</option> \n ";
			}
		}else if ($valuesAsLabels == false){
			$valsToAllow = array();
			foreach ($values as $value => $valueLabel){
				$valsToAllow[]=$value;
				echo "<option value=\"".$value."\" ";
				if($this->isNew==true){
					if ($_POST[$fieldName] == $value) { echo 'selected'; };
				}else if($this->isNew==false){
					if ($this->obj->$fieldName == $value) { echo 'selected'; };
				}
				echo " >".$valueLabel."</option> \n ";
			}
			$this->selectVals[$fieldName]['vals']=$valsToAllow;

		}
		echo "</select>\n <br/> \n ";
	}
	public function insertTextArea($fieldName, $title, $placeholder = "Enter text here...", $isRequired=true, $cols=75, $rows=5){
		$fieldNameText = fieldnameAsText($fieldName);
		$this->fieldTypes[$fieldName] = "textarea";
		echo "<label class=\"formItemLabel\" for=\"".$fieldName."\">".$fieldNameText;
		if($isRequired==true){
			echo "<span class=\"requiredIndicator\">*</span>";
		}
		echo "</label>\n <textarea name=\"".$fieldName."\" cols=\"".$cols."\" rows=\"".$rows."\" id=\"".$fieldName."\" placeholder=\"".$placeholder."\" title=\"".$title."\">\n";
		if($this->isNew==true){
			echo $_POST[$fieldName];
		}else if($this->isNew==false){
			echo $this->obj->$fieldName;
		}
		echo "</textarea>\n <br/> \n<br/>\n";
	}
	public function insertCheckboxes($fieldName, $fieldTitle, $valuesAndTitles, $isRequired=true){
		$this->fieldTypes[$fieldName] = "checkbox";
		echo "<div class=\"formButtonSet\" > \n <h4>".$fieldTitle;
		if($isRequired==true){
			echo "<span class=\"requiredIndicator\">*</span>";
		}
		echo "</h4>\n <div id=\"".$fieldName."Div\"> <fieldset>\n";
		foreach ($valuesAndTitles as $value => $title){
			$this->radioCheckboxVals[$fieldName][]=$value;
			echo "<label for=\"".$value."\">".$title."</label> \n <input type=\"checkbox\" name=\"".$fieldName."[]\" id=\"".$value."\" value=\"".$value."\" ";
			if($this->isNew==true){
				if (hasInclusionIn($value, $_POST[$fieldName])) {
					echo 'checked';
				}
			}else if($this->isNew==false){
				if ($this->obj->$value == 1) {
					echo 'checked';
				}
			}
			echo " > \n";
		}
		echo "</fieldset></div> \n </div> \n ";
	}
	public function insertRadioButtons($fieldName, $fieldTitle, $valuesAndTitles, $required=true){
		$this->fieldTypes[$fieldName] = "radio";
		echo "<div class=\"formButtonSet\" >\n <h4>".$fieldTitle;
		if($isRequired==true){
			echo "<span class=\"requiredIndicator\">*</span>";
		}
		echo "</h4>\n <div id=\"".$fieldName."Div\"> <fieldset>\n";
		foreach ($valuesAndTitles as $value => $title){
			$this->radioCheckboxVals[$fieldName][]=$value;
			echo "<label for=\"".$fieldName."_".$value."\">".$title."</label> \n <input type=\"radio\" name=\"".$fieldName."\" id=\"".$fieldName."_".$value."\" value=\"".$value."\" ";
			if($this->isNew==true){
				if ($_POST[$fieldName] == $value) {
					echo 'checked';
				}
			}else if($this->isNew==false){
				if ($this->obj->$fieldName == $value) {
					echo 'checked';
				}
			}
			echo " > \n";
		}
		echo "</fieldset></div> \n </div> \n ";
	}
	public function insertRadioButtonsNonstandard($fieldName, $fieldTitle, $valuesAndTitles, $required=true){
		$this->fieldTypes[$fieldName] = "radio";
		echo "<div class=\"formButtonSet\" >\n <h4>".$fieldTitle;
		if($isRequired==true){
			echo "<span class=\"requiredIndicator\">*</span>";
		}
		echo "</h4>\n <div id=\"".$fieldName."Div\"><fieldset> \n";
		foreach ($valuesAndTitles as $value => $title){
			$this->radioCheckboxVals[$fieldName][]=$value;
			echo "<label for=\"".$fieldName."_".$value."\">".$title."</label> \n <input type=\"radio\" name=\"".$fieldName."\" id=\"".$fieldName."_".$value."\" value=\"".$value."\" ";
			if ($_POST[$fieldName] == $value) {
				echo 'checked';
			}
			echo " > \n";
		}
		echo "</fieldset></div> \n </div> \n ";
	}
	public function outputJQueryButtonSets(){
		$fieldNamesForButtons = array();
		foreach($this->fieldTypes as $field=>$type){
			$fieldNamesForButtons["#".$field."Div"]=$type;
		}
		echo "<script type=\"text/javascript\"> \n $(function() { \n ";
		foreach($this->buttonSets as $buttonsets){
				echo "$( \"".$buttonsets." input\" ).checkboxradio(); \n ";
		}
		//	echo "$( \"input\" ).checkboxradio(); \n ";
		
		echo " }); \n </script>\n ";
	}
	public function validateItems(){
		global $errors;
		global $session;
		$currentTime = time();
		if (isset($_POST['formStartTime'])&&!empty($_POST['formStartTime'])){
			$timeToCompleteForm= $currentTime - $_POST['formStartTime'];
			if($timeToCompleteForm<MIN_FORM_COMPLETION_TIME){
				$msg = "Form: ".$this->formName.", Time taken: ".$timeToCompleteForm;
				CoreFunctions::logAction("blockRobots", "Blocked Robot", $msg);
				$session->message('Are you a robot? You completed that form too quickly.');
				CoreFunctions::redirectTo($this->redirectOnIncomplete);
			}
		}else{
			Validation::validatePresences($this->requiredFields);
			Validation::validatePasswordSecurity($this->passwords,$this->requiredFields);
			Validation::validatePhoneNumbers($this->phones,$this->requiredFields);
			Validation::validateMaxLengths($this->fieldsWithMaxLengths);
			Validation::validateMinLengths($this->fieldsWithMinLengths,$this->requiredFields);
			Validation::validateNumericsAssoc($this->numerics,$this->requiredFields);
			Validation::validateEmails($this->emails,$this->requiredFields);
			Validation::validateConfirmMatches($this->confirmRequired);			
			Validation::validateSelectVals($this->selectVals);		
			Validation::validateRadioCheckboxVals($this->radioCheckboxVals,$this->fieldTypes);			
		}
		
	}
	
	public function processFile($fieldName, $newFileNameBase, $newFileDirectory, $fileType, $fileExtensions, $fileDescription, $checkIsImage=true, $isRequired=true, $thumbWidth = 150){
		if (isset($_FILES)) {
			if (isset($_FILES[$fieldName])) {
				if ($_FILES[$fieldName]['error'] == 0) {
					$maxSizeForFileField = "MAX_FILE_SIZE_".$fieldName;
					$tempName    = $_FILES[$fieldName]['tmp_name'];
					$fileName       = $_FILES[$fieldName]['name'];
					$targetFile     = $fieldName."_".$newFileNameBase . "_" . time()."_". rand(1, 11111111) ;
					$fileExtension  = fileExtension($fileName);
					$newFileName       = "{$targetFile}.{$fileExtension}";
					if (validateFile($fieldName, $_POST[$maxSizeForFileField], $fileType, $fileExtensions, $checkIsImage)) {
						if (move_uploaded_file($tempName, $newFileDirectory . $newFileName)) {
							$this->obj->$fieldName = $newFileName;
							if($checkIsImage){
								if(CoreFunctions::generateThumbnail($newFileDirectory . $newFileName, $thumbWidth, 90, $newFileDirectory)){
									if(realpath($newFileDirectory) == FILE_ROOT."images/objectImages"){
										SiteMaintenance::backupDirAsZip(FILE_ROOT."images/objectImages/","objectImages");
									}
									return $newFileName;
								}else{
									$msg = implode(",",$_FILES[$fieldName]) . " ". $newFileDirectory . $newFileName;
									CoreFunctions::logAction("debugFiles", "file error thumb", $msg);
									$this->onFormIncomplete("There was a problem processing your ". $fileDescription. ". Please try again. thumb");
								}
							}else{
								return $newFileName;
							}
						} else {
							$msg = implode(",",$_FILES[$fieldName]) . " ". $newFileDirectory . $newFileName;
							CoreFunctions::logAction("debugFiles", "file error moving", $msg);
							$this->onFormIncomplete("There was a problem processing your ". $fileDescription. ". Please try again. not moved");
						}
					} else {
						CoreFunctions::logAction("debugFiles", "file not validated", implode(",",$_FILES[$fieldName]));
						$this->onFormIncomplete("There was a problem processing your ". $fileDescription. ". Please try again. not valid");
					}
				} elseif (($_FILES[$fieldName]['error'] == 4)&&($isRequired==true)) {
					if( $checkIsImage == true){
						$msg = implode(",",$_FILES[$fieldName]) . " ". $newFileDirectory . $newFileName;
						CoreFunctions::logAction("defaultUsed", "default image used", $msg);
						return "default.jpg";
					}else{
						$this->onFormIncomplete("You MUST upload your ". $fileDescription. ".");
					}
				} else {
					$this->onFormIncomplete("There was a problem processing your ". $fileDescription. ". Please try again. " . fileUploadError($_FILES[$fieldName]['error']));
				}
			} elseif($isRequired==true) {
				if( $checkIsImage == true){
					$msg = implode(",",$_FILES[$fieldName]) . " ". $newFileDirectory . $newFileName;
					CoreFunctions::logAction("defaultUsed", "default image used", $msg);
					return "default.jpg";
				}else{
					$this->onFormIncomplete("You MUST upload your ". $fileDescription. ".");
				}
			}
		} elseif($isRequired==true){
			$this->onFormIncomplete('You must upload all required files.');
		}
	}
	public function processFileReplacement($fieldName, $newFileNameBase, $newFileDirectory, $fileType, $fileExtensions, $fileDescription, $checkIsImage=true, $thumbWidth = 150 ){
		if (isset($_FILES)) {
			if (isset($_FILES[$fieldName])) {
				if ($_FILES[$fieldName]['error'] == 0) {
					$maxSizeForFileField = "MAX_FILE_SIZE_".$fieldName;
					$tempName    = $_FILES[$fieldName]['tmp_name'];
					$fileName       = $_FILES[$fieldName]['name'];
					$targetFile     = $fieldName."_".$newFileNameBase . "_" . time()."_". rand(1, 11111111) ;
					$fileExtension  = fileExtension($fileName);
					$newFileName       = "{$targetFile}.{$fileExtension}";
					if (validateFile($fieldName, $_POST[$maxSizeForFileField], $fileType, $fileExtensions, $checkIsImage)) {
						if (move_uploaded_file($tempName, $newFileDirectory . $newFileName)) {
							$this->obj->$fieldName = $newFileName; 
							if($checkIsImage){
								if(CoreFunctions::generateThumbnail($newFileDirectory . $newFileName, $thumbWidth, 90, $newFileDirectory)){
									if(realpath($newFileDirectory) == FILE_ROOT."images/objectImages"){
										SiteMaintenance::backupDirAsZip(FILE_ROOT."images/objectImages/","objectImages");
									}
									return $newFileName;
								}else{
									$msg = implode(",",$_FILES[$fieldName]) . " ". $newFileDirectory . $newFileName;
									CoreFunctions::logAction("debugFiles", "file error thumb", $msg);
									$this->onFormIncomplete("There was a problem processing your ". $fileDescription. ". Please try again. thumb");
								}
							}else{
								return $newFileName;
							}
							
							
						} else {
							$msg = implode(",",$_FILES[$fieldName]) . " ". $newFileDirectory . $newFileName;
							CoreFunctions::logAction("debugFiles", "file error 1", $msg);
							$this->onFormIncomplete("There was a problem processing your ". $fileDescription. ". Please try again. 1");
						}
					} else {
						CoreFunctions::logAction("debugFiles", "file not validated", implode(",",$_FILES[$fieldName]));
						$this->onFormIncomplete("There was a problem processing your ". $fileDescription. ". Please try again. 2");
					}
				} elseif (($_FILES[$fieldName]['error'] == 4)) {
						
				} else {
					$this->onFormIncomplete("There was a problem processing your ". $fileDescription. ". Please try again. " . fileUploadError($_FILES[$fieldName]['error']));
				}
			}
		} 
	}
	public function onFormIncomplete($message="There are errors in this form. Please correct them and resubmit"){
		global $session;
		$session->message($message);
		CoreFunctions::redirectTo($this->redirectOnIncomplete);
	}
	public function onFormCompletion($message="You have successfully completed this form!"){
		global $session;
		$session->confirmation($message);
		CoreFunctions::redirectTo($this->redirectOnSuccess);
	}
	public function setField($fieldName,$value){
		$this->obj->$fieldName = $value;
	}
	
}
