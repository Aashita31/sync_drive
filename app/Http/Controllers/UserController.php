<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;


class UserController extends Controller
{
		public $gClient;
		public function __construct(){
			$google_redirect_url = route('glogin');
			$this->gClient = new \Google_Client();
			$this->gClient->setApplicationName(config('services.google.app_name'));
			$this->gClient->setClientId(config('services.google.client_id'));
			$this->gClient->setClientSecret(config('services.google.client_secret'));
			$this->gClient->setRedirectUri($google_redirect_url);
			$this->gClient->setDeveloperKey(config('services.google.api_key'));
			$this->gClient->setScopes(array(               
				'https://www.googleapis.com/auth/drive.file',
				'https://www.googleapis.com/auth/drive'
			));
			$this->gClient->setAccessType("offline");
			$this->gClient->setApprovalPrompt("force");
		}

		public function googleLogin(Request $request)  {
			$google_oauthV2 = new \Google_Service_Oauth2($this->gClient);
			if ($request->get('code')){
				$this->gClient->authenticate($request->get('code'));
				$request->session()->put('token', $this->gClient->getAccessToken());
			}
			if ($request->session()->get('token'))
			{
				$this->gClient->setAccessToken($request->session()->get('token'));
			}
			if ($this->gClient->getAccessToken())
			{
				//For logged in user, get details from google using acces
				$user_id = User::where('email','sahani.aashita31@gmail.com')->first();
				$user=User::find($user_id->id);
				$user->access_token=json_encode($request->session()->get('token'));
				$user->save();               
				dd("Successfully authenticated");
			} else
			{
				//For Guest user, get google login url
				$authUrl = $this->gClient->createAuthUrl();
				return redirect()->to($authUrl);
			}
		}
		public function uploadFileUsingAccessToken(){
			$upload_array = ['Default - Workplace' => [
					'Test1' => [
							'0' => "tasklistfiles/2530/tasklist_216190942753.png",
							'1' => "tasklistfiles/2530/tasklist_516190942791.txt"
						],
					'Test2' => [
						'0' => "tasklistfiles/2550/tasklist_216190882045.txt"
					]
				]
			];
			
			$user_id = User::where('email','sahani.aashita31@gmail.com')->first();
			$service = new \Google_Service_Drive($this->gClient);
			/* Refresh login token */
				$user=User::find($user_id->id);
				$this->gClient->setAccessToken(json_decode($user->access_token,true));
				if ($this->gClient->isAccessTokenExpired()) {
					// save refresh token to some variable
					$refreshTokenSaved = $this->gClient->getRefreshToken();
					// update access token
					$this->gClient->fetchAccessTokenWithRefreshToken($refreshTokenSaved);               
					// pass access token to some variable
					$updatedAccessToken = $this->gClient->getAccessToken();
					// append refresh token
					$updatedAccessToken['refresh_token'] = $refreshTokenSaved;
					//Set the new acces token
					$this->gClient->setAccessToken($updatedAccessToken);
					$updatedAccessToken = $user->update($updatedAccessToken);
				}else{
					$updatedAccessToken = $user->access_token;
				}
			/*End Refresh login token */
			
			/*Check if folder exsist or not if not then create*/
				/* 1-Thing folder name and id **/
			   $oneThingFolder = $this->getOneThingFolder('root');
						if($oneThingFolder[1] == '1-Thing')
						{
							// Get 1-thing folder id   
							$onething_folder_id = $oneThingFolder[0];//$this->getParentId('root');//1Kbfoaq_2bn6G4uHWOZO13Q96IbNvyBvZ
								/* Add workspace */
								if($onething_folder_id){
									/* Get list of folders(workspace) within 1-thing folder */
									$listFolder = $this->listFolder($onething_folder_id);
									if($listFolder == []){
										foreach($upload_array as $item =>$key){
											$workspace_folder_name = $item;
										}
											$workspace = $this->addSubFolder($onething_folder_id, $workspace_folder_name);
											$listFolder = $this->listFolder($onething_folder_id);
									}
									/* check and upload */
									if($listFolder){
										foreach($upload_array as $item =>$key){
											$workspace_folder_name = $item;
													// *check if worplace folder exsist */
													if(in_array($workspace_folder_name, $listFolder))
													{
														echo"<pre>";
														print_r($workspace_folder_name);
														if($workspace_folder_name){
															// Get folder id
															$workspace_folder_id = $this->getFolderId($onething_folder_id,$workspace_folder_name);
															foreach( $key as $list => $file_key)
															{
																$tasklist_folder_name = $list;
																	echo "<pre>";
																	print_r($tasklist_folder_name);
																/* check if folder exsist */
																$task_list_folder = $this->listFolder($workspace_folder_id);
																if($task_list_folder == []){
																	$task_folder_id = $this->addSubFolder($workspace_folder_id, $tasklist_folder_name);
																	$task_folder_id = $this->getFolderId($workspace_folder_id,$tasklist_folder_name);
																}else{
																		// *check if task folder exsist */
																		if(in_array($tasklist_folder_name, $task_list_folder))
																		{
																			// Get folder id
																			$task_folder_id = $this->getFolderId($workspace_folder_id,$tasklist_folder_name);
																			// break;
																		}else{
																			/* Create task folder */
																			$task_folder_id = $this->addSubFolder($workspace_folder_id, $tasklist_folder_name);
																			$task_folder_id = $this->getFolderId($workspace_folder_id,$tasklist_folder_name);
																			// break;
																		}
																		
																}
															
																/* Upload file in task folder */
																foreach($file_key as $task_data){
																	$tasklist_folder_path = $task_data;
																	$tasklist_folder_name = explode("/",$tasklist_folder_path);
																	
																	// * Check if file exsist before upload (No duplicate file)*/
																	$task_files = $this->listFile($workspace_folder_id);
																		echo "<pre>";
																		print_r($task_files);
																	if($task_files == [])
																	{
																		if(in_array($tasklist_folder_name[2], $task_files)){
																			echo "<pre>";
																			print_r("Uploaded previously..!!");
																			// $task_id = $this->uploadFile($task_folder_id, $tasklist_folder_path);
																		}else{
																			echo "<pre>";
																			print_r($tasklist_folder_path);
																			$task_id = $this->uploadFile($task_folder_id, $tasklist_folder_path);
																		}
																	}else{
																		if(in_array($tasklist_folder_name[2], $task_files)){
																			echo "<pre>";
																			print_r("Uploaded previously..!!");
																				// $task_id = $this->uploadFile($task_folder_id, $tasklist_folder_path);
																		}else{
																			echo "<pre>";
																			print_r($tasklist_folder_path);
																			$task_id = $this->uploadFile($task_folder_id, $tasklist_folder_path);
																		}
																	}
																	
																	
																}
															}
														}
													}else{
															return("1.2 something went wrong..!!");
													}
											}
									}else{
										return("1.1 something went wrong..!!");
									}  
								}else{
									return("1.0 something went wrong..!!");
								} 
							}else{

						}
				
		}

		public function getParentId($id){
			$query = "mimeType='application/vnd.google-apps.folder' and '".$id."' in parents and trashed=false";
			$optParams = [
				'fields' => 'files(id, name)',
				'q' => $query
			];
			$service = new \Google_Service_Drive($this->gClient);
			$user=User::find(1);
			$this->gClient->setAccessToken(json_decode($user->access_token,true));
			$results = $service->files->listFiles($optParams);
			// return $results->getFiles();
			if (count($results->getFiles()) == 0) {
			   return 0;
			} else {
				foreach ($results->getFiles() as $file) {
					//  $file->getID()
					if($file->getName() == '1-Thing')
					{
						return $file->getID();
					}
				}
				
			}
		}
	 
		public function getOneThingFolder($id){
			$query = "mimeType='application/vnd.google-apps.folder' and '".$id."' in parents and trashed=false";
			$optParams = [
				'fields' => 'files(id, name)',
				'q' => $query
			];
			$service = new \Google_Service_Drive($this->gClient);
			$user=User::find(1);
			$this->gClient->setAccessToken(json_decode($user->access_token,true));
			$results = $service->files->listFiles($optParams);
			if (count($results->getFiles()) == 0) {
				$oneThingFolder = 'undefine';
			} else {
				foreach ($results->getFiles() as $file) {
					//  $file->getID()
					if($file->getName() == '1-Thing')
					{
						$oneThingFolder = array($file->getID(), $file->getName());
						break; 
					}else{
						$oneThingFolder = 'undefine';
					}
				}    
			}
			if($oneThingFolder == 'undefine'){
				/* create 1-thing folder */
				$folder = $this->addSubFolder('root','1-Thing');
				$oneThingFolder = array($folder, '1-Thing');
				return $oneThingFolder;
			}else{
				return $oneThingFolder ;
			}
		}

	public function addSubFolder($parent_folder_id, $workspace){
		// $parent_folder_id = '17TC7UnSdjPHFApeeBxub2zbgHzcRPF9a';
		$service = new \Google_Service_Drive($this->gClient);
		$folder = new \Google_Service_Drive_DriveFile();
		$folder->setName($workspace);
		$folder->setMimeType('application/vnd.google-apps.folder');
		if( !empty( $parent_folder_id ) ){
			$folder->setParents( [ $parent_folder_id ] );        
		}
		$result = $service->files->create( $folder );
		$folder_id = null;
		if( isset( $result['id'] ) && !empty( $result['id'] ) ){
			$folder_id = $result['id'];
		}
		$sub_folder_id = $folder_id;
		return $sub_folder_id;
	}

	public function listFolder($id){
		$query = "mimeType='application/vnd.google-apps.folder' and '".$id."' in parents and trashed=false";
		$optParams = [
			'fields' => 'files(id, name)',
			'q' => $query
		];
		$service = new \Google_Service_Drive($this->gClient);
		$user=User::find(1);
		$this->gClient->setAccessToken(json_decode($user->access_token,true));
		$results = $service->files->listFiles($optParams);
		if (count($results->getFiles()) == 0) {
		   return [];
		} else {
			foreach ($results->getFiles() as $file) {
				//  $file->getID()
				 $files[] = $file->getName();
			}
			return $files;
		}
	}

	public function getFolderId($id, $folder_name){
		$query = "mimeType='application/vnd.google-apps.folder' and '".$id."' in parents and trashed=false";
		$optParams = [
			'fields' => 'files(id, name)',
			'q' => $query
		];
		$service = new \Google_Service_Drive($this->gClient);
		$user=User::find(1);
		$this->gClient->setAccessToken(json_decode($user->access_token,true));
		$results = $service->files->listFiles($optParams);
		if (count($results->getFiles()) == 0) {
		   return [];
		} else {
			foreach ($results->getFiles() as $file) {
				//  $file->getID()
				if($file->getName() == $folder_name)
				{
						$file = $file->getID();
						break;
				}
			}
			return $file;
		}
	}

	public function uploadFile($tasklist_folder_id, $file_path)
	{
		//  upload file code
		$file_name = explode("/",$file_path);
            $file = new \Google_Service_Drive_DriveFile(array(
                            'name' => $file_name[2],
                            'parents' => array($tasklist_folder_id)
                        ));
			$service = new \Google_Service_Drive($this->gClient);
			$user=User::find(1);
			$this->gClient->setAccessToken(json_decode($user->access_token,true));
            $result = $service->files->create($file, array(
              'data' => file_get_contents(public_path($file_path)),
              'mimeType' => 'application/octet-stream',
              'uploadType' => 'media'
            ));
            // get url of uploaded file
            $url='https://drive.google.com/open?id='.$result->id;
           return $result;
	}

	public function listFile($id){
		$parameters['q'] = "mimeType='application/vnd.google-apps.folder' and '".$id."' in parents and trashed=false";
		$service = new \Google_Service_Drive($this->gClient);
		$files = $service->files->listFiles($parameters);
		$user=User::find(1);
		$file_list= [];
		$this->gClient->setAccessToken(json_decode($user->access_token,true));
		foreach( $files as $k => $file ){
			$sub_files = $service->files->listFiles(array('q' => "'{$file['id']}' in parents and trashed=false"));
			foreach( $sub_files as $kk => $sub_file ) {
				$file_list[] = $sub_file['name'];
			}
		}
		// if ($file_list == []) {
		//    return [];
		// } else {
			return $file_list;
		// }
	}

	public function delete(){
		$workspace_folder_name= "Default - Workplace";
		$file_name = "tasklist_216190942753.png";
		$user_id = User::where('email','sahani.aashita31@gmail.com')->first();
		$service = new \Google_Service_Drive($this->gClient);
		/* Refresh login token */
			$user=User::find($user_id->id);
			$this->gClient->setAccessToken(json_decode($user->access_token,true));
			if ($this->gClient->isAccessTokenExpired()) {
				// save refresh token to some variable
				$refreshTokenSaved = $this->gClient->getRefreshToken();
				// update access token
				$this->gClient->fetchAccessTokenWithRefreshToken($refreshTokenSaved);               
				// pass access token to some variable
				$updatedAccessToken = $this->gClient->getAccessToken();
				// append refresh token
				$updatedAccessToken['refresh_token'] = $refreshTokenSaved;
				//Set the new acces token
				$this->gClient->setAccessToken($updatedAccessToken);
				$user->update($updatedAccessToken);
			}else{
				$updatedAccessToken = $user->access_token;
			}
		/*End Refresh login token */

			/*get worspace folder id */
			$onething_folder_id =  $this->getOneThingFolder('root');
			$workspace_folder_id =  $this->getFolderId($onething_folder_id[0], $workspace_folder_name);
			
			/*get list of files */
			$parameters['q'] = "mimeType='application/vnd.google-apps.folder' and '".$workspace_folder_id."' in parents and trashed=false";
			$service = new \Google_Service_Drive($this->gClient);
			$files = $service->files->listFiles($parameters);
			$user=User::find(1);
			$file_list= [];
			$this->gClient->setAccessToken(json_decode($user->access_token,true));
			foreach( $files as $k => $file ){
				$sub_files = $service->files->listFiles(array('q' => "'{$file['id']}' in parents and trashed=false"));
				foreach( $sub_files as $kk => $sub_file ) {
					if($sub_file['name']  == $file_name)
					{
						$id = $sub_file['id'];
						break;
					}
					
				}
			}
			/*delete specific file */
			$service->files->delete($id);
			return "File deleted..!!";
		}
}


