<?php 
	$this->includeAtTemplateBase('includes/header.php'); 
?>

	<div id="newwiki">

	<img src="resources/dokuwiki.png" style="float: right; border: none" />
	<h2 style="margin-top: 2px" >Create a new wiki</h2>
	
	<form method="post" action="edit.php">
		<input type="hidden" name="createnew" value="1" />
		

		
		<p>The first thing you need to do to create a wiki is to select a wiki identifier, a machine readable name of the wiki. Examples of wiki identifiers: andreas, simplesamlphp, fasintegrasjon, intmask, foo. Please make the wiki identifier descriptive of the wiki content. You can not change the wiki identifier later.</p>
		
		
		<p>Important: <i>The wiki identifier must consist of one or more lowercase characters [a-z]. Maximum 15 characters.</i></p>
		
		<p>Wiki identifier: 
		<input type="text" name="edit" value="" /></p>
		

		
		<input type="submit" name="createnewsubmit" value="Create new wiki" />
		
	</form>
	</div>
	
	

	<h2>Your wikis</h2>
	
	<p>You are authenticated as <span style="color: #833"><?php echo $this->data['user']; ?></span>, and these are wikis that you have access to administer:</p>
	


	<?php
	
		foreach ($this->data['listprivate'] AS $wiki) {
			
			echo '<div class="wikientry">';
			
			echo '<h3>' . $wiki->getName() . '</h3>';
			echo '<p>' . $wiki->getDescr() . '</p>';
			
			echo '<p><img class="linkicon" src="resources/web-link.png" /><a href="http://ow.feide.no/' . $wiki->getIdentifier() . ':start">Go to ' . $wiki->getName() . '</a> ';
			echo '<img class="linkicon" src="resources/settings.png" /><a href="edit.php?edit=' . $wiki->getIdentifier() . '">Administer ' . $wiki->getName() . '</a></p>';
			
			echo '</div>';
		}
	
	
	?>


	
	
	<h2>Public wikis</h2>
	
	<p>Public wikis is wikis that are accessible for all authenticated users, or even anonymous users. </p>
	
	<?php

	
	
		foreach ($this->data['listpublic'] AS $wiki) {
			
			echo '<div class="wikientry">';
			
			echo '<h3>' . $wiki->getName() . '</h3>';
			echo '<p>' . $wiki->getDescr() . '</p>';
			
			echo '<p><img class="linkicon" src="resources/web-link.png" /><a href="http://ow.feide.no/' . $wiki->getIdentifier() . ':start">Go to ' . $wiki->getName() . '</a> ';
	#		echo '<img src="resources/settings.png" /><a href="edit.php?edit=' . $wiki->getIdentifier() . '">Administer ' . $wiki->getName() . '</a></p>';
			
			echo '</div>';
		}
	
	?>

	</dl>
	



			
<?php $this->includeAtTemplateBase('includes/footer.php'); ?>