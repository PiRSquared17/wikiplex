<?php 
	$this->includeAtTemplateBase('includes/header.php'); 
?>

	<h1><?php if (isset($data['header'])) { echo $data['header']; } else { echo "Some error occured"; } ?></h1>

	<p>Here is a list of existing wikis.</p>

	<h2>Your own wikis (<?php echo $this->data['user']; ?>)</h2>
	
	<dl>
	<?php
	
		foreach ($this->data['listprivate'] AS $wiki) {
			
			echo '<dt>' . $wiki->getName() . '</dt>';
			echo '<dd><p>' . $wiki->getDescr() . '</p>';
			echo '<p>[ <a href="http://ow.feide.no/doku.php?id=' . $wiki->getIdentifier() . ':start">visit wiki</a> 
			| <a href="edit.php?edit=' . $wiki->getIdentifier() . '">setup</a>
			]</p></dd>';
		}
	
	
	?>
	</dl>

	
	
	<h2>Public wikis</h2>
	
	<dl>
	
	<?php
	
		#print_r($this->data['listpublic']);
		foreach ($this->data['listpublic'] AS $wiki) {
			
			echo '<dt>' . $wiki->getName() . '</dt>';
			echo '<dd><p>' . $wiki->getDescr() . '</p>';
			echo '<p>[ <a href="http://ow.feide.no/doku.php?id=' . $wiki->getIdentifier() . ':start">visit wiki</a> ]</p></dd>';
		}
	
	
	?>

	</dl>
	

	<h2>Create a new wiki</h2>
	
	<form method="post" action="edit.php">
		<input type="hidden" name="createnew" value="1" />
		
		<p>Wiki ID: 
		<input type="text" name="edit" value="" /></p>
		
		<p>The wiki ID must consist of 1 or more lowercase characters [a-z]. Maximum 15 characters.</p>
		
		<input type="submit" name="createnewsubmit" value="Create new wiki" />
		
	</form>

			
<?php $this->includeAtTemplateBase('includes/footer.php'); ?>