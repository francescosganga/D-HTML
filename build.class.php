<?php
	ini_set("display_errors", 1);
	class Build {
		private $config;

		public function __construct() {
			include("config.inc.php");
			$this->config = $config;
		}

		public function templateExists($template) {
			if(file_exists("{$this->config['global']['path']}/templates/{$template}/template.tpl"))
				return true;
			else
				return false;
		}

		public function workingDirectoryExists($workingDirectory) {
			if(file_exists("{$this->config['global']['path']}/tmp/{$workingDirectory}/template.tpl"))
				return true;
			else
				return false;
		}

		public function newWorkingDirectory() {
			while(1 == 1) {
				$workingDirectory = $this->randomString();
				$newDirectory = "{$this->config['global']['path']}/tmp/{$workingDirectory}/";
				if(realpath($newDirectory) !== false AND is_dir($newDirectory)) {
					continue;
				} else {
					mkdir($newDirectory);
					break;
				}
			}

			return $workingDirectory;
		}

		public function initializeWorkingDirectory($workingDirectory, $template) {
			$templateDirectory = "{$this->config['global']['path']}/templates/{$template}";
			$workingDirectory = "{$this->config['global']['path']}/tmp/{$workingDirectory}";
			if(copy("{$templateDirectory}/template.tpl", "{$workingDirectory}/template.tpl") and copy("{$templateDirectory}/assets.zip", "{$workingDirectory}/assets.zip")) {
				$zip = new ZipArchive;
				if($zip->open("{$templateDirectory}/assets.zip") === TRUE) {
					$zip->extractTo($workingDirectory);
					$zip->close();
					return true;
				}
				return false;
			}
			return false;
		}

		private function randomString($length = 10) {
			return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
		}

		public function loadWorkingDirectory($workingDirectory) {
			$template = file_get_contents("{$this->config['global']['path']}/tmp/{$workingDirectory}/template.tpl");
			$template = str_replace("{{url}}", "{$this->config['global']['url']}/tmp/{$workingDirectory}/", $template);
			$template = str_replace("{{editorContent}}", '
<editor>
	<div class="editor-bar row">
		<div class="col-md-6">
			<h3>EDITOR BAR</h3>
		</div>
		<div class="col-md-6" style="text-align: right">
			<button class="btn btn-primary" data-toggle="modal" data-target="#modal-editor-global">Global Settings</button>&emsp;
			<button class="btn btn-primary save-website">SAVE</button>
		</div>
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
	<link rel="stylesheet" type="text/css" href="' . $this->config['global']['url'] . '/assets/css/editor.css" />
	<script type="text/javascript" src="' . $this->config['global']['url'] . '/assets/js/editor.js"></script>
</editor>
', $template);
			$template = preg_replace_callback("(\[content\](.*?)\[/content\])is", function($matches) {
				static $id = 0;
				$id++;

				return "<content class=\"contentEditor\" id=\"{$id}\">{$matches[1]}</content>";
			}, $template);

			print $template;
		}

		public function removeEditorTags($page) {
			$page = preg_replace_callback('/<editor>(.*)<\/editor>/s', function($matches) {
				return "{{editorContent}}";
			}, $page);
			$page = preg_replace_callback("/<content (.*?)>(.*?)<\/content>/", function($matches) {
				return "[content]{$matches[2]}[/content]";
			}, $page);

			return $page;
		}

		public function updateWorkingDirectory($workingDirectory, $page) {
			$page = $this->removeEditorTags($page);
			$page = $this->htmlizePage($page);
			$page = str_replace("{$this->config['global']['url']}/tmp/{$workingDirectory}/", "{{url}}", $page);
			$h = fopen("{$this->config['global']['path']}/tmp/{$workingDirectory}/template.tpl", "w");
			fwrite($h, $page);
			fclose($h);
		}

		public function htmlizePage($page) {
			$page = "<html>{$page}</html>";

			return $page;
		}
	}
?>