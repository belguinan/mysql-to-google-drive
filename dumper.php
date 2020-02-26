<?php 

if (php_sapi_name() != 'cli') {
	http_response_code(404);
	die();	
}

include __DIR__ . '/vendor/autoload.php';

$databaseName = 'test';
$username = 'root';
$password = 'root';
$backupName = 'backup.gz';
$dsn = 'mysql:host=localhost;dbname=' . $databaseName;

try {
    $dump = new \Ifsnop\Mysqldump\Mysqldump($dsn, $username, $password, [
    	'compress' => \Ifsnop\Mysqldump\Mysqldump::GZIP
    ]);
    $file = $dump->start($backupName);
} catch (\Exception $e) {
	echo $e->getMessage();
    die();
}

$client = getGoogleClient();
$driveService = new Google_Service_Drive($client);

try {
	$fileMetadata = new Google_Service_Drive_DriveFile([
		'name' => $backupName
	]);
	$content = file_get_contents($backupName);
	$file = $driveService->files->create($fileMetadata, [
	    'data' => $content,
	    'uploadType' => 'resumable',
	    'fields' => 'id'
	]);
} catch (\Exception $e) {
	echo $e;
}




































if (! function_exists('getGoogleClient')) {
	
	function getGoogleClient() {
	    $client = new Google_Client();
	    $client->setApplicationName('Google Drive API');
	    $client->setScopes(Google_Service_Drive::DRIVE);
	    $client->setAuthConfig('credentials.json');
	    $client->setAccessType('offline');
	    $client->setPrompt('select_account consent');

	    // Load previously authorized token from a file, if it exists.
	    // The file token.json stores the user's access and refresh tokens, and is
	    // created automatically when the authorization flow completes for the first
	    // time.
	    $tokenPath = 'token.json';
	    if (file_exists($tokenPath)) {
	        $accessToken = json_decode(file_get_contents($tokenPath), true);
	        $client->setAccessToken($accessToken);
	    }

	    // If there is no previous token or it's expired.
	    if ($client->isAccessTokenExpired()) {
	        // Refresh the token if possible, else fetch a new one.
	        if ($client->getRefreshToken()) {
	            $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
	        } else {
	            // Request authorization from the user.
	            $authUrl = $client->createAuthUrl();
	            printf("Open the following link in your browser:\n%s\n", $authUrl);
	            print 'Enter verification code: ';
	            $authCode = trim(fgets(STDIN));

	            // Exchange authorization code for an access token.
	            $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
	            $client->setAccessToken($accessToken);

	            // Check to see if there was an error.
	            if (array_key_exists('error', $accessToken)) {
	                throw new Exception(join(', ', $accessToken));
	            }
	        }
	        // Save the token to a file.
	        if (!file_exists(dirname($tokenPath))) {
	            mkdir(dirname($tokenPath), 0700, true);
	        }
	        file_put_contents($tokenPath, json_encode($client->getAccessToken()));
	    }
	    return $client;
	}
}