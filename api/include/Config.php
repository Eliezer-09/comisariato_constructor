<?php

/**
 * Database config variables
 */

/* define("DB_USERNAME", "induwagen");
define("DB_PASSWORD", "Bonsai!2022");
define("DB_HOST", "192.168.1.153");
define("DB_NAME", "abraldes"); */

// define("DB_USERNAME", "bonsai");
// define("DB_PASSWORD", "Bonsai!2017");
define("DB_USERNAME", "root");
define("DB_PASSWORD", "");
define("DB_HOST", "localhost");
define("DB_NAME", "constructor");

define('RECORD_CREATED_SUCCESSFULLY', 0);
define('RECORD_CREATION_FAILED', -1);
define('RECORD_ALREADY_EXISTED', 2);
define('RECORD_DOES_NOT_EXIST', 3);
define('OPERATION_COMPLETED', 4);
define('ACCESS_DENIED', 5);
define('RECORD_DOES_NOT_APPLY', 6);
define('OPERATION_FULL', 7);
define('RECORD_UPDATED_SUCCESSFULLY', 8);
define('RECORD_UPDATED_FAILED', 9);


//DATOS PARA PODER GENERAR EL TOKEN QUE PERMITE OBTENER LOS DATOS DESDE LAS API
define('USERNAME_API', 'bonsai');
define('PASSWORD_API', 'bon123');
define('TOKEN_API', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiJib25zYWkiLCJqdGkiOiIxMzhmNTY1NS0xNmM4LTRkM2YtYmJkNS0wN2ZhNmE5MzdjZTIiLCJleHAiOjE3Mjg0ODg0OTEsImlzcyI6Imh0dHA6Ly9sb2NhbGhvc3QiLCJhdWQiOiJodHRwOi8vbG9jYWxob3N0In0.Ww0vJNrVkKvTyXDfJSezmJyEg2gA_ic32MoekfDVaBo');
