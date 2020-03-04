<?php
	require_once $_SERVER["DOCUMENT_ROOT"] . '/model/exceptions/InvalidParamException.class.php';
	require_once $_SERVER["DOCUMENT_ROOT"] . '/model/exceptions/DatabaseException.class.php';
	require_once $_SERVER["DOCUMENT_ROOT"] . '/model/functions/send_mail.php';

	class User {
		private $_id;
		private $_pseudo;
		private $_email;
		private $_db;



		// quand on cree l objet user; l user est connecte via l objet
		// ajouter certain parma SI y on utilise le meme souvent ex gender
		/*
		** -------------------- Serialize --------------------
		*/
		public function serialize()
	    {
	        return serialize([
	            $this->_id,
	            $this->_pseudo,
	            $this->_email,
				$this->_db
	        ]);
	    }

	    public function unserialize($data)
	    {
	        list(
	            $this->_id,
	            $this->_pseudo,
	            $this->_email,
				$this->_db
	        ) = unserialize($data);
	    }


		/*
		** -------------------- Construct --------------------
		*/
		public function __construct()
		{
			$a = func_get_args();
	        $i = func_num_args();
	        if (method_exists($this, $f = '__construct' . $i)) {
	            call_user_func_array(array($this,$f),$a);
	        }
			else {
				throw new InvalidParamException("Failed constructing " . __CLASS__ . ". Wrong amount of parameters ($i).", 0);
			}
		}

		private function __construct2($id_user, $db)
		{
			// test parameters validity
			if (!User::is_valid_id($id_user)) {
				throw new InvalidParamException("Failed constructing " . __CLASS__ . ". Invalid id.", 21);
			}
			if (!Database::is_valid($db)) {
				throw new InvalidParamException("Failed constructing " . __CLASS__ . ". Invalid db object.", 22);
			}

			// query from database
			$query = "SELECT pseudo, email FROM user WHERE id_user = :idu;";
			$db->query($query, array(':idu' => $id_user));
			$row = $db->fetch();
			if ($row === false) {
				throw new InvalidParamException("Failed constructing " . __CLASS__ . ". id_user not found in database.", 30);
			}

			// set object properties
			$this->_id = $id_user;
			$this->_pseudo = $row['pseudo'];
			$this->_email = $row['email'];
			$this->_db = $db;
		}

		private function __construct3($pseudo, $hashed_password, $db)
		{
			// test parameters validity
			if (!User::is_valid_pseudo($pseudo)) {
				throw new InvalidParamException("Failed constructing " . __CLASS__ . ". Invalid pseudo.", 31);
			}
			if (!User::is_valid_hashed_password($hashed_password)) {
				throw new InvalidParamException("Failed constructing " . __CLASS__ . ". Invalid hashed password.", 32);
			}
			if (!Database::is_valid($db)) {
				throw new InvalidParamException("Failed constructing " . __CLASS__ . ". Invalid db object.", 33);
			}

			// query from database
			$query = 'SELECT id_user, email FROM user WHERE pseudo = :ps AND password = :pw;';
			$db->query($query, array(':ps' => $pseudo, ':pw' => $hashed_password));
			$row = $db->fetch();
			if ($row === false) {
				throw new InvalidParamException("Failed constructing " . __CLASS__ . ". Pseudo-password combination not found in database.", 30);
			}

			// set object properties
			$this->_id = $row['id_user'];
			$this->_pseudo = $pseudo;
			$this->_email = $row['email'];
			$this->_db = $db;
		}

		private function __construct6($pseudo, $firstname, $lastname, $email, $hashed_password, $db)
		{
			// test parameters validity
			if (!User::is_valid_pseudo($pseudo)) {
				throw new InvalidParamException("Failed constructing " . __CLASS__ . ". Invalid pseudo.", 41);
			}
			if (!User::is_valid_email($email)) {
				throw new InvalidParamException("Failed constructing " . __CLASS__ . ". Invalid email.", 42);
			}
			if (!User::is_valid_hashed_password($hashed_password)) {
				throw new InvalidParamException("Failed constructing " . __CLASS__ . ". Invalid password.", 43);
			}
			if (!Database::is_valid($db)) {
				throw new InvalidParamException("Failed constructing " . __CLASS__ . ". Invalid db object.", 44);
			}
			if (User::is_email_in_use($email, $db)) {
				throw new InvalidParamException("Failed constructing " . __CLASS__ . ". Email in use.", 42);
			}

			// adding new user to database and pull the id_user
			$query = 'INSERT INTO user (pseudo, firstname, lastname, email, password) VALUES (:ps, :fn, :lm, :em, :pw);';
			$db->query($query, array(':ps' => $pseudo, ':fn' => $firstname, ':lm' => $lastname, ':em' => $email, ':pw' => $hashed_password));
			$query = 'SELECT LAST_INSERT_ID() AS `id_user`;';
			$db->query($query, array());
			$row = $db->fetch();
			if ($row === false) {
				throw new DatabaseException("Failed constructing " . __CLASS__ . ". Id not pulled from db.");
			}

			// set object properties
			$this->_id = $row['id_user'];
			$this->_pseudo = $pseudo;
			$this->_email = $email;
			$this->_db = $db;

			//now i will set 5 default picture with one as profile picture

		}


		/*
		** -------------------- Magic methods --------------------
		*/
		public function __destruct() {
			$this->_id = null;
			$this->_pseudo = null;
			$this->_email = null;
			$this->_db = null;
		}
		public function __toString()
		{
			return get_pseudo();
		}


		/*
		** -------------------- Account verification --------------------
		*/
		public function send_account_verification_request($server_url)
		{
			$query = 'INSERT INTO account_verification (account_verification_key, id_user) VALUES ((SELECT FLOOR(RAND()*1000000000000000) AS random_key), :idu);';
			$this->_db->query($query, array(':idu' => $this->_id));
			$query = 'SELECT account_verification_key FROM account_verification WHERE id_user = :idu;';
			$this->_db->query($query, array(':idu' => $this->_id));
			$row = $this->_db->fetch();
			if ($row === false) {
				throw new DatabaseException("Failed constructing " . __CLASS__ . ". Id not pulled from db.");
			}

			send_mail(
				$this,
				"Matcha: account confirmation link.",
				$server_url . '?a=' . $this->get_id() . '&b=' . $row['account_verification_key']
			);
		}
		public static function receive_account_verification_request($a, $b, $db)
		{
			if (!User::is_valid_id($a)) {
				throw new InvalidParamException("Failed running " . __METHOD__ . ". Invalid a param.", 1);
			}
			if (!preg_match("/^[1-9][0-9]*$/", $b)) {
				throw new InvalidParamException("Failed running " . __METHOD__ . ". Invalid b param.", 2);
			}
			if (!Database::is_valid($db)) {
				throw new InvalidParamException("Failed running " . __METHOD__ . ". Invalid db object.", 3);
			}

			// delete account_verification entry from database
			$query = 'DELETE FROM account_verification WHERE account_verification_key = :key AND id_user = :idu;';
			$db->query($query, array(':idu' => $a, ':key' => $b));
			$modified_row_count = $db->rowCount();
			if ($modified_row_count !== 1) {
				throw new InvalidParamException("Account verification failed. a-b combination not found in database.");
			}

			// return the user to log him in
			return new User($a, $db);
		}
		public function is_validated_account()
		{
			$query = 'SELECT account_verification_key FROM account_verification WHERE id_user = :idu;';
			$this->_db->query($query, array(':idu' => $this->get_id()));
			$row = $this->_db->fetch();
			if ($row === false) {
				// if no row dedicated to this user in account_verification_key
				return true;
			}
			// if there is
			return false;
		}


		/*
		** -------------------- Account retrieval --------------------
		*/
		public static function send_account_retrieval($email, $db, $server_url)
		{
			if (!User::is_valid_email($email)) {
				throw new InvalidParamException("Failed running " . __METHOD__ . ". Invalid email.", 1);
			}
			if (!Database::is_valid($db)) {
				throw new InvalidParamException("Failed running " . __METHOD__ . ". Invalid db object.", 2);
			}

			// query from database to make sure the email is linked with a user
			$query = "SELECT id_user FROM user WHERE email = :m;";
			$db->query($query, array(':m' => $email));
			$row = $db->fetch();
			if ($row === false) {
				throw new InvalidParamException("Failed running " . __METHOD__ . ". Email not found in database.", 1);
			}

			$id_user = $row['id_user'];

			// query to remove the other account_retrieval_requests sent by this user
			$query = 'DELETE FROM account_retrieval_requests WHERE id_user = :idu;';
			$db->query($query, array(':idu' => $id_user));

			// query to add an account_retrieval_request to the db
			$query = 'INSERT INTO account_retrieval_requests (account_retrieval_request_key, id_user) VALUES ((SELECT FLOOR(RAND()*1000000000000000) AS random_key), :idu);';
			$db->query($query, array(':idu' => $id_user));

			// query to retrieve the account_retrieval_request_key
			$query = 'SELECT account_retrieval_request_key FROM account_retrieval_requests WHERE id_user = :idu;';
			$db->query($query, array(':idu' => $id_user));
			$row = $db->fetch();
			if ($row === false) {
				throw new DatabaseException("Failed running " . __METHOD__ . ". Key not pulled from db.");
			}

			$account_retrieval_request_key = $row['account_retrieval_request_key'];

			send_mail(
				$id_user,
				$db,
				"Matcha: account retrieval link.",
				$server_url . '/' . $id_user . '/' . $account_retrieval_request_key
			);
		}
		public static function receive_account_retrieval($a, $b, $db)
		{
			if (!User::is_valid_id($a)) {
				throw new InvalidParamException("Failed running " . __METHOD__ . ". Invalid a param.", 1);
			}
			if (!preg_match("/^[1-9][0-9]*$/", $b)) {
				throw new InvalidParamException("Failed running " . __METHOD__ . ". Invalid b param.", 2);
			}
			if (!Database::is_valid($db)) {
				throw new InvalidParamException("Failed running " . __METHOD__ . ". Invalid db object.", 3);
			}

			// delete account_verification entry from database
			$query = 'DELETE FROM account_retrieval_requests WHERE account_retrieval_request_key = :key AND id_user = :idu;';
			$db->query($query, array(':idu' => $a, ':key' => $b));
			$modified_row_count = $db->rowCount();
			if ($modified_row_count !== 1) {
				throw new InvalidParamException("Account retieval failed. a-b combination not found in database.");
			}

			// return the user to log him in
			return new User($a, $db);
		}


		/*
		** -------------------- Set --------------------
		*/
		public function set_pseudo($new)
		{
			if (!User::is_valid_pseudo($new)) {
				throw new InvalidParamException("Failed setting pseudo. Invalid pseudo.");
			}

			$query = 'UPDATE user SET pseudo = :ps WHERE id_user = :id;';
			$this->_db->query($query, array(':ps' => $new, ':id' => $this->_id));
			$modified_row_count = $this->_db->rowCount();
			if ($modified_row_count !== 1) {
				throw new DatabaseException("Fail setting pseudo. " . $modified_row_count . " rows have been modified in the database.");
			}

			$this->_pseudo = $new;
		}
		public function set_email($new)
		{
			if (!User::is_valid_email($new)) {
				throw new InvalidParamException("Fail setting email. Invalid email.", 1);
			}
			if (User::is_email_in_use($new, $this->_db)) {
				throw new InvalidParamException("Failed setting email. Email in use.", 1);
			}

			$query = 'UPDATE user SET email = :em WHERE id_user = :id;';
			$this->_db->query($query, array(':em' => $new, ':id' => $this->_id));
			$modified_row_count = $this->_db->rowCount();
			if ($modified_row_count !== 1) {
				throw new DatabaseException("Fail setting email. " . $modified_row_count . " rows have been modified in the database.");
			}

			$this->_email = $new;
		}
		public function set_password($hashed_new)
		{

			if (!User::is_valid_hashed_password($hashed_new)) {
				throw new InvalidParamException("Fail setting password. Invalid new password.", 2);
			}

			// update db
			$query = 'UPDATE user SET password = :pw WHERE id_user = :id;';
			$this->_db->query($query, array(':pw' => $hashed_new, ':id' => $this->_id));
			$modified_row_count = $this->_db->rowCount();
			if ($modified_row_count !== 1) {
				throw new DatabaseException("Fail setting password. " . $modified_row_count . " rows have been modified in the database.");
			}
		}
		public function set_pref_mail_notifications()
		{
			// update db
			$query = 'UPDATE user SET pref_mail_notifications = :pmn WHERE id_user = :id;';
			$this->_db->query($query, array(':pmn' => $this->get_pref_mail_notifications() ? '0' : '1', ':id' => $this->_id));
			$modified_row_count = $this->_db->rowCount();
			if ($modified_row_count !== 1) {
				throw new DatabaseException("Fail setting pref_mail_notifications. " . $modified_row_count . " rows have been modified in the database.");
			}
		}

	public function set_location($pref_loc, $longitude, $latitude)
	{
			$query = 'UPDATE `user` SET `longitude` = :longitude,`latitude` = :latitude,`pref_localisation`= :pref_loc WHERE id_user = :idco';
			$this->_db->query($query, array(':longitude' => $longitude, ':latitude' => $latitude, ':pref_loc' => $pref_loc, ':idco' => $this->_id));
	}

		public function  set_gender($bool)
		{
		    $query = 'UPDATE user SET gender = :genre WHERE id_user = :id';
		    $this->_db->query($query, array(':genre' => $bool, ':id' => $this->_id));
		}

		public function set_log($bool)
		{
		  //tester qu il capte bien la variable = a true or false
		  $query = ('UPDATE user SET is_loged = :logged WHERE id_user = :id');
		  $this->_db->query($query, array(':logged' => $bool, ':id' => $this->_id));

			$modified_row_count = $this->_db->rowCount();
			if ($modified_row_count !== 1) {
				throw new DatabaseException("Fail setting logged. " . $modified_row_count . " rows have been modified in the database.");
			}
		}

		public function set_sexuality_orientation($datatoinsert)
		{
		  $query = ('UPDATE `user` SET `orientation` = :orientation WHERE `id_user` = :id');
		  $this->_db->query($query, array(':orientation' => $datatoinsert, ':id' => $this->_id));
		}

		public function set_biography($datatoinsert)
		{
		  $query = ('UPDATE user SET biography = :bio WHERE id_user = :id');
		  $this->_db->query($query, array(':bio' => $datatoinsert, ':id' => $this->_id));

		}

		public function set_last_log()
		{
			date_default_timezone_set('Europe/Paris');
		  $timenow = date("Y-m-d H:i:s");
			$query = ('UPDATE user SET last_log = :lastlog WHERE id_user = :id');
		  $this->_db->query($query, array(':lastlog' => $timenow, ':id' => $this->_id));
		  $this->_db->execute();
		}

		public function set_birthdate($data)
		{
			$query = ('UPDATE user SET birth = :data WHERE id_user = :id');
		 	$this->_db->query($query, array(':data' => $data, ':id' => $this->_id));

		}

		public function set_popularity_score($value)
		{
			$query = ('UPDATE user SET popularity_score = :value WHERE id_user = :id');
		 	$this->_db->query($query, array(':value' => $value, ':id' => $this->_id));

		}

		public function set_first_name($value)
		{
			$query = ('UPDATE `user` SET `firstname` = :value WHERE `id_user` = :id');
		 	$this->_db->query($query, array(':value' => $value, ':id' => $this->_id));

		}

		public function set_last_name($value)
		{
			$query = ('UPDATE user SET lastname = :value WHERE id_user = :id');
		 	$this->_db->query($query, array(':value' => $value, ':id' => $this->_id));
		}

		public function list_of_like_of_user_connected()
		{
			$query = 'SELECT * FROM `like` WHERE `liked` = :lik AND (id_user_liking = :id OR id_user_liked = :id) ';
			$this->_db->query($query, array(':lik' => 1, ':id' => $this->_id));
			$row = $this->_db->fetchAll();
			return $row;
		}
		public function get_if_liked($idli)
		{
			// id user liking
			// id user liked
			// like 1 or 0
			// time current time stamp fera le job


			$query = ('SELECT `liked` FROM `like` WHERE `id_user_liking` = :idliking AND `id_user_liked` = :idliked');
			$this->_db->query($query, array(':idliking' => $this->get_id(), 'idliked' => $idli));
			$row = $this->_db->fetch();

			if (isset($row['liked']))
				{

					if($row['liked']== 1)
							return True;
					else
							return False;

					// returne 1 si like   si dislike 0 si aucun like
					// le dislike comptera pour le score de popularité
				}
				return False;
	}

	public	function get_who_liked_the_connected_user()
	{
		$query = 'SELECT * FROM `like` WHERE `id_user_liked` = :id AND `liked` = :true';
		$this->_db->query($query, array(':id' => $this->_id, 'true' => 1));
		$row = $this->_db->fetchAll();
		return $row;
	}

	public function set_profile_viewed($id)
	{
		$query = 'INSERT INTO `profile_viewed`(`id_user_viewing`, `id_user_viewed`) VALUES (:idco, :id)';
		$this->_db->query($query, array(':idco' => $this->_id, 'id' => $id));
	}

	public function set_a_report($id)
	{
		$query = 'INSERT INTO `report`(`id_user_reporting`, `id_user_reported`, `description`) VALUES (:idco, :id, :string)';
		$this->_db->query($query, array(':idco' => $this->_id, 'id' => $id, 'string' => 'Default Description By now'));
	}
	public function set_a_block($id)
	{
		$query = 'INSERT INTO `block`(`id_user_blocking`, `id_user_blocked`, `description`) VALUES (:idco, :id, :string)';
		$this->_db->query($query, array(':idco' => $this->_id, 'id' => $id, 'string' => 'Default Description By now'));
	}

	public function add_popularity()
	{
		$query = 'SELECT `popularity_score` FROM `user` WHERE id_user = :idco';
		$this->_db->query($query, array(':idco' => $this->_id));
		$row = $this->_db->fetch();
		$int = 5 + $row['popularity_score'];
		if ($int > 100)
			$int = 100;

		$query = 'UPDATE `user` SET `popularity_score` = :value WHERE id_user = :idco';
		$this->_db->query($query, array(':idco' => $this->_id, 'value' => $int));
	}
	public function add_popularity_of_this_user($id)
	{
		$query = 'SELECT `popularity_score` FROM `user` WHERE id_user = :idco';
		$this->_db->query($query, array(':idco' => $id));
		$row = $this->_db->fetch();
		$int = 5 + $row['popularity_score'];
		if ($int > 100)
			$int = 100;

		$query = 'UPDATE `user` SET `popularity_score` = :value WHERE id_user = :idco';
		$this->_db->query($query, array(':idco' => $id, 'value' => $int));
	}

	public function substract_popularity()
	{
		$query = 'SELECT `popularity_score` FROM `user` WHERE id_user = :idco';
		$this->_db->query($query, array(':idco' => $this->_id));
		$row = $this->_db->fetch();
		$int = $row['popularity_score'] - 5;
		if ($int < 0)
			$int = 0;

		$query = 'UPDATE `user` SET `popularity_score` = :value WHERE id_user = :idco';
		$this->_db->query($query, array(':idco' => $this->_id, 'value' => $int));
	}

	public function substract_popularity_of_this_user($id)
	{
		$query = 'SELECT `popularity_score` FROM `user` WHERE id_user = :idco';
		$this->_db->query($query, array(':idco' => $id));
		$row = $this->_db->fetch();
		$int = $row['popularity_score'] - 5;
		if ($int < 0)
			$int = 0;

		$query = 'UPDATE `user` SET `popularity_score` = :value WHERE id_user = :idco';
		$this->_db->query($query, array(':idco' => $id, 'value' => $int));
	}

	public function set_a_like($idli)
		{
			// id user liking
			// id user liked
			// like 1 or 0
			// time current time stamp fera le job


			$query = ('SELECT `liked` FROM `like` WHERE `id_user_liking` = :idliking AND `id_user_liked` = :idliked');
			$this->_db->query($query, array(':idliking' => $this->get_id(), 'idliked' => $idli));
			$row = $this->_db->fetch();


			if (isset($row['liked']))
			{
						if ($row['liked'] == 1)
						{

								// je unlike ici
								$value = 0;
								$query = ('UPDATE `like`  SET `liked` = :likkk WHERE `id_user_liking` = :idliking AND `id_user_liked` = :idliked');
								$this->_db->query($query, array(':likkk' => $value, ':idliking' => $this->get_id(), 'idliked' => $idli));
								//$row = $this->_db->fetch();
							//	$this->_db->execute();
							return(1);
							}
						else {

								// je like ici
								$value = 1;
								$query = ('UPDATE `like` SET `liked` = :likkk WHERE  `id_user_liking` = :idliking AND `id_user_liked` = :idliked ');
								$this->_db->query($query, array(':likkk' => $value, ':idliking' => $this->get_id(), 'idliked' => $idli));
								//$this->_db->execute();
							//	$row = $this->_db->fetch();
							return(2);
					}
		}
		else {
							// je like + j insere les donnes du nouveau like
			$value = 1;
			$query = ('INSERT INTO `like` SET `id_user_liking` = :iduserlliking, `id_user_liked` = :iduserliked, `liked` = :likeornot; ');
			$this->_db->query($query, array(':iduserlliking' => $this->_id, ':iduserliked' => $idli, ':likeornot' => $value));
			return(3);
			// je dois permettre qu un seul like
		}


			//comme ca je like unlike avec la mm fonction
		}

		/*
		** -------------------- Message --------------------
		*/

		public function set_a_conversation($id)
		{
			$query = ('INSERT INTO `messages` SET `id_user_sending` = :idusersending, `id_user_receiving` = :iduserreceiving, `content` = :message;');
			$this->_db->query($query, array(':idusersending' => $this->_id, ':iduserreceiving' => $id, ':message' => 'Ceci est votre premier message ensemble suite à votre Match !'));

			$query = ('INSERT INTO `messages` SET `id_user_sending` = :idusersending, `id_user_receiving` = :iduserreceiving, `content` = :message;');
			$this->_db->query($query, array(':idusersending' => $id, ':iduserreceiving' => $this->_id, ':message' => 'Ceci est votre premier message ensemble suite à votre Match !'));

		}

		public function delete_conv_between_user($id)
		{
			$query = 'DELETE FROM `messages` WHERE `id_user_sending` = :idco AND `id_user_receiving` = :id OR `id_user_sending` = :id AND `id_user_receiving` = :idco';
			$this->_db->query($query, array(':idco' => $this->_id, ':id' => $id));
		}


		public function send_message_to_id($id_destinataire, $content)
		{
			$query = ('INSERT INTO `messages` SET `id_user_sending` = :idusersending, `id_user_receiving` = :iduserreceiving, `content` = :message; ');
			$this->_db->query($query, array(':idusersending' => $this->_id, ':iduserreceiving' => $id_destinataire, ':message' => $content));
		}

		public function get_all_messages_of_user_connected()
		{
			$query = ('SELECT *  FROM `messages` WHERE id_user_sending = :id OR id_user_receiving =:id ORDER BY date DESC');
			$this->_db->query($query, array(':id' => $this->get_id()));
			$row = $this->_db->fetchAll();
			if ($row === false) {
				throw new InvalidParamException("Failed running " . __METHOD__ . ". Id not found in database.");
			}
			 return $row;
		}

		public function get_all_messages_between_two_user($id)
		{
			$query = ('SELECT *  FROM `messages` WHERE id_user_sending = :idco AND id_user_receiving = :idvar OR id_user_sending = :idvar AND id_user_receiving = :idco  ORDER BY date DESC');
			$this->_db->query($query, array(':idco' => $this->_id, ':idvar' => $id));
			$row = $this->_db->fetchAll();
			if ($row === false) {
				throw new InvalidParamException("Failed running " . __METHOD__ . ". Id not found in database.");
			}
			 return $row;
		}
		public function set_msg_readed($id)
		{
			$query = ('UPDATE `messages` SET `msg_read` = :bool  WHERE id_user_sending = :idco AND id_user_receiving = :idvar OR id_user_sending = :idvar AND id_user_receiving = :idco');
			$this->_db->query($query, array(':bool' => 1, ':idco' => $this->_id, ':idvar' => $id));
		}

		/*
		** -------------------- Picture --------------------
		*/

		public function set_picture()
		{
			//$query ()
			$path = '/Pictures/def.jpg';
			$query = 'INSERT INTO `picture` (`id_user`, `is_profile-picture`, `path`) VALUES (:id, :true, :pathf)';
	//		$query = 'INSERT INTO `picture` (id_user, `path`) VALUES (:id, :pathfichier)';
			$this->_db->query($query, array(':id' => $this->_id, ':true' => true, ':pathf' => $path));

			$i = 0;
			while ($i <= 3)
			{
				$path = '/Pictures/addpic.png';
				$query = 'INSERT INTO `picture` (`id_user`, `is_profile-picture`, `path`) VALUES (:id, :false, :pathf)';
				//		$query = 'INSERT INTO `picture` (id_user, `path`) VALUES (:id, :pathfichier)';
				$this->_db->query($query, array(':id' => $this->_id, ':false' => false, ':pathf' => $path));

				$i++;
			}
			// $modified_row_count = $this->_db->rowCount();
			// if ($modified_row_count !== 1) {
			// 	throw new DatabaseException("Fail setting picture in data base. " . $modified_row_count . " rows have been modified in the database.");
			// }
		}

		public function set_is_picture_profil($bool, $id_picture)
		{
			$query = 'UPDATE picture SET is_profile-picture = :p WHERE id_picture = :id;';
			$this->_db->query($query, array(':p' => $bool, ':id' => $id_picture));
		}

		public function get_picture_profil($id)
		{
			$query = 'SELECT * FROM `picture` WHERE `id_user` = :id AND `is_profile-picture` = :bool';
			$this->_db->query($query, array(':id' => $id, ':bool' => 1));
			$row = $this->_db->fetch();
			if ($row === false) {
				throw new InvalidParamException("Failed running " . __METHOD__ . ". Id not found in database.");
			}
			 return $row;
		}

		public function get_all_picture()
		{
			//$query ()
			$query = 'SELECT * FROM `picture` WHERE id_user = :id';
			$this->_db->query($query, array(':id' => $this->_id));

			$row = $this->_db->fetchAll();
			if ($row === false) {
				throw new InvalidParamException("Failed running " . __METHOD__ . ". Id not found in database.");
			}
			 return $row;

			// $modified_row_count = $this->_db->rowCount();
			// if ($modified_row_count !== 1) {
			// 	throw new DatabaseException("Fail setting picture in data base. " . $modified_row_count . " rows have been modified in the database.");
			// }
		}
		public function get_all_picture_of_this_id($id)
		{
			//$query ()
			$query = 'SELECT * FROM `picture` WHERE id_user = :id';
			$this->_db->query($query, array(':id' => $id));

			$row = $this->_db->fetchAll();
			if ($row === false) {
				throw new InvalidParamException("Failed running " . __METHOD__ . ". Id not found in database.");
			}
			 return $row;

			// $modified_row_count = $this->_db->rowCount();
			// if ($modified_row_count !== 1) {
			// 	throw new DatabaseException("Fail setting picture in data base. " . $modified_row_count . " rows have been modified in the database.");
			// }
		}

		public function update_picture($id_photo, $path_fichier)
		{
			//UPDATE `picture` SET `path`= '/Pictures/reda.png' WHERE`id_picture`= 12
			$query = 'UPDATE `picture` SET `path` = :pat WHERE `id_picture` = :idp';
			$this->_db->query($query, array(':pat' => $path_fichier, ':idp' => $id_photo));
		}

		public function delete_picture($id_photo)
		{
			$path_fichier = '/Pictures/addpic.png';
			$query = 'UPDATE `picture` SET `path` = :pat WHERE `id_picture` = :idp';
			$this->_db->query($query, array(':pat' => $path_fichier, ':idp' => $id_photo));
		}


		/*
		** -------------------- Notif --------------------
		*/



		//j ai besoin de plusieurs fonction pour ajouter une notif selon le cas dans lequel on est
		// pour resumer notif quand
		// liked OK
		// match OK mais pas encore utiliser

	// fonction qui notif quand like & unlike
		// profile viewed OK mais pas encore utiliser
		// new message OK
		// dislike OK



											//SET
		public function set_a_notif_string($value, $message)
		{
			$idconcat = $this->_pseudo . $message;
			$query = ('INSERT INTO `notifications` SET `id_user` = :value, `notification` = :id');
			$this->_db->query($query, array(':value' => $value, ':id' => $idconcat));
		}

		public function set_a_notif_for_like($value, $message)
		{
			$idconcat = $this->_pseudo . $message;
			$query = ('INSERT INTO `notifications` SET `id_user` = :value, `notification` = :id');
			$this->_db->query($query, array(':value' => $value, ':id' => $idconcat));
		}

		public function set_a_notif_for_new_message($value)
		{
			$idconcat = $this->_pseudo . ' send u a message !';
			$query = ('INSERT INTO `notifications` SET `id_user` = :value, `notification` = :id');
			$this->_db->query($query, array(':value' => $value, ':id' => $idconcat));
		}

		public function set_a_notif_for_match($value)
		{
			$idconcat = 'U got a match with ' .$this->_pseudo;
			$query = ('INSERT INTO `notifications` SET `id_user` = :id, `notification` = :content');
			$this->_db->query($query, array(':id' => $value, ':content' => $idconcat));



			$row = $this->get_all_details_of_this_id($value);
			$idconcat1 = 'U got a match with ' . $row['pseudo'];
			$query = ('INSERT INTO `notifications` SET `id_user` = :value, `notification` = :id');
			$this->_db->query($query, array(':value' => $this->_id, ':id' => $idconcat1));
		}

		public function set_a_notif_for_unmatch($value)
		{
			$idconcat = 'U lost match with ' .$this->_pseudo. ', so ur chat was deleted !';
			$query = ('INSERT INTO `notifications` SET `id_user` = :id, `notification` = :content');
			$this->_db->query($query, array(':id' => $value, ':content' => $idconcat));



			$row = $this->get_all_details_of_this_id($value);
			$idconcat1 = 'U lost a match with ' . $row['pseudo'];
			$query = ('INSERT INTO `notifications` SET `id_user` = :value, `notification` = :id');
			$this->_db->query($query, array(':value' => $this->_id, ':id' => $idconcat1));
		}

		public function set_a_notif_for_profile_viewed($value)
		{
			$idconcat = $this->_pseudo . ' visit ur profile !';
			$query = ('INSERT INTO `notifications` SET `id_user` = :value, `notification` = :id');
			$this->_db->query($query, array(':value' => $value, ':id' => $idconcat));
		}

		public function set_all_notif_readed()
		{
			$query = 'UPDATE `notifications` SET readed = :val WHERE `id_user` = :id';
			$this->_db->query($query, array(':val' => true, ':id' => $this->_id));
		}


												//GET



		public function get_all_notif_of_user_connected()
		{
			$query = ('SELECT *  FROM `notifications` WHERE id_user = :id');
				// ORDER BY date DESC');
			$this->_db->query($query, array(':id' => $this->_id));
			$row = $this->_db->fetchAll();
			if ($row === false) {
				throw new InvalidParamException("Failed running " . __METHOD__ . ". Id not found in database.");
			}
			 return $row;
		}

		public function get_count_notif_user_connected()
		{
			$query = ('SELECT *  FROM `notifications` WHERE id_user = :id AND readed = :readed');
				// ORDER BY date DESC');
			$this->_db->query($query, array(':id' => $this->_id, ':readed' => false));
			$row = $this->_db->rowCount();
			return $row;
		}

		// profile viewed




		/*
		** ------------------- TAGS ---------------------
		*/

			public function get_tag()
			{
				$query = 'SELECT * FROM `intrests` WHERE id_user = :id';
				$this->_db->query($query, array(':id' => $this->_id));
				$row = $this->_db->fetchAll();
			 	if ($row === false) {
				 throw new InvalidParamException("Failed running " . __METHOD__ . ". Id not found in database.");
			 	}
				return $row;
			}

			public function get_users_who_have_this_tag($tag)
			{
				$query = 'SELECT * FROM `intrests` WHERE `tag` = :tag';
				$this->_db->query($query, array(':tag' => $tag));
				$row = $this->_db->fetchAll();
			 	if ($row === false) {
				 throw new InvalidParamException("Failed running " . __METHOD__ . ". Id not found in database.");
			 	}
				return $row;
			}

			public function get_tag_of_this_id($id)
			{
				$query = 'SELECT * FROM `intrests` WHERE id_user = :id';
				$this->_db->query($query, array(':id' => $id));
				$row = $this->_db->fetchAll();
			 	if ($row === false) {
				 throw new InvalidParamException("Failed running " . __METHOD__ . ". Id not found in database.");
			 	}
				return $row;
			}


			public function delete_all_tag()
			{
				//$query = 'DELETE FROM `intrests` WHERE id_user = :id AND tag = :string)';
				$query = 'DELETE FROM `intrests` WHERE `intrests`.`id_user` = :id';
				$this->_db->query($query, array(':id' => $this->_id));
				//$this->_db->execute();
				// $row = $this->_db->fetch();
				// return $row;
			}

			public function set_tag($string)
			{
				$query = 'INSERT INTO `intrests` (id_user, tag) VALUES (:id, :string)';
				$this->_db->query($query, array(':id' => $this->_id, ':string' => $string));
				// $row = $this->_db->fetch();
				// return $row;
			}
			public function get_if_tag_already_set($string)
			{
				$query = 'SELECT COUNT(*) FROM `intrests` WHERE id_user = :id AND tag = :string';
				$this->_db->query($query, array(':id' => $this->_id, ':string' => $string));
				$row = $this->_db->fetch();
			 	return $row;
			}

		/*
		** -------------------- Get --------------------
		*/
		public function get_id()
		{
			return $this->_id;
		}
		public function get_pseudo()
		{
			return $this->_pseudo;
		}
		public function get_email()
		{
			return $this->_email;
		}
		public function get_pref_mail_notifications()
		{
			// query from database
			$query = 'SELECT pref_mail_notifications FROM user WHERE id_user = :id;';
			$this->_db->query($query, array(':id' => $this->get_id()));
			$row = $this->_db->fetch();
			if ($row === false) {
				throw new InvalidParamException("Failed running " . __METHOD__ . ". Id not found in database.");
			}

			//echo 'get_pref_mail_notifications: "' . (($row['pref_mail_notifications'] == '1' ? true : false) ? 't'  . '"' : 'f' . '"');
			return $row['pref_mail_notifications'] == '1' ? true : false;
		}

		public function get_all_details()
		{
			$query = 'SELECT * FROM user WHERE id_user = :id';
			$this->_db->query($query, array(':id' => $this->get_id()));
			$row = $this->_db->fetch();
			if ($row === false) {
				throw new InvalidParamException("Failed running " . __METHOD__ . ". Id not found in database.");
			}
			 return $row;
		}

		public function get_all_details_of_this_id($id)
		{
			$query = 'SELECT * FROM user WHERE id_user = :id';
			$this->_db->query($query, array(':id' => $id));
			$row = $this->_db->fetch();
			if ($row === false) {
				throw new InvalidParamException("Failed running " . __METHOD__ . ". Id not found in database.");
			}
			 return $row;
		}

		public function get_all_details_of_all_id()
		{
			$query = 'SELECT * FROM user';
			$this->_db->query($query);
			$row = $this->_db->fetchAll();
			if ($row === false) {
				throw new InvalidParamException("Failed running " . __METHOD__ . ". Id not found in database.");
			}
			 return $row;

		}

		public function get_all_details_of_all_id_between_age_min_max($age_min, $age_max)
		{
			$query = 'SELECT * FROM user WHERE `birth` BETWEEN "'.$age_min.'" AND "'.$age_max.'" ';
			$this->_db->query($query);
			$row = $this->_db->fetchAll();
			if ($row === false) {
				throw new InvalidParamException("Failed running " . __METHOD__ . ". Id not found in database.");
			}
			 return $row;

		}

		public function get_row_filter($age_min, $age_max, $popularity_min, $popularity_max)
		{
			$query = 'SELECT * FROM user WHERE `birth` BETWEEN "'.$age_min.'" AND "'.$age_max.'" AND WHERE `popularity_score` BETWEEN "'.$popularity_min.'" AND "'.$popularity_max.'" ';
			$this->_db->query($query);
			$row = $this->_db->fetchAll();
			if ($row === false) {
				throw new InvalidParamException("Failed running " . __METHOD__ . ". Id not found in database.");
			}
			 return $row;

		}

		public function get_sexuality_orientation()
		{
		  $query = 'SELECT `orientation` FROM `user` WHERE id_user = :id';
		  $this->_db->query($query, array(':id' => $this->_id));
			$row = $this->_db->fetch();
			if ($row === false) {
				throw new InvalidParamException("Failed running " . __METHOD__ . ". Id not found in database.");
			}
			 return $row;
		}

		public function get_gender()
		{
			$query = 'SELECT `gender` FROM `user` WHERE id_user = :id';
			$this->_db->query($query, array(':id' => $this->_id));
			$row = $this->_db->fetch();
			if ($row === false) {
				throw new InvalidParamException("Failed running " . __METHOD__ . ". Id not found in database.");
			}
			 return $row;
		}


		public function get_who_see_the_profil_of_user_connect()
		{
			$query = 'SELECT * FROM `profile_viewed` WHERE `id_user_viewed` = :id';
			$this->_db->query($query, array(':id' => $this->_id));
			$row = $this->_db->fetchAll();
			if ($row === false) {
				throw new InvalidParamException("Failed running " . __METHOD__ . ". Id not found in database.");
			}
			 return $row;
		}

		public function get_if_a_user_like_user_connected($id)
		{
			$query = 'SELECT `liked` FROM `like` WHERE id_user_liking = :id AND id_user_liked = :idco';
			$this->_db->query($query, array('id' => $id, ':idco' => $this->_id));
			$row = $this->_db->fetch();

			return $row;
		}

		public function get_all_users_blocked_by_user_connected()
		{
			$query = 'SELECT `id_user_blocked` FROM `block` WHERE `id_user_blocking` =:idco';
			$this->_db->query($query, array(':idco' => $this->_id));
			$row = $this->_db->fetchAll();
			return $row;
		}

		// public function get_location_of_this_id()
		// {
		//
		// }


		/*
		** -------------------- Is valid --------------------
		*/
		public static function is_valid_email($email)
		{
			$patern = "/\A(?=[a-z0-9@.!#$%&'*+\/=?^_`{|}~-]{6,254}\z)(?=[a-z0-9.!#$%&'*+\/=?^_`{|}~-]{1,64}@)[a-z0-9!#$%&'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+\/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\z/";
			return preg_match($patern, $email) ? TRUE : FALSE;
		}
		public static function is_valid_pseudo($pseudo)
		{
			$patern = "/^[a-zA-Z0-9]{3,64}$/";
			return preg_match($patern, $pseudo) ? TRUE : FALSE;
		}
		public static function is_valid_password($password)
		{
			$patern = "/^.{12,64}$/";
			return preg_match($patern, $password) ? TRUE : FALSE;
		}
		public static function is_valid_hashed_password($password)
		{
			$patern = "/^[a-f0-9]{128}$/"; // related to hash_password()
			return preg_match($patern, $password) ? TRUE : FALSE;
		}
		public static function is_valid_id($id)
		{
			if (gettype($id) === 'integer' && $id > 0) {
				return TRUE;
			}
			if (gettype($id) === 'string' && preg_match("/^[1-9][0-9]*$/", $id)) {
				return TRUE;
			}
			return FALSE;
		}
		public static function is_email_in_use($email, $db)
		{
			if (!User::is_valid_email($email)) {
				return FALSE;
			}
			if (!Database::is_valid($db)) {
				throw new InvalidParamException("Failed running " . __METHOD__ . ". Invalid db object.", 2);
			}

			$query = 'SELECT id_user FROM user WHERE email = :em;';
			$db->query($query, array(':em' => $email));
			$row = $db->fetch();
			if ($row !== false) {
				return TRUE;
			}
			return FALSE;
		}
		public function is_correct_password($hashed_password)
		{
			if (!User::is_valid_hashed_password($hashed_password)) {
				throw new InvalidParamException("Fail setting password. Invalid new password.", 2);
			}

			$query = 'SELECT password FROM user WHERE id_user = :id;';
			$this->_db->query($query, array(':id' => $this->_id));
			$row = $this->_db->fetch();
			if ($row === false) {
				throw new DatabaseException("Fail testing password. `id_user` not found in database.");
			}
			if (strcmp($row['password'], $hashed_password) != 0) {
				return FALSE;
			}
			return TRUE;
		}

		public static function is_valid($user)
		{
			return gettype($user) === 'object' && get_class($user) === __CLASS__;
		}

		function is_logged($session)
		{
			if (!array_key_exists('user', $session) ||
				$session['user'] == null ||
				$session['user'] == "" ||
				!User::is_valid(unserialize($session['user'])))
			{
				return false;
			}
			return true;
		}

		public function is_id_exist($id)
		{

			$query = 'SELECT * FROM `user` WHERE id_user = :iduseracheck';
			$this->_db->query($query, array(':iduseracheck' => $id));
			$row = $this->_db->fetch();
			if ($row === false) {
				return FALSE;
			}
			return TRUE;
		}



}
?>
