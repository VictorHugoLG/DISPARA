(function() {

	tinymce.create('tinymce.plugins.TretaImgUpload', {
		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 * This call is done before the editor instance has finished it's initialization so use the onInit event
		 * of the editor instance to intercept that event.
		 *
		 * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
		 * @param {string} url Absolute URL to where the plugin is located.
		 */
		init : function(ed, url) {
		
			// Register the command so that it can be invoked by using tinyMCE.activeEditor.execCommand('mceExample');
			ed.addCommand('mceTretaImgUpload', function() {
				ed.windowManager.open({
					file : url + '/TretaImgUpload.php',
					width : 450,
					height : 350,
					inline : 1
				}, {
					plugin_url : url
				});
			});

			// Register example button
			ed.addButton('tretaimgupload', {
				title : 'Fazer Upload de Imagem(CTRL + Q)',
				cmd : 'mceTretaImgUpload',
				image : url + '/img/upimg.gif'
			});

			ed.addShortcut('ctrl+q', 't', 'mceTretaImgUpload');
		},

		/*
          Retorna informações sobre o plugin
		 */
		getInfo : function() {
			return {
				longname : 'Treta Imagem Upload',
				author : 'Cristiano S S',
				authorurl : 'http://tretasdanet.com',
				infourl : 'http://tretasdanet.com/desenvolvimento/?url=tretaimgupload',
				version : "1.0.1"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('tretaimgupload', tinymce.plugins.TretaImgUpload);
})();
