<?php

defined('MAX_LOG_SIZE') ? NULL : define("MAX_LOG_SIZE", 10000000); 
defined('MAX_ERROR_LOG_SIZE') ? NULL : define('MAX_ERROR_LOG_SIZE',1000000);// max size of the log to keep (in bytes, 1000000000 ~ 1GB)

defined('SITE_ROOT') ? NULL : define("SITE_ROOT", '[REPLACE WITH YOUR SITE ROOT]');
defined('SITE_ROOT_NO_ENDING_SLASH') ? null : define("SITE_ROOT_NO_ENDING_SLASH", '[REPLACE WITH YOUR SITE ROOT]');
defined('FILE_ROOT') ? NULL : define("FILE_ROOT", '[REPLACE WITH YOUR FILE ROOT]');
defined('FILE_ROOT_NO_ENDING_SLASH') ? null : define("FILE_ROOT_NO_ENDING_SLASH", '[REPLACE WITH YOUR FILE ROOT]');

defined('ITEM_PER_PAGE') ? NULL : define("ITEM_PER_PAGE", 10);
defined('REST_PAGE_MAX') ? NULL : define("REST_PAGE_MAX", 50);
defined('DEVELOPER_TOKEN_MAX') ? NULL : define("DEVELOPER_TOKEN_MAX", 5);
defined('NUM_HIGHLIGHTS_ON_INDEX')? NULL:define("NUM_HIGHLIGHTS_ON_INDEX",1);
defined('MAX_OBJ_ADMIN_DB')?NULL:define('MAX_OBJ_ADMIN_DB',1000);
defined('MAX_FILES_BEFORE_CLEANING')?NULL:define('MAX_FILES_BEFORE_CLEANING',1000);
defined('MAX_OBJS_FOR_EXTENDED_DETAILS')?NULL:define('MAX_OBJS_FOR_EXTENDED_DETAILS',100);
defined("ANNOUNCE_OBJ_THRESHOLD")?NULL:define("ANNOUNCE_OBJ_THRESHOLD",50); 
defined("MAINTENANCE_CLASS")?NULL:define("MAINTENANCE_CLASS","siteMaintenance");


defined('INACTIVE_TIME') ? NULL : define("INACTIVE_TIME", 14400); //timeout in seconds, currently 4 Hours
defined('INACTIVE_TIME_HIDDEN') ? NULL : define("INACTIVE_TIME_HIDDEN", 3600000); //timeout once page loses focus in milliseconds, currently 1 hour


defined('DEFAULT_RATING') ? NULL : define("DEFAULT_RATING", 75);
defined('MIN_FORM_COMPLETION_TIME') ? NULL : define("MIN_FORM_COMPLETION_TIME", 3);
defined('MAX_FILE_UPLOAD_SIZE') ? NULL : define("MAX_FILE_UPLOAD_SIZE",6291456);
defined('MAX_SELECT_OPTIONS') ? NULL : define('MAX_SELECT_OPTIONS',200);

defined('PASSWORD_HASH_METHOD') ? NULL : define('PASSWORD_HASH_METHOD',PASSWORD_DEFAULT);