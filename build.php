<?php
	include("build.class.php");
	
	$Build = new Build;
	if(isset($_GET['template'])) {
		$template = $_GET['template'];
		if(!$Build->templateExists($template))
			die("non esiste");
		
		//$Build->loadTemplate($template);
		$workingDirectory = $Build->newWorkingDirectory();
		if($Build->initializeWorkingDirectory($workingDirectory, $template)) {
			header("Location: build.php?workingDirectory={$workingDirectory}");
		} else {
			print "error!";
		}
	} elseif(isset($_GET['workingDirectory'])) {
		$workingDirectory = $_GET['workingDirectory'];
		if(!$Build->workingDirectoryExists($workingDirectory))
			print "working directory not exists!";
		if(isset($_GET['save-website']) and isset($_POST['page'])) {
			$page = $_POST['page'];
			$page = $Build->removeEditorTags($page);
			
			$Build->updateWorkingDirectory($workingDirectory, $page);
		} else {
			$Build->loadWorkingDirectory($workingDirectory);
		}
	} else {
	$page = '
<editor>
<div class="editor-bar">
	<h3>EDITOR BAR</h3>
	<button class="btn btn-primary" data-toggle="modal" data-target="#modal-editor-global">Global Settings</button>
	<button class="btn btn-primary save-website">SAVE</button>
</div>
<div class="modal fade" id="modal-editor-global" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Global Settings</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<table>
					<tr>
						<td>Page Title</td>
						<td><input type="text" name="page-title" /></td>
					</tr>
					<tr>
						<td>Page Description</td>
						<td><input type="text" name="page-description" /></td>
					</tr>
					<tr>
						<td>Page Favicon</td>
						<td><input type="file" name="page-favicon" /></td>
					</tr>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary save-changes">Save changes</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="modal-content-editor" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Edit Block</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary save-changes">Save changes</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
</editor>';
		$page = $Build->removeEditorTags($page);
		print $page;
	}
?>