/**
* TretaImgUpload é um plugim para o editor tinyMCE
* @licenca LGPL(Lesser General Public License)
* Para fins de compatibilidade entre licenças, este plugin adotou a mesma licença do editor.
* Para detalhes sobre a licença visite http://www.fsf.org/,  ou para uma tradução(pt) não oficial
* da licença veja http://gpl3.neoscopio.org/index.php?title=P%C3%A1gina_principal
*
* @suporte
* Este plugin não fornece nenhum tipo de suporte se não pelo próprio site.
* Caso deseje um suporte mais avançado, ou mesmo o desenvolvimento de
* alguma solução específica baseada no editor entre em contato contato@tretasdanet.com
*
* @versao 1.0.1beta
* Esta é uma versão beta e como tal pode não funcionar como previsto. Por isso contamos
* com o seus feedback, para que possamos aperfeiçoar e melhorar o plugin a cada mais.
*
* @contato tretaimgupload@tretasdanet.com
* Para informar bugs, contribuir com correções e/ou contribuições de código
*
* @browsers
* este plugin foi testado com sucesso no seguintes browsers
* firefox 3.*, 4.*(beta), chrome 4.*, 5.*, 6.*, ie 6, 7 e 8
* caso o mesmo não funcione corretamente ou apresente problemas em algum outro browser, nos avise.

*/

var imgUpadas = new Array();
var dir = '';
var TretaImgUpload =
{
	ocupado : false, // impedir duas requisições em paralelo
	last_img_copied : '', // última imagem copiada
	/**
	* init inicializa o componente e carrega o que for necessário
	* @param void
	* @return void
	*/
	init : function()
	{ // para futuras implentações, para inicializa-lo automaticamento vide a última linha deste.
		//this.switchMode('upload');
	},
	
	/**
	* switchMode seleciona a aba ativa
	* @param string a aba que se quer ativar
	* @return void
	*/
	switchMode : function(m)
	{
		var lm = this.lastMode;
		if (lm != m)
		{
			mcTabs.displayTab(m + '_tab',  m + '_panel');
			document.getElementById("detect").style.display = (m == "remoto") ? "inline" : "none";			
			this.lastMode = m;
		}
	},
	/**
	* newElement cria elementos em tempo de execução
	* @param string o elemento que quer criar
	* @param string(opcional) o tipo de elemento no caso seja text
	* @return object o elemento criado
	*/
	newElement : function(e, t)
	{
		return (t == 'text') ? document.createTextNode(e) : document.createElement(e);
	},
	
	/**
	* novoCampo cria um novo campo no formulário
	* @param string o nome do campo
	* @return void
	*/
	novoCampo : function(nome)
	{
		if(this.lastMode == 'remoto' && this.lastMode !== undefined)
			form = document.forms[1];
		else
			form = document.forms[0];
		
		var separa = nome.split('.');
		_nome = separa[0];
		ext = separa[1];		
		var span = this.newElement('span');
		span.setAttribute("id", _nome);
		var label = this.newElement('label');
		var texto = this.newElement('imagem: ', 'text');
		label.appendChild(texto);
		var nomeImg = this.newElement(nome+' ', 'text');
		var img = this.newElement('img');
		img.setAttribute("src", "img/del.png");
		img.setAttribute("border", "0");
		img.onclick = function()
		{			
			TretaImgUpload.get('["del","'+nome+'"]');
		}
		var link = this.newElement('a');
		link.setAttribute("href", "javascript:;");
		link.onclick = function()
		{
			TretaImgUpload.insereEditor(nome);
		}
		link.appendChild(nomeImg);
		var a = this.newElement('a');
		a.setAttribute("href", "javascript:;");
		a.setAttribute("title", "Eliminar");
		br = this.newElement('br');
		
		a.appendChild(img);
		span.appendChild(label);
		span.appendChild(link);
		span.appendChild(a);
		span.appendChild(br);		
		form.appendChild(span);
	},

	/**
	* insere insere a imagem upada no formulário e no array global de imagens
	* @param string o nome da imagem a ser inserida
	* @return void
	*/
	insere : function(nome)
	{
		this.ocupado = false;
		this.status(false);
		imgUpadas.push(nome);
		this.novoCampo(nome);
		this.reload_qtd_imgs(imgUpadas.length);
	},
	/**
	* insereEditor retorna a url da imagem pro view
	* @param string a imagem
	* @return void
	*/
	insereEditor : function(img)
	{
		len = imgUpadas.length;
		last_img = img ? img : (len > 0 ? imgUpadas[len-1] : '');
		if(last_img != '' && last_img != undefined)
		{
			url = dir+last_img;
			
			if(t == 1)
			{ // se tiver sido acesso via image
				try
				{
					var win = tinyMCEPopup.getWindowArg("window");
				}
				catch(err)
				{
					alert('Desculpe, mas houve um erro inesperado!');
					return;
				}				
				win.document.getElementById(tinyMCEPopup.getWindowArg("input")).value = url;
				
				if (typeof(win.ImageDialog) != "undefined")
				{
					if (win.ImageDialog.getImageData)
						win.ImageDialog.getImageData();
				
					if (win.ImageDialog.showPreviewImage)
						win.ImageDialog.showPreviewImage(url);
				}
			}
			else
			{ // // se tiver sido acesso via button
				tinyMCEPopup.editor.execCommand('mceInsertContent', false, '<img src="'+url+'"><p>&nbsp;</p>');
			}
			
			tinyMCEPopup.close();
			return;
		}
		alert('Nenhuma imagem pra inserir!');
	},	
	
	/**
	* cria um obejto xmlHTTP(ajax)
	* @param void
	* @return objeto em caso de sucesso ou false em caso de falha
	*/
	objXMLHttp : function()
	{
		if(window.XMLHttpRequest) // mozilla, safari...
			return new XMLHttpRequest();
		else if(window.ActiveXObject)
		{ // ie
			var versoes = ["MSXML2.XMLHttp.6.0", "MSXML2.XMLHttp.5.0", "MSXML2.XMLHttp.4.0", "MSXML2.XMLHttp.3.0", "MSXML2.XMLHttp", "Microsoft.XMLHttp"];
			for(var i=0; i < versoes.length; i++)
			{
				try
				{
					return new ActiveXObject(versoes[i]);
				}
				catch(ex){}
			}
		}
		return false;
	},
	
	/**
	* get efetua requisições get via ajax
	* @param string dados string recebe os dados que utilizará na requisição
	* @return void
	*/
	get : function(dados)
	{
		if(this.ocupado)
		{
			alert('aguarde a requisição anterior terminar');
			return;
		}
		
		if(dados == '')
			return;
		
		dados = escape(dados);
		dados = 'index.php?d='+dados;
		objAjax = this.objXMLHttp();
		if(!objAjax)
		{
			alert('Não foi possível utilizar o componente XMLHttpRequest(ajax)');
			return;
		}
		
		objAjax.open("GET", dados, true);
		this.status(true);
		this.ocupado = true;
		objAjax.onreadystatechange = function()
		{
			if(objAjax.readyState == 4)
			{
				if(objAjax.status == 200)
				{					
					TretaImgUpload.acao(objAjax.responseText);
				}
				else
					alert("Houve um problema ao tentar executar a requisição!");
			}
		}
		objAjax.send(null);
		return;
	},
	
	/**
	* del deleta objetos da página e do array global
	* @param string nome
	* @return void
	*/
	del : function(nome)
	{
		imgUpadas.unset(nome);
		var separa = nome.split('.');
		_nome = separa[0];
		if(document.getElementById(_nome))
		{
			ed = tinyMCEPopup.editor;
			url = ed.documentBaseURI.toRelative(dir+nome);
			if(this.lastMode == 'remoto' && this.lastMode !== undefined)
				form = document.forms[1];
			else
				form = document.forms[0];

			var filho = document.getElementById(_nome);
			form.removeChild(filho);			
			if(t == 1)
			{ // se tiver sido acesso via image
				try
				{
					var win = tinyMCEPopup.getWindowArg("window");					
					urlimg = ed.documentBaseURI.toRelative(win.document.getElementById(tinyMCEPopup.getWindowArg("input")).value);
					if(urlimg == url)
					{
						win.document.getElementById(tinyMCEPopup.getWindowArg("input")).value = null;
						win.ImageDialog.showPreviewImage();
					}
				}
				catch(err){}
			}
			/*
			* implementar deleção da imagem no editor, quando a mesma for deletada
			else
			{
			}
			*/
			
			
			
		}
		this.reload_qtd_imgs(imgUpadas.length);
	},
	
	/**
	* reload_qtd_imgs atualiza a quantidade de imagens upadas na página
	* @param int a quantidade de imagens upadas
	* @return void
	*/
	reload_qtd_imgs : function(qtd)
	{
		get_ups = document.getElementsByTagName('span');
		for(i = 0; i < get_ups.length; i++)
		{
			if(get_ups[i].id == 'get_ups')
			{
				get_ups[i].innerHTML = uploadMax - qtd;
			}
			
		}
	},

	/**
	* upload faz upload dos arquivos(imgs), efetua um validação baseado na extensão 
	* e envia o formulário do com o upload
	* @param Object o formulário
	* @return void
	*/
	upload : function(f)
	{
		if(imgUpadas.length >= uploadMax)
		{
			alert('Você atingiu o limite de uploads!');
			return;
		}
		
		if(this.ocupado)
		{
			alert('aguarde a requisição anterior terminar');
			return;
		}
		
		if(f.arquivo.value != '')
		{
			if((/(.gif|.jpg|.jpeg|.png|.bmp)$/i).test(f.arquivo.value))
			{
				f.submit();
				this.ocupado = true;
				this.status(true);
			}
			else
				alert('São permitidos apenas upload de imagens com extensão gif, jpg, jpeg, png ou bmp!');
		}
		else
			alert('Coloque a imagem que deseja fazer upload!');
	},
	
	/**
	* upload_remote efetua upload remoto de imagens
	* @param Object formulário
	* @return void
	*/
	upload_remote : function(f, opcao)
	{
		if(opcao == 1)
		{
			this.insere(f);
			return;
		}

		if(f.nodeName == 'FORM')
		{
			e = f.elements;
			url = e.url.value;
			tipo = 'upload_remoto';
		}
		else
		{
			url = f;
			tipo = 'upload_remoto_ins';
			this.last_img_copied = f;
		}

		if(url == '')
		{
			alert('Coloque uma url');
			return;
		}
		
		if(!this.isURL(url))
		{
			alert('url inválida');
			return;
		}
		
		if(!(/(.gif|.jpg|.jpeg|.png|.bmp)$/i).test(url))
		{
			alert('São permitidos apenas upload de imagens com extensão gif, jpg, jpeg, png ou bmp!');
			return;
		}
		
		this.get('["'+tipo+'", "'+url+'"]');
	},
	
	upload_remote_view_inf : function(inf)
	{
		var show_inf = document.getElementById('inf');
		show_inf.innerHTML = inf;
	},
	
	/**
	* detect_imagens pega todas as imagens externas contidas no documento
	* @param void
	* @return void
	*/
	detect_imagens : function()
	{
		ed = tinyMCEPopup.editor;		
		imgs = ed.contentDocument.images;
		l = imgs.length;
		var show_detect = document.getElementById('detectadas');
		html = '';
		if(l > 0)
		{
			for(i = 0; i < l; i++)
			{
				img = imgs.item(i).src;
				width = imgs.item(i).width+20;
				height = imgs.item(i).height+20;

				u = new tinymce.util.URI(img);
				
				if((dir+u.file) != u.relative)
					html += 'URL: <input type="text" name="url'+i+'" value="'+img+'" size="30"> <a href="javascript:;" title="Copiar"><img src="img/okclick.gif" border="0" id="imgUp" onclick="TretaImgUpload.upload_remote(document.forms[1].url'+i+'.value);" style="position: relative; top: 4px"></a> <a href="javascript:;" title="Informações"><img src="img/upimg.gif" border="0" id="imgUp" onclick="val = \''+img+'\'; if(val == \'\'){ alert(\'Coloque uma url!\'); return false;} if(!TretaImgUpload.isURL(val)){ alert(\'url inválida\'); return false;} d = unescape(\'[%22get_inf%22, %22\'+val+\'%22]\'); TretaImgUpload.get(d);" style="position: relative; top: 4px"></a> <a href="javascript:;" onclick="tinyMCE.activeEditor.windowManager.open({file:\''+img+'\', width : '+width+', height: '+height+', inline: true});" title="Visualizar"><img src="img/view.png" border="0" alt="Visualizar" style="position: relative; top: 4px"></a><br>';
					//html += 'URL: <input type="text" name="url'+i+'" value="'+img+'" size="30"> <a href="javascript:;" title="Copiar"><img src="img/okclick.gif" border="0" id="imgUp" onclick="TretaImgUpload.upload_remote(document.forms[1].url'+i+'.value);" style="position: relative; top: 4px"></a> <a href="javascript:;" title="Informações"><img src="img/upimg.gif" border="0" id="imgUp" onclick="val = \''+img+'\'; if(val == \'\'){ alert(\'Coloque uma url!\'); return false;} if(!TretaImgUpload.isURL(val)){ alert(\'url inválida\'); return false;} d = unescape(\'[%22get_inf%22, %22\'+val+\'%22]\'); TretaImgUpload.get(d);" style="position: relative; top: 4px"></a> <a href="javascript:;" onclick="w = new tinymce.WindowManager(ed); w.open({url:\''+img+'\', width : '+width+', height: '+height+', inline: true, resizable : false});" title="Visualizar"><img src="img/view.png" border="0" alt="Visualizar" style="position: relative; top: 4px"></a><br>';
			}
			show_detect.innerHTML = html;
		}
	},
	
	/**
	* alter_img_editor altera url de imagens externas por url de img upadas agora
	* @param nome string nome da imagem
	* @param url_img string url da img que esou procurando
	* @return void
	*/
	alter_img_editor : function(nome)
	{		
		imgUpadas.push(nome);
		this.reload_qtd_imgs(imgUpadas.length);
		
		ed = tinyMCEPopup.editor, imgs = ed.contentDocument.images, lel = imgs.length, url_img = this.last_img_copied;
		
		url_img = ed.documentBaseURI.toRelative(url_img); // pego o parte relativo
		url_img = url_img.replace(/([^a-z0-9])/gi, '\\$1'); // escape caracteres especiais
		
		new_url_img = ed.documentBaseURI.toRelative(dir+nome); // pego o parte relativo
		
		content = ed.getContent({format : 'html'}); // pego o conteudo do editor
		
		er = '/<img(.*)?src=\"'+url_img+'\"(.*)?\\/>/'; // pre crio a er
		er = eval(er); // converto a string anterior em formato de er
		
		if(content.search(er) != -1)
		{ // verifico se a imagem existe no editor, se existir ao altero a url dela
			content = content.replace(er, '<img$1src="'+new_url_img+'"$2/>');
			ed.setContent(content, {format : 'html'});
			imgUpadas.unset(nome);
			alert('A imagem foi upada com sucesso, e ja foi substituída automaticamente no editor.');
		}
		else
			alert('A imagem foi upada com sucesso, porém não foi possível altera-la automaticamante no editor.');
	},
	
	/**
	* status exibe os status pro usuário
	* @param boolean status
	* @return void
	*/
	status : function(s)
	{		
		var pai = document.getElementById('pai');
		if(s)
		{
			dimensoes = getPageSize();			
			div = this.newElement('div');
			div.setAttribute("id", "carregando");
			div.style.width = dimensoes[0]+'px';
			div.style.height = dimensoes[1]+'px';
			div.style.display = 'block';
			
			var img = new Image();
			img.src = 'img/progress.gif';
			img.style.position = 'relative';
			img.style.top = (dimensoes[1]/2)+'px';
			img.style.left = (dimensoes[0]/2)+'px';
			
			div.appendChild(img);
			div.ondblclick = function()
			{
				TretaImgUpload.status(false);
			}
			pai.appendChild(div);
		}
		else
		{		
			if(this.ocupado)
			{
				alert('aguarde a requisição anterior terminar');
				return;
			}
			
			if(document.getElementById('carregando'))
			{
				var filho = document.getElementById('carregando');
				pai.removeChild(filho);
			}
		}
	},
	
	/**
	* acao pra manipular ações na página baseado na resposta da requisição
	* @param string resposta
	* @return void
	*/
	acao : function(rpt)
	{
		this.ocupado = false;
		this.status(false);
		if(rpt == '')
			return;
		
		separa = eval(rpt);
		if(separa[1] == 'erro')
		{
			alert(separa[2]);
			return;
		}
		else if(separa[0] == 'del')
			this.del(separa[1]);
		else if(separa[0] == 'get_inf')
			this.upload_remote_view_inf(separa[1]);
		else if(separa[0] == 'upload_remote')
			this.upload_remote(separa[1], 1);
		else if(separa[0] == 'upload_remoto_ins')
			this.alter_img_editor(separa[1]);
	},
	
	/**
	* msg exibe mensagens pro usuário
	* @param string a msg
	* @return void
	*/
	msg : function(text)
	{
		this.status(false);
		alert(text);
	},
	
	/**
	* isURL verifica se uma url é valida
	* @param string url
	* @return boolean
	*/ 
	isURL : function(url)
	{
		return url != '' ? (/^([a-z]+:\/\/)?([a-z]([a-z0-9\-]*\.)+([a-z]{2}|aero|arpa|biz|com|coop|edu|gov|info|int|jobs|mil|museum|name|nato|net|org|pro|travel)|(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}[0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])(:[0-9]{1,5})?(\/[a-z0-9_\-\.~]+)*(\/([a-z0-9_\-\.]*)(\?[a-z0-9+_\-\.%=&amp;]*)?)?(#[a-z][a-z0-9_]*)?$/i.test(url)) : false;
	}
}

/**
* acrescenta a proprieda unset ao tipo array
*/
Array.prototype.unset = function(str)
{
	len = this.length-1;
	for(i = 0; i < len; i++)
	{
		if(str == this[i])
		{
			for(j = i; j < len; j++)
			{
				this[j] = this[j+1];
			}	
		}
	}
		this.pop();	
}

function isArray(obj) {
	return obj.constructor == Array;
}

function getPageSize()
{
	var xScroll, yScroll;
	if (window.innerHeight && window.scrollMaxY)
	{
		xScroll = document.body.scrollWidth;
		yScroll = window.innerHeight + window.scrollMaxY;
	}
	else if (document.body.scrollHeight > document.body.offsetHeight)
	{
		xScroll = document.body.scrollWidth;
		yScroll = document.body.scrollHeight;
	}else
	{
		xScroll = document.body.offsetWidth;
		yScroll = document.body.offsetHeight;
	}

	var windowWidth, windowHeight;
	if (self.innerHeight)
	{
		windowWidth = self.innerWidth;
		windowHeight = self.innerHeight;
	}
	else if (document.documentElement && document.documentElement.clientHeight)
	{
		windowWidth = document.documentElement.clientWidth;
		windowHeight = document.documentElement.clientHeight;
	}else if (document.body)
	{
		windowWidth = document.body.clientWidth;
		windowHeight = document.body.clientHeight;
	}

	if(yScroll < windowHeight)
		pageHeight = windowHeight;
	else
		pageHeight = yScroll;

	if(xScroll < windowWidth)
		pageWidth = windowWidth;
	else
		pageWidth = xScroll;

	return new Array((pageWidth-10),(pageHeight-10),(windowWidth-10),(windowHeight-10));
}

//tinyMCEPopup.onInit.add(TretaImgUpload.init, TretaImgUpload);