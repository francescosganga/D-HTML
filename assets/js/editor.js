$(document).ready(function() {
	$("a").click(function(e) {
		e.preventDefault();
	});
	
	$("content.contentEditor").click(function() {
		content = $(this).html();
		$("#modal-content-editor").attr("data-id", $(this).attr("id"));
		$("#modal-content-editor .modal-body").html("<textarea>" + content + "</textarea>");
		$("#modal-content-editor").modal();
	});

	$(".save-changes").click(function() {
		id = $("#modal-content-editor").attr("data-id");
		newContent = $("#modal-content-editor textarea").val();
		$("content.contentEditor[id='" + id + "']").html(newContent);
		$("#modal-content-editor").modal("hide");
	});

	$(".editor-bar .save-website").click(function() {
		page = $("html").html();
		$.ajax({
			method: "POST",
			url: window.location + "&save-website",
			data: {
				page: page
			}
		}).done(function( msg ) {
			alert( "Data Saved: " + msg );
		});
	});
})