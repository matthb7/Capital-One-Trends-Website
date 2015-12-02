<?php 
	//Configuration for PHP Server
	set_time_limit(0);
	ini_set('default_socket_timeout', 300);
	session_start();

	//Client Info Constants
	define('clientID', '38955fc2bf1047fd99af39c66394bd3c');
	define('clientSecret', 'a40d9bb6d02348da978e949db6ee51a6');
	define('redirectURI', 'http://localhost:8888/capitalonetrends/index.php');
	define('accessToken', '185061817.1fb234f.8fd5a868080c4fafacd0d91df1426fd9');

	//Function to connect to Instagram
	//Parameter: Authorization URL containing desired information and access token
	//Return: Encoded instagram information
	function connectToInstagram($url) {
		$ch = curl_init();
		curl_setopt_array($ch, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => 2));
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}
	//Function to 
	//Parameter: 
	//Return: 
	function getConnotationScore($text) {
		$score = 0;
		$text = strtolower($text);
		//Recently found positvie words in posts that I feel reflected positive connotations
		$posArray = array(1 => 'good', 2 => 'great', 3 => 'thank', 4 => 'happ', 5 => 'lov', 
							6 => 'luck', 7 => 'inspir', 8 => 'satisf', 9 => 'support' , 10 => 'dream');
		//Recently found profanity and/or negative words in posts that I feel reflected negative connotations
		$negArray = array(1 => 'bad', 2 => 'wors', 3 => 'never', 4 => 'hate', 5 => 'horribl', 
							6 => 'terribl', 7 => 'shit', 8 => 'stupid', 9 => 'fuck' , 10 => 'suck');
		foreach ($posArray as $value) {
			$score += substr_count($text, $value);
		}
		foreach ($negArray as $value) {
			$score -= substr_count($text, $value);
		}
		if ($score > 0) {
			return "".$score." (Positive)";
		}
		elseif ($score < 0) {
			return "".$score." (Negative)";
		}
		return "".$score." (Neutral)";
	}
	//Function to
	//Parameter:
	//Return: 
	function getInfluenceScore($followers) {
		$var = 1;
		if ($followers < 100) {
			return "".$var." (Max is 5)";
		}
		elseif ($followers < 10000) {
			$var++;
			return "".$var." (Max is 5)";
		}
		elseif ($followers < 100000) {
			$var++;
			return "".$var." (Max is 5)";
		}
		elseif ($followers < 1000000) {
			$var++;
			return "".$var." (Max is 5)";
		}
		else { //1000000+
			$var++;
			return "".$var." (Max is 5)";
		}
	}
?>

<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
    <meta name="description" content="">
    <meta name="viewport" content="width-device=width, initial-scale=1">
    <meta name="author" content="Matt Blumen">
    <style>
    	body {
    		background-color: #F0F0F0; 
    	}
    	h1 {
    		margin-left: auto; 
    		margin-right: auto;
    		text-align: center;
    		font-family:  'Hoefler Text', Georgia, 'Times New Roman', serif;
			font-weight: normal;
        	font-size: 1.75em;
			letter-spacing: .2em;
			line-height: 1.1em;
			text-transform: uppercase;
    	}
    	.loginButton a {
    		position: fixed; 
    		display: block;
    		margin-left: auto; 
    		margin-right: auto;
    		padding: 45px;
    		top: 300px; 
    		left: 70px; 
    		width: 150px; 
    		height: 50px;
    		color: white;
    		background-color: #FF0066;
    		text-decoration: none;
    		text-align: center;
    		font-size: 35px;
    	}
    	.loginButton a:hover {
			color: #FF0066;
    		background: white;
    		transition: background-color 0.3s ease-in-out;
    		text-decoration: none;
    		box-shadow: 3px 3px 3px #888888;
		}
    	.postDisplay {
    		position: 200px;
    		display: block;
    		margin-left: auto;
    		margin-right: auto;
    		border: 3px solid #8AC007;
    		padding: 10px;
    		background-color: white;
    		width: 60%;
    		height: 250px;
		}
		.successDisplay {
			position: relative;
			top: 30px;
			left: 43px;
			display: block;
    		border: 3px solid #8AC007;
    		padding: 0px;
    		background-color: white;
    		width: 275px;
    		height: 80px;
		}
    </style>
    <title>Capital One Challenge</title>
</head>
<body>
	<h1>RECENT POST FINDER</h1>
	<h1>Capital One Challenge</h1>
	<div class="loginButton">
		<a href="https:api.instagram.com/oauth/authorize/?
			client_id=<?php echo clientID; ?>
			&redirect_uri=<?php echo redirectURI; ?>
			&response_type=code">Login</a>
	</div>
	<?php
		//If authorization is a success, forms to input tag and post amount values are displayed 
		if (isset($_GET['code'])) {
			echo "<form name='myform' id='myform' action='' method='post'><table><tr>
					<td>
						<input type='text' name='tag' id='tag' value='Type Tag Here' />
  						<input type='text' name='num' id='num' value='How Many Posts' />
  						<input type='submit' name='submit' id='submit' value='Submit' />
  					</td></tr></table></form>";
  			echo "<span class='successDisplay'>
  				Login was a success! Please enter your desired tag and post amount in the above fields and click submit to see the results.
  				</span>";
  			
  			//If submit is clicked, server connects to Instagram and fetches neceessary information to display to the screen
			if (isset($_POST['submit'])) {
				$tag = $_POST['tag'];
				$numRecent = $_POST['num'];
				$urlPostInfo = 'https://api.instagram.com/v1/tags/'.$tag.'/media/recent?access_token='.accessToken.'&count='.$numRecent;
				$instagramPostInfo = connectToInstagram($urlPostInfo);
				$postInfo = json_decode($instagramPostInfo, true);

				for ($index = 0; $index < sizeof($postInfo['data']); $index++) {
					$userID = $postInfo['data'][''.$index]['caption']['from']['id'];
					$urlUserInfo = 'https://api.instagram.com/v1/users/'.$userID.'/?access_token='.accessToken;
					$instagramUserInfo = connectToInstagram($urlUserInfo);
					$userInfo = json_decode($instagramUserInfo, true);
					
					$userName = $userInfo['data']['username'];
					$fullName = $userInfo['data']['full_name'];
					$numPosts = $userInfo['data']['counts']['media'];
					$numFollowers = $userInfo['data']['counts']['followed_by'];
					$numFollowing = $userInfo['data']['counts']['follows'];
					$postText = $postInfo['data'][''.$index]['caption']['text'];
					$numLikes = $postInfo['data'][''.$index]['likes']['count'];		
					if ($fullName != "") {
						$fullName = "(".$fullName.")";
					}

					echo "<br/><span class='postDisplay'>
						USERNAME:   ".$userName." ".$fullName."<br/>
						POSTS:   ".$numPosts."<br/>
						FOLLOWERS:   ".$numFollowers."<br/>
						FOLLOWING:   ".$numFollowing."<br/>
						CAPTION:   ".$postText."<br/>
						LIKES:   ".$numLikes."<br/>
						CONNOTATION SCORE:   ".getConnotationScore($postText)."<br/>
						INFLUENCE SCORE:   ".getInfluenceScore($numFollowers)."<br/><br/></span>";
				}	
			}
		}
		else { }
	?>
</body>
</html>