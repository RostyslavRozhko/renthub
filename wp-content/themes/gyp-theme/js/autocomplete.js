jQuery(document).ready(function($) {	
	
	$('#autocomplete-field').autoComplete({
		source: function(name, response) {
      console.log(name)
			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: '/wp-admin/admin-ajax.php',
				data: 'action=get_listing_names&name='+name,
				success: function(data) {
					data = data.reduce((result, sentence) => (
						result.concat(sentence.toLowerCase().split(" ").filter(word => (
							word.includes(name) && result.indexOf(word)
						)))
					), [])
					response(data);					
				}
			});
		}
	});
 
});