<html>
<head>
	<meta charset="UTF-8">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<title>Десять четверостиший</title>
	<style>
		h1 {
			font-family: Georgia, 'Times New Roman', Times, serif;
			font-size: 18pt;
			} 
		p {
			font-family: Georgia, 'Times New Roman', Times, serif;
			}
		table {
			font-family: Georgia, 'Times New Roman', Times, serif; font-size: 10pt;
			}
		input[type=submit]{
			font-family: Georgia, 'Times New Roman', Times, serif;
			}
		#main {
			width: 550px;
			margin:0 auto;
		}
	</style>
</head>
<body>
	<?php
	echo "<div id=\"main\"><div align=\"center\">";
	function intersectionWithRepetitions($array1, $array2){
		sort($array1);
		sort($array2);
		$intersection = 0;
		$pointer1 = 0;
		$pointer2 = 0;
		$size1 = count($array1);
		$size2 = count($array2);
		while(($pointer1 < $size1)&($pointer2 < $size2)){
			if ($array1[$pointer1] == $array2[$pointer2]){
				$intersection += 1;
				$pointer1 += 1;
				$pointer2 += 1;
			}elseif($array1[$pointer1] < $array2[$pointer2]){
				$pointer1 += 1;
			}else{
				$pointer2 += 1;
			}
		}
		return $intersection;
	}
	
	function sessionToFile($sess, $mat, $st){
		$session = fopen("sessions/".$sess.".txt","w");
		fwrite($session, $st."\r\n");
		fwrite($session, $_SERVER['REMOTE_ADDR']."\r\n");
		foreach ($mat as $line){
			fwrite($session, $line);
		}
		fclose($session);
	}
	if  (isset($_POST["feedback"])){
		$f = fopen("sessions/".$_POST["sessionID"]."_completed.txt","a");
		fwrite($f, $_POST["feedback"]);
		fclose($f);
		echo "<h1>Спасибо за ваш комментарий!</h1>";
		exit();
	}
	if (!isset($_POST["step"])){
		echo "<h1>Десять четверостиший</h1></div><div align=\"right\"><p><i>Александр Пиперски, Антон Кухто</i></p></div><div align=\"justify\"><p>Спасибо вам за&nbsp;готовность поучаствовать в&nbsp;эксперименте! Он&nbsp;займёт у&nbsp;вас совсем немного времени. Вы увидите четверостишие; вам надо будет выучить его, нажать кнопку «Далее» и&nbsp;письменно воспроизвести текст по&nbsp;памяти — и&nbsp;так 10 раз (и перед этим ещё один тренировочный). </p>
		
		<p>Учтите, что лучше всего проходить эксперимент в спокойной обстановке, где вас никто не будет отвлекать.</p>
		
		<p>Пожалуйста, не пытайтесь жульничать: это не спортивное мероприятие, допинг здесь неуместен :) </p>
		
		<p>И ещё: не нажимайте, пожалуйста, кнопки «Назад» и «Вперёд» в браузере — от этого всё сломается и ваши результаты не сохранятся.</p>
		
		<p>Готовы?</p></div>";
		echo "<form method=\"post\" action=\"poetic.php\">
		<input type=\"hidden\" name=\"step\" value=\"1\">
		<input type=\"submit\" value=\"Да, начнём!\">
		</form></div>";
	}else{
		$step = $_POST["step"];
		#создаём сессию, если её ещё нет
		if (!isset($_POST["sessionID"])){
			$sessions = array_diff(scandir("sessions", 0), array('..', '.'));
			$totalSessions = count($sessions);
			#$sessionID = str_pad($totalSessions*1000 + rand(0,999),8,"0",STR_PAD_LEFT);
			$sessionID = $totalSessions*1000 + rand(0,999);
			$sources = array_diff(scandir("data", 0), array('..', '.'));
			$material = array();
			foreach ($sources as $source){
				$q = file_get_contents("data/".$source);
				$quatrains = explode("\r\n", $q);
				array_push($material,$quatrains[rand(0,count($quatrains)-1)]."\r\n");
			}
			shuffle($material);
			array_unshift($material, "0\tTest\tTest\tЯ3\t6\t2\tТрусишка зайка серенький\\nПод ёлочкой скакал.\\nПорою волк, сердитый волк,\\nРысцою пробегал.\r\n");
			sessionToFile($sessionID, $material, 0);
		}else{
			$sessionID = $_POST["sessionID"];
		}
		$f = fopen("sessions/".$sessionID.".txt","r");
		$step = trim(fgets($f))+1;
		$ip = fgets($f);
		$material = array();
		for ($i = 0; $i <= 10; $i++){
			array_push($material,fgets($f));
		}
		fclose($f);
		if ($step == 34){
			echo "<h1>Эксперимент окончен</h1></div><div align=\"justify\"><p>Спасибо вам за участие в эксперименте!</p> <p>В этом поле вы можете свой оставить комментарий:</p>
			
			<form method=\"post\" action=\"poetic.php\">
			<textarea name=\"feedback\" cols=\"70\" rows=\"5\"></textarea>
			<input type=\"hidden\" name=\"sessionID\" value=\"".$sessionID."\">
			<input type=\"hidden\" name=\"step\" value=\"".($step+1)."\">
			<input type=\"submit\" value=\"Отправить комментарий\">
			</form>
			
			<p> Кстати, вы можете сравнить оригиналы с вашими ответами:</p>
			<table>";
			for ($i = 1; $i <= 10; $i++){
				echo "<tr><td colspan=\"2\">".str_replace("\\n", "<br />", explode("\t", $material[$i])[1]).": ".str_replace("\\n", "<br />", explode("\t", $material[$i])[2])."</td></tr><tr><td valign=top>".str_replace("\\n", "<br />", explode("\t", $material[$i])[6])."</td><td valign=top>".str_replace("\\n", "<br />", explode("\t", $material[$i])[10])."</td></tr>";
			}
			echo "</table></div></div>";
			rename("sessions/".$sessionID.".txt", "sessions/".$sessionID."_completed.txt");
			exit();
		}
		#print_r($material);
		$exampleID = floor(($step-1)/3);
		if ($exampleID == 0){
			echo "<h1>Тестовое четверостишие</h1>";
		}else{
		
		echo "<h1>Четверостишие №".$exampleID."</h1>";
		}
		if ($step % 3 == 1){
			$timestamp = time();
			echo "<div align=\"justify\"><p>Прочитайте этот текст внимательно и выучите его наизусть. Когда будете готовы, нажмите «Далее»</p> <p>&nbsp;</p><p>";
			$text = str_replace("\\n", "\n", explode("\t", $material[$exampleID])[6]);
			$material[$exampleID] = trim($material[$exampleID])."\t".$timestamp."\r\n";
			$im = imagecreatetruecolor(600, 200);
			$white = imagecolorallocate($im, 255, 255, 255);
			$black = imagecolorallocate($im, 0, 0, 0);
			$font = 'georgia.ttf';
			imagefilledrectangle($im, 0, 0, 599, 199, $white);
			imagettftext($im, 14, 0, 10, 20, $black, $font, $text);
			#echo $text."</p>";
			imagepng($im, "img/".$sessionID.$exampleID.".png");
			echo "<img src=\"img/".$sessionID.$exampleID.".png\"></p>";
			echo "<form method=\"post\" action=\"poetic.php\">
			<input type=\"hidden\" name=\"sessionID\" value=\"".$sessionID."\">
			<input type=\"hidden\" name=\"step\" value=\"".($step+1)."\">
			<input type=\"submit\" value=\"Далее\">
			</form></div></div>";
			sessionToFile($sessionID, $material, $step);
		}elseif($step % 3 == 2){
			$timestamp = time();
			echo "<div align=\"justify\"><p>Введите текст четверостишия, которое вы только что выучили, и нажмите «Далее». Указание: пунктуация не учитывается, так что вы можете не обращать на неё внимания.</p>";
			$material[$exampleID] = trim($material[$exampleID])."\t".$timestamp."\r\n";
			echo "<form method=\"post\" action=\"poetic.php\">
			<textarea name=\"response\" cols=\"70\" rows=\"5\"></textarea>
			<input type=\"hidden\" name=\"sessionID\" value=\"".$sessionID."\">
			<input type=\"hidden\" name=\"step\" value=\"".($step+1)."\">
			<input type=\"submit\" value=\"Далее\">
			</form></div></div>";
			sessionToFile($sessionID, $material, $step);
		}elseif($step % 3 == 0){
			$timestamp = time();
			#$response = mb_ereg_replace("[^а-я]","", mb_ereg_replace("\s+", " ", mb_ereg_replace("ё","е",mb_strtolower($_POST["response"]))));
			$response = mb_ereg_replace("ё","е",mb_strtolower($_POST["response"]));
			$original = mb_ereg_replace("ё","е",mb_strtolower(explode("\t", $material[$exampleID])[6]));
			preg_match_all("/[а-я]+/u",$response,$wordsResponse);
			preg_match_all("/[а-я]+/u",$original,$wordsOriginal);
			#print_r($wordsResponse);
			#print_r($wordsOriginal);
			$intersection = intersectionWithRepetitions($wordsResponse[0], $wordsOriginal[0]);
			$perc = $intersection / (count($wordsResponse[0]) + count($wordsOriginal[0]) - $intersection) * 100;
			#$response = strtr($response, 'абвгдежзийклмнопрстуфхцчшщъыьэюя','abvgdejziyklmnoprstufxc46q!@#$%^');
			#$original = strtr($original, 'абвгдежзийклмнопрстуфхцчшщъыьэюя','abvgdejziyklmnoprstufxc46q!@#$%^');
			#echo $original."<br />".$response;
			#$sim = similar_text($response, $original, $perc);
			echo "<p>Точность вашего ответа: ".round($perc,2)."%</p>";
			echo "<form method=\"post\" action=\"poetic.php\">
			<input type=\"hidden\" name=\"sessionID\" value=\"".$sessionID."\">
			<input type=\"hidden\" name=\"step\" value=\"".($step+1)."\">
			<input type=\"submit\" value=\"Далее\">
			</form></div></div>";
			$material[$exampleID] = trim($material[$exampleID])."\t".$timestamp."\t".preg_replace("/\r\n|\r|\n/","\\n",$_POST["response"])."\t".$perc."\r\n";
			sessionToFile($sessionID, $material, $step);
		}
	}
	?>
</body>
</html>