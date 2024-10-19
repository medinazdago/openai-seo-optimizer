jQuery(document).ready(function($) {
	// Interceptar el clic en el botón "Generate SEO"
	$('.openai-generate-seo').on('click', function(e) {
		e.preventDefault(); // Evitar que el enlace provoque una recarga de página

		var button = $(this); // Botón actual
		var postId = button.data('post-id'); // ID del post
		var nonce = button.data('nonce'); // Nonce de seguridad

		// Mostrar un indicador de carga (puedes usar un spinner o texto)
		button.text('Generating SEO...');

		// Realizar la llamada AJAX
		$.ajax({
			url: ajaxurl, // ajaxurl está disponible globalmente en el admin de WordPress
			method: 'POST', // Usar POST para seguridad
			data: {
				action: 'openai_seo_generate_content',
				post_id: postId,
				_wpnonce: nonce
			},
			success: function(response) {
				if (response.success) {
					// Actualizar los campos del meta box con los datos generados
					$('input[name="openai_seo_title"]').val(response.data.title);
					$('textarea[name="openai_seo_description"]').val(response.data.description);
					$('textarea[name="openai_seo_keywords"]').val(response.data.keywords);

					// Mostrar un mensaje de éxito
					alert('SEO generated successfully!');
				} else {
					// Mostrar el mensaje de error
					alert('Error: ' + response.data.message);
				}
			},
			error: function() {
				alert('An error occurred while generating SEO.');
			},
			complete: function() {
				// Restaurar el texto del botón
				button.text('Generate SEO');
			}
		});
	});

	// Interceptar el clic en el botón "Save SEO"
	$('.openai-save-seo').on('click', function(e) {
		e.preventDefault(); // Evitar recarga de página

		var button = $(this); // Botón actual
		var postId = button.data('post-id'); // ID del post
		var nonce = button.data('nonce'); // Nonce de seguridad

		// Capturar los valores de los campos manuales
		var seoTitle = $('input[name="openai_seo_title"]').val();
		var seoDescription = $('textarea[name="openai_seo_description"]').val();
		var seoKeywords = $('textarea[name="openai_seo_keywords"]').val();

		console.log({
			post_id: postId,
			nonce: nonce,
			seo_title: seoTitle,
			seo_description: seoDescription,
			seo_keywords: seoKeywords
		});
		// Mostrar un indicador de carga
		button.text('Saving SEO...');

		// Realizar la llamada AJAX para guardar los datos manualmente
		$.ajax({
			url: ajaxurl,
			method: 'POST',
			data: {
				action: 'save_manual_seo_content', // Acción que definimos en PHP
				post_id: postId,
				_wpnonce: nonce,
				seo_title: seoTitle,
				seo_description: seoDescription,
				seo_keywords: seoKeywords
			},
			success: function(response) {
				if (response.success) {
					// Mostrar un mensaje de éxito
					alert('SEO saved successfully!');
				} else {
					// Mostrar el mensaje de error
					alert('Error: ' + response.data.message);
				}
			},
			error: function() {
				alert('An error occurred while saving SEO.');
			},
			complete: function() {
				// Restaurar el texto del botón
				button.text('Save SEO');
			}
		});
	});
});