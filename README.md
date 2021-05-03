# sync_drive
Laravel drive sync/ cloud upload- multi-structure folder.


Work Flow

Structure:
Root->Parent folder->Workspace folder->Task folder->Task folder files.


	$upload_array = ['Workplace' => [
					'Test1' => [
							'0' => "tasklistfiles/2530/tasklist_216190942753.png",
							'1' => "tasklistfiles/2530/tasklist_516190942791.txt"
						],
					'Test2' => [
						'0' => "tasklistfiles/2550/tasklist_216190882045.txt"
					]
				]
			];
      

steps:

1- Login into drive(If new user), save the access-token json in DB. 

2- Check if access-token active, if not active refresh access token and update json in DB.

3- Check if Parent folder exsist, if YES - step4, if NO - step5

4- Get Parent folder Id Name, go to-step6

5- Create Parent folder, get folder Id Name, go to-step6

6- Get folder List, Check if Workspace folder exsist, if YES - step7, if NO - step8

7- Get Workspace folder id Name, go to-step9

8- Create Workspace folder, get folder Id Name, go to-step9

9- Get folder List,Check if Task folder exsist, if YES - step10, if NO - step11

10- Get Task folder id Name, go to-step12

11- Create Task folder, get folder Id Name, go to-step12

12- Get File List, Check if Task file exsist, if YES - step13, if NO- (msg-uploaded previously)

13- Upload file in specific folder

 * If while checking folder/file exsist or not, if function return empty array then create
  and make specific upload will be the starting phase. 
  
  



    
  
