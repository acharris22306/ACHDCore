<?php

$INIerrorLogFile = FILE_ROOT."logs/errors.txt";

$numObjUsedForPage = 0;
$numSQLUsedForPage = 0;
$daysToKeepSessionFiles = 7;
$inDebugMode       = true;
$useHeaderImg = false;
$headerImgLocation = "bg104.jpg";
$headerImgTxt = " ";
$imgPos = "center";
$inStylingMode = false;
$limitIdle = true;
$lastWeekTime = time()-604800;
$quickSearchTitles=array(
	"allMeds"=>"All Medicine Related Items",
	"latest"=>"All Items Catalogued In The Past Week",
	"bookChinese"=>"All Books Related To Chinese Medicine",
	"bookAyervedic"=>"All Books Related To Ayervedic Medicine",
	"bookHomeopathy"=>"All Books Related To Homeopathy",
);
$quickSearchTypesCode=array(
	"allMeds"=>" nameTitle LIKE '%medicine%' OR keywords LIKE '%medicine%' OR description LIKE '%medicine%' OR objectType='medicine' ",
	"latest"=>" dateAdded>=".$lastWeekTime." ",
	"bookChinese"=>" (nameTitle LIKE '%Chinese%' OR keywords LIKE '%Chinese%' OR description LIKE '%Chinese%') AND objectType='book' ",
	"bookAyervedic"=>" (nameTitle LIKE '%Ayervedic%' OR keywords LIKE '%Ayervedic%' OR description LIKE '%Ayervedic%') AND objectType='book' ",
	"bookHomeopathy"=>" (nameTitle LIKE '%homeopathy%' OR keywords LIKE '%homeopathy%' OR description LIKE '%homeopathy%') AND objectType='book' ",

);
$dirsToSkip = array("googleAPI","zips","logs","_ASSETS","fonts","dompdf","phpunit","RxPHP","TCPDF","Scripts","Templates","phpSnips",
					"atom","rss","searchCSVs","searchTXTs","searchJSONs","searchHTMLs","searchPDFs","searchXMLs",
					"objectImages",
					"databaseBackups","__ASSETS","vendor");

$validUserTypes = array("admin","collectionManager","curator","developer");

$orderByVals = array(
	"collectionId,objectNumber ASC"=>"Object Number (Ascending)",
	"collectionId,objectNumber DESC"=>"Object Number (Descending)",
	"objectType ASC"=>"Object Type (Ascending)",
	"objectType DESC"=>"Object Type (Descending)",
	"nameTitle ASC"=>"Object Name/Title (Ascending)",
	"nameTitle DESC"=>"Object Nam/Title (Descending)",
	"collectionId ASC"=>"Collection (Ascending)",
	"collectionId DESC"=>"Collection (Descending)",
	"dateAdded ASC"=>"Date Added (Ascending)",
	"dateAdded DESC"=>"Date Added (Descending)",
	"quantity ASC"=>"Quantity (Ascending)",
	"quantity DESC"=>"Quantity (Descending)",
	"height ASC"=>"Height in Inches (Ascending)",
	"height DESC"=>"Height in Inches (Descending)",
	"width ASC"=>"Width in Inches (Ascending)",
	"width DESC"=>"Width in Inches (Descending)",
	"depth ASC"=>"Depth in Inches (Ascending)",
	"depth DESC"=>"Depth in Inches (Descending)",
	"addedById ASC"=>"Who Added The Object (Ascending)",
	"addedById DESC"=>"Who Added The Object (Descending)",
	"objectCondition ASC"=>"Object Condition (Ascending)",
	"objectCondition DESC"=>"Object Condition (Descending)",
	"timeframe ASC"=>"Object Timeframe (Ascending)",
	"timeframe DESC"=>"Object Timeframe (Descending)",
);
$objectTypes = array(
	"book"=>"Books",
	"container"=>"Containers",
	"cosmetic"=>"Cosmetics",
	"device"=>"Devices",
	"equipment"=>"Equipment",
	"file"=>"Files",
	"medicine"=>"Medicine",
	"imageCollection"=>"Image Collections",
	"miscObject"=>"Miscellaneous",
);
$sqlTableForType = array(
	"book"=>"books",
	"container"=>"containers",
	"cosmetic"=>"cosmetics",
	"device"=>"devices",
	"equipment"=>"equipment",
	"file"=>"files",
	"medicine"=>"medicines",
	"imageCollection"=>"imageCollections",
	"miscObject"=>"miscObjects",
);
$collectionManagerStatus = array(
	"New","Assigned","Inactive"
);
$collectionStatuses =  array("Cataloging","Cataloged");
$bookFormats = array("paperback","hardback","digital","other");
$bookCategories = array("textbook","pharmacology","complementaryAndAlternativeMedicine","materiaMedica","cosmetics","mindBody","diet",
						"nutrition","historyOfPharmacy","dictionary","manual","history","other");

$fileObjectFormats = array("journalArticle","pharmacyRecord","budgets","researchPapers","thesis","other");
$imageCollectionFormats = array("print","negative","slide","digital","other");
$timeframeTitles = array(
	"0_1600"=>"Pre 1600",
	"1600early"=>"Early 17th Century",
	"1600late"=>"Late 17th Century",
	"1700early"=>"Early 18th Century",
	"1700late"=>"Late 18th Century",
	"1800early"=>"Early 19th Century",
	"1800late"=>"Late 19th Century",
	"1900early"=>"Early 20th Century",
	"1900late"=>"Late 20th Century",	
	"2000early"=>"Early 21st Century",	
	"1850s"=>"1850-1859",
	"1860s"=>"1860-1869",
	"1870s"=>"1870-1879",
	"1880s"=>"1980-1989",
	"1890s"=>"1890-1899",
	"1900s"=>"1900-1909",
	"1910s"=>"1910-1919",
	"1920s"=>"1920-1929",
	"1930s"=>"1930-1939",
	"1940s"=>"1940-1949",
	"1950s"=>"1950-1959",
	"1960s"=>"1960-1969",
	"1970s"=>"1970-1979",
	"1980s"=>"1980-1989",
	"1990s"=>"1990-1999",
	"2000s"=>"2000-2009",
	"2010s"=>"2010-2019",
	"2020s"=>"2020-2029",
	"other"=>"Other Timeframe",
	"unknown"=>"Timeframe is unknown",
);

//$dosageForms = array();
/*if ((!isset($_SERVER['HTTPS'])) || ($_SERVER['HTTPS'] == "") || ($_SERVER['HTTPS'] == "off")) {
	$redirect = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	header("Location: $redirect");
}
*/

// automatically makes sure curators are marked as offline if they have not accessed their dashboard for over 8 hours (exact amount can be altered easily) 
// also makes sure that all timesOnline entries that are started over 8 hours ago and have not been closed (end == 0) are closed with the current time
/*SiteMaintenance::cleanUpFilesInDir("files/searchCSVs", 7);
SiteMaintenance::cleanUpFilesInDir("files/searchTXTs", 7); 
SiteMaintenance::cleanUpFilesInDir("files/searchHTMLs", 7);
SiteMaintenance::cleanUpFilesInDir("files/searchPDFs", 7); 

SiteMaintenance::cleanUpFilesInDir("files/zips", 10); 
SiteMaintenance::cleanUpFilesInDir("files/databaseBackups", 30); 

SiteMaintenance::cleanUpFilesInDirMaxFiles("files/searchCSVs",MAX_FILES_BEFORE_CLEANING);
SiteMaintenance::cleanUpFilesInDirMaxFiles("files/searchTXTs",MAX_FILES_BEFORE_CLEANING);
SiteMaintenance::cleanUpFilesInDirMaxFiles("files/searchHTMLs",MAX_FILES_BEFORE_CLEANING);
SiteMaintenance::cleanUpFilesInDirMaxFiles("files/searchPDFs",MAX_FILES_BEFORE_CLEANING);
SiteMaintenance::cleanUpFilesInDirMaxFiles("files/zips",MAX_FILES_BEFORE_CLEANING);
SiteMaintenance::cleanUpFilesInDirMaxFiles("files/databaseBackups",MAX_FILES_BEFORE_CLEANING);*/
$dirsToKeepCleanSavedSearches = array(
	"files/searchCSVs"=>array("daysToKeepFiles"=>7,"cleanupExcess"=>true,"maxFiles"=>MAX_FILES_BEFORE_CLEANING ),
	"files/searchXMLs"=>array("daysToKeepFiles"=>7,"cleanupExcess"=>true,"maxFiles"=>MAX_FILES_BEFORE_CLEANING ),
	"files/searchJSONs"=>array("daysToKeepFiles"=>7,"cleanupExcess"=>true,"maxFiles"=>MAX_FILES_BEFORE_CLEANING ),
	"files/searchTXTs"=>array("daysToKeepFiles"=>7,"cleanupExcess"=>true,"maxFiles"=>MAX_FILES_BEFORE_CLEANING ),
	"files/searchHTMLs"=>array("daysToKeepFiles"=>7,"cleanupExcess"=>true,"maxFiles"=>MAX_FILES_BEFORE_CLEANING ),
	"files/searchPDFs"=>array("daysToKeepFiles"=>7,"cleanupExcess"=>true,"maxFiles"=>MAX_FILES_BEFORE_CLEANING ),
	
);
$dirsToKeepCleanArchives = array(
	"files/zips"=>array("daysToKeepFiles"=>1,"cleanupExcess"=>true,"maxFiles"=>MAX_FILES_BEFORE_CLEANING ),
	"files/zips/objectImages"=>array("daysToKeepFiles"=>30,"cleanupExcess"=>true,"maxFiles"=>MAX_FILES_BEFORE_CLEANING ),
	"files/zips/allImages"=>array("daysToKeepFiles"=>30,"cleanupExcess"=>true,"maxFiles"=>MAX_FILES_BEFORE_CLEANING ),
	"files/zips/fullSite"=>array("daysToKeepFiles"=>365,"cleanupExcess"=>true,"maxFiles"=>MAX_FILES_BEFORE_CLEANING ),
	"files/databaseBackups"=>array("daysToKeepFiles"=>30,"cleanupExcess"=>true,"maxFiles"=>MAX_FILES_BEFORE_CLEANING ),
);

$dirsToKeepCleanFeeds = array(
	"files/atom"=>array("daysToKeepFiles"=>7,"cleanupExcess"=>true,"maxFiles"=>MAX_FILES_BEFORE_CLEANING ),
	"files/rss"=>array("daysToKeepFiles"=>7,"cleanupExcess"=>true,"maxFiles"=>MAX_FILES_BEFORE_CLEANING ),
);

$foldersToArchve = array(
	"images/objectImages"=>"objectImages",
	//""=>"fullSite",

);

/*
SiteMaintenance::backupDatabase();
SiteMaintenance::archiveFolders($foldersToArchve, 7,true);
SiteMaintenance::cleanupFiles($dirsToKeepCleanArchives,1);
*/

$currentTimeINT = time();
$currentTimeTIMESTAMP = strftime("%Y-%m-%d %H:%M:%S", $currentTimeINT);

$autoZoomingEnabled = true;
$memOverhead = memory_get_usage();
$sectionMenuType = "home";
$passwordHashOptions = array(
	'cost'=>12,
);
