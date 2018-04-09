<?php
	$user_id = $_SESSION['user_id'];
	$end_date = strtotime('-75 days');
	$end_date = date('Y-m-d', $end_date);
	$sql = "SELECT U.user_id AS user_id, U.firstname AS first_name, U.lastname AS last_name, U.view_lastname AS view_lastname, U.username AS username, U.sex AS gender, FEED.date_posted AS date_posted, FEED.note_id AS note_id, FEED.note AS note, A.extension AS extension, FEED.section AS section, FEED.user_type AS user_type FROM ";
	/* -- Messages -- */
	$sql .= "((SELECT M.from_user_id AS user_id, M.datesent AS date_posted, M.message_id AS note_id, CONCAT(M.viewed, '*{|}*', M.subject) AS note, 'messages' AS section, 'friend' AS user_type FROM pd_messages M WHERE M.to_user_id = ".$user_id." ORDER BY M.message_id DESC LIMIT 0,100) ";
	$sql .= "UNION ";
	/* -- Comments -- */
	$sql .= "(SELECT C.user_id AS user_id, C.dt_commented AS date_posted, C.cid AS note_id, C.is_new AS note, 'comments' AS section, 'friend' AS user_type FROM pd_comments C WHERE C.fuser_id = ".$user_id." ORDER BY C.cid DESC LIMIT 0,100) ";
	$sql .= "UNION ";
	/* -- Friends -- */
	$sql .= "(SELECT F.fuser_id AS user_id, F.date_added AS date_posted, F.friend_id AS note_id, F.status AS note, 'friends' AS section, 'friend' AS user_type FROM pd_friends F WHERE F.user_id = ".$user_id." ORDER BY F.friend_id DESC LIMIT 0,100) ";
	$sql .= "UNION ";
	/* -- Avatar -- */
	$sql .= "(SELECT A.user_id AS user_id, A.date_uploaded AS date_posted, A.avatar_id AS note_id, A.extension AS note, 'avatar' AS section, 'friend' AS user_type FROM pd_avatars A LEFT JOIN pd_friends FR ON A.user_id = FR.user_id WHERE FR.fuser_id = ".$user_id." AND FR.status = 'A' ORDER BY A.avatar_id DESC LIMIT 0,100) ";
	$sql .= "UNION ";
	/* --------------------------- */
	/* -- Blogs -- */
	$sql .= "(SELECT B.user_id AS user_id, B.date_created AS date_posted, B.blog_id AS note_id, CONCAT(B.title, '*{|}*', B.summary) AS note, 'blogs' AS section, 'friend' AS user_type FROM pd_blogs B LEFT JOIN pd_friends FR ON B.user_id = FR.user_id WHERE FR.fuser_id = ".$user_id." AND FR.status = 'A' ORDER BY B.blog_id DESC LIMIT 0,100) ";
	$sql .= "UNION ";
	$sql .= "(SELECT B.user_id AS user_id, B.date_created AS date_posted, B.blog_id AS note_id, CONCAT(B.title, '*{|}*', B.summary) AS note, 'blogs' AS section, 'follow' AS user_type FROM pd_blogs B LEFT JOIN pd_follow_member FM ON B.user_id = FM.follow_userid WHERE B.privacy = 'P' AND FM.user_id = ".$user_id." ORDER BY B.blog_id DESC LIMIT 0,100) ";
	/* --------------------------- */
	$sql .= "UNION ";
	/* --------------------------- */
	/* -- Videos -- */
	$sql .= "(SELECT V.user_id AS user_id, V.date_added AS date_posted, V.video_id AS note_id, CONCAT(V.title, '*{|}*', V.embed_src, '*{|}*', V.description) AS note, 'video' AS section, 'friend' AS user_type FROM pd_videos V LEFT JOIN pd_friends FR ON V.user_id = FR.user_id WHERE FR.fuser_id = ".$user_id." AND FR.status = 'A' ORDER BY V.video_id DESC LIMIT 0,100) ";
	$sql .= "UNION ";
	$sql .= "(SELECT V.user_id AS user_id, V.date_added AS date_posted, V.video_id AS note_id, CONCAT(V.title, '*{|}*', V.embed_src, '*{|}*', V.description) AS note, 'video' AS section, 'follow' AS user_type FROM pd_videos V LEFT JOIN pd_follow_member FM ON V.user_id = FM.follow_userid WHERE FM.user_id = ".$user_id." ORDER BY V.video_id DESC LIMIT 0,100) ";
	/* --------------------------- */
	$sql .= "UNION ";
	/* --------------------------- */
	/* -- Photo Albums -- */
	$sql .= "(SELECT AL.user_id AS user_id, AL.date_created AS date_posted, AL.album_id AS note_id, AL.album_title AS note, 'albums' AS section, 'friend' AS user_type FROM pd_albums AL LEFT JOIN pd_friends FR ON AL.user_id = FR.user_id WHERE FR.fuser_id = ".$user_id." AND FR.status = 'A' ORDER BY AL.album_id DESC LIMIT 0,100) ";
	$sql .= "UNION ";
	$sql .= "(SELECT AL.user_id AS user_id, AL.date_created AS date_posted, AL.album_id AS note_id, AL.album_title AS note, 'albums' AS section, 'follow' AS user_type FROM pd_albums AL LEFT JOIN pd_follow_member FM ON AL.user_id = FM.follow_userid WHERE AL.privacy = 'P' AND FM.user_id = ".$user_id." ORDER BY AL.album_id DESC LIMIT 0,100) ";
	/* --------------------------- */
	$sql .= "UNION ";
	/* --------------------------- */
	/* -- Classifieds -- */
	$sql .= "(SELECT CL.user_id AS user_id, CL.date_added AS date_posted, CL.class_id AS note_id, CL.title AS note, 'classifieds' AS section, 'friend' AS user_type FROM pd_classifieds CL LEFT JOIN pd_friends FR ON CL.user_id = FR.user_id WHERE FR.fuser_id = ".$user_id." AND FR.status = 'A' ORDER BY CL.class_id DESC LIMIT 0,100) ";
	$sql .= "UNION ";
	$sql .= "(SELECT CL.user_id AS user_id, CL.date_added AS date_posted, CL.class_id AS note_id, CL.title AS note, 'classifieds' AS section, 'follow' AS user_type FROM pd_classifieds CL LEFT JOIN pd_follow_member FM ON CL.user_id = FM.follow_userid WHERE FM.user_id = ".$user_id." ORDER BY CL.class_id DESC LIMIT 0,100) ";
	/* --------------------------- */
	/* -- Comments -- */
	$sql .= "UNION ";
	$sql .= "(SELECT BC.user_id AS user_id, BC.dt_commented AS date_posted, CONCAT(BC.blog_id, '*{|}*', BC.cid) AS note_id, BL.title AS note, 'blog_comment' AS section, 'friend' AS user_type FROM pd_comments_blog BC INNER JOIN pd_blogs BL ON BC.blog_id = BL.blog_id WHERE BL.user_id = ".$user_id." ORDER BY BC.cid DESC LIMIT 0,100) ";
	$sql .= "UNION ";
	$sql .= "(SELECT PC.user_id AS user_id, PC.dt_commented AS date_posted, CONCAT(AL.album_id, '*{|}*', PC.cid) AS note_id, CONCAT(PC.photo_id, '*{|}*', AL.album_title) AS note, 'photo_comment' AS section, 'friend' AS user_type FROM (SELECT cid, PH.album_id AS album_id, P.photo_id AS photo_id, P.user_id AS user_id, dt_commented FROM pd_comments_photo P INNER JOIN pd_photos PH ON P.photo_id = PH.photo_id WHERE PH.user_id = ".$user_id." ORDER BY P.cid DESC LIMIT 0,100) PC INNER JOIN pd_albums AL ON PC.album_id = AL.album_id) ";
	$sql .= "UNION ";
	$sql .= "(SELECT VC.user_id AS user_id, VC.dt_commented AS date_posted, CONCAT(VC.video_id, '*{|}*', VC.cid) AS note_id, VI.title AS note, 'video_comment' AS section, 'friend' AS user_type FROM pd_comments_video VC INNER JOIN pd_videos VI ON VC.video_id = VI.video_id WHERE VI.user_id = ".$user_id." ORDER BY VC.cid DESC LIMIT 0,100) ";
	$sql .= "UNION ";
	/* --------------------------- */
	/* -- Notes -- */
	$sql .= "(SELECT N.user_id AS user_id, N.date_posted AS date_posted, N.note_id AS note_id, N.note AS note, 'note' AS section, 'friend' AS user_type FROM pd_notes N LEFT JOIN pd_friends FR ON N.user_id = FR.user_id WHERE (FR.fuser_id = ".$user_id." AND FR.status = 'A') OR N.user_id = ".$user_id." ORDER BY N.note_id DESC LIMIT 0,100) ";
	$sql .= "UNION ";
	$sql .= "(SELECT N.user_id AS user_id, N.date_posted AS date_posted, N.note_id AS note_id, N.note AS note, 'note' AS section, 'follow' AS user_type FROM pd_notes N LEFT JOIN pd_follow_member FM ON N.user_id = FM.follow_userid WHERE FM.user_id = ".$user_id." ORDER BY N.note_id DESC LIMIT 0,100) ";
	/* --------------------------- */
	$sql .= "UNION ";
	/* --------------------------- */
	/* -- Shares -- */
	$sql .= "(SELECT S.user_id AS user_id, S.date_shared AS date_posted, S.share_id AS note_id, CONCAT(S.comment, '*{|}*', S.description, '*{|}*', S.item_id, '*{|}*', S.section) AS note, 'share' AS section, 'friend' AS user_type FROM pd_shares S LEFT JOIN pd_friends FR ON S.user_id = FR.user_id WHERE (FR.fuser_id = ".$user_id." AND FR.status = 'A') OR S.user_id = ".$user_id." ORDER BY S.share_id DESC LIMIT 0,100) ";
	$sql .= "UNION ";
	$sql .= "(SELECT S.user_id AS user_id, S.date_shared AS date_posted, S.share_id AS note_id, CONCAT(S.comment, '*{|}*', S.description, '*{|}*', S.item_id, '*{|}*', S.section) AS note, 'share' AS section, 'follow' AS user_type FROM pd_shares S LEFT JOIN pd_follow_member FM ON S.user_id = FM.follow_userid WHERE FM.user_id = ".$user_id." ORDER BY S.share_id LIMIT 0,100) ";
	/* --------------------------- */
	$sql .= "UNION ";
	/* --------------------------- */
	/* -- Photos -- */
	$sql .= "(SELECT P.user_id AS user_id, P.date_uploaded AS date_posted, P.album_id AS note_id, CONCAT(COUNT(P.album_id), '*{|}*', album_title) AS note, 'photos' AS section, 'friend' AS user_type FROM (SELECT P.user_id AS user_id, date_uploaded, album_id FROM pd_photos P LEFT JOIN pd_friends FR ON P.user_id = FR.user_id WHERE FR.fuser_id = ".$user_id." AND FR.status = 'A' ORDER BY photo_id DESC LIMIT 0,100) P INNER JOIN pd_albums AL2 ON P.album_id = AL2.album_id GROUP BY P.album_id) ";
	$sql .= "UNION ";
	$sql .= "(SELECT P.user_id AS user_id, P.date_uploaded AS date_posted, P.album_id AS note_id, CONCAT(COUNT(P.album_id), '*{|}*', album_title) AS note, 'photos' AS section, 'follow' AS user_type FROM (SELECT P.user_id AS user_id, date_uploaded, album_id FROM pd_photos P LEFT JOIN pd_follow_member FM ON P.user_id = FM.follow_userid WHERE FM.user_id = ".$user_id." ORDER BY photo_id DESC LIMIT 0,100) P INNER JOIN pd_albums AL2 ON P.album_id = AL2.album_id WHERE AL2.privacy = 'P' GROUP BY P.album_id) ";
	/* --------------------------- */
	$sql .= "UNION ";
	/* --------------------------- */
	/* -- Questions -- */
	$sql .= "(SELECT Q.user_id AS user_id, Q.date_published AS date_posted, Q.question_id AS note_id, Q.question AS note, 'questions' AS section, 'friend' AS user_type FROM pd_questions Q LEFT JOIN pd_friends FR ON Q.user_id = FR.user_id WHERE (FR.fuser_id = ".$user_id." AND FR.status = 'A') AND Q.anonymous = 'N' ORDER BY Q.question_id DESC LIMIT 0,100) ";
	$sql .= "UNION ";
	$sql .= "(SELECT Q.user_id AS user_id, Q.date_published AS date_posted, Q.question_id AS note_id, Q.question AS note, 'questions' AS section, 'follow' AS user_type FROM pd_questions Q LEFT JOIN pd_follow_member FM ON Q.user_id = FM.follow_userid WHERE FM.user_id = ".$user_id." AND Q.anonymous = 'N' ORDER BY Q.question_id DESC LIMIT 0,100) ";
	/* --------------------------- */
	$sql .= "UNION ";
	/* --------------------------- */
	/* -- Answers -- */
	$sql .= "(SELECT QA.user_id AS user_id, QA.date_posted AS date_posted, QA.question_id AS note_id, Q.question AS note, 'answers' AS section, 'friend' AS user_type FROM pd_questions Q INNER JOIN (SELECT QA.user_id AS user_id, date_posted, question_id FROM pd_questions_answers QA LEFT JOIN pd_friends FR ON QA.user_id = FR.user_id WHERE (FR.fuser_id = ".$user_id." AND FR.status = 'A') AND QA.anonymous = 'N' ORDER BY QA.answer_id DESC LIMIT 0,100) QA ON Q.question_id = QA.question_id) ";
	$sql .= "UNION ";
	$sql .= "(SELECT QA.user_id AS user_id, QA.date_posted AS date_posted, QA.question_id AS note_id, Q.question AS note, 'answers' AS section, 'follow' AS user_type FROM pd_questions Q INNER JOIN (SELECT QA.user_id AS user_id, date_posted, question_id FROM pd_questions_answers QA LEFT JOIN pd_follow_member FM ON QA.user_id = FM.follow_userid WHERE FM.user_id = ".$user_id." AND QA.anonymous = 'N' ORDER BY QA.answer_id DESC LIMIT 0,100) QA ON Q.question_id = QA.question_id) ";
	$sql .= ") AS FEED ";
	$sql .= "LEFT JOIN pd_users U ON FEED.user_id = U.user_id ";
	$sql .= "LEFT JOIN pd_avatars A ON U.user_id = A.user_id ";
	//$sql .= "WHERE ";
	//$sql .= "FEED.date_posted >= '".$end_date."' ";
	$sql .= "ORDER BY FEED.date_posted DESC LIMIT 0,100";
	//echo $sql;
	$msc = microtime(true);
	$result = mysql_query($sql, $conn);
	$msc = microtime(true)-$msc;