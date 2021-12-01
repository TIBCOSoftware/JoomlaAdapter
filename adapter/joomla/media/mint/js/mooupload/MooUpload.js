/*
 ---

 name: MooUpload

 description: Crossbrowser file uploader with HTML5 chunk upload support

 version: 1.1

 license: MIT-style license

 authors:
 - Juan Lago

 requires: [Core/Class, Core/Object, Core/Element.Event, Core/Fx.Elements, Core/Fx.Tween]

 provides: [MooUpload]

 ...
 */


var progressSupport = ('onprogress' in new Browser.Request);

/*
 Extend Request class for allow send binary files

 provides: [Request.sendblob]
 */
Request.implement({

	sendBlob: function(blob) {

		this.options.isSuccess = this.options.isSuccess || this.isSuccess;
		this.running = true;

		var url = String(this.options.url), method = this.options.method.toLowerCase();

		if(!url) url = document.location.pathname;

		var trimPosition = url.lastIndexOf('/');
		if(trimPosition > -1 && (trimPosition = url.indexOf('#')) > -1) url = url.substr(0, trimPosition);

		if(this.options.noCache)
			url += (url.contains('?') ? '&' : '?') + String.uniqueID();

		var xhr = this.xhr;

		if(progressSupport) {
			xhr.onloadstart = this.loadstart.bind(this);
			xhr.onprogress = this.progress.bind(this);
		}

		xhr.open(method.toUpperCase(), url, this.options.async, this.options.user, this.options.password);
		if(this.options.user && 'withCredentials' in xhr) xhr.withCredentials = true;

		xhr.onreadystatechange = this.onStateChange.bind(this);

		Object.each(this.headers, function(value, key) {
			try {
				xhr.setRequestHeader(key, value);
			} catch(e) {
				this.fireEvent('exception', [key, value]);
			}
		}, this);

		this.fireEvent('request');

		xhr.send(blob);

		if(!this.options.async) this.onStateChange();
		if(this.options.timeout) this.timer = this.timeout.delay(this.options.timeout, this);
		return this;
	}
});


/*
 MooUpload class

 provides: [MooUpload]
 */
var MooUpload = new Class({
	Implements: [Options, Events],

	options: {
		action: 'upload.php',
		draggable: true,
		accept: '*/*',
		method: 'auto',
		multiple: true,
		autostart: false,
		listview: true,
		blocksize: 101400,        // I don't recommend you less of 101400 and not more of 502000
		maxuploadspertime: 2,
		minfilesize: 1,
		maxfilesize: 0,
		maxfiles: 0,
		verbose: false,

		flash: {
			movie: 'Moo.Uploader.swf'
		},

		texts: {
			error: 'Error',
			file: 'File',
			filesize: 'Filesize',
			filetype: 'Filetype',
			nohtml5: 'Not support HTML5 file upload!',
			noflash: 'Please install Flash 8.5 or highter version (Have you disabled FlashBlock or AdBlock?)',
			maxselect: 'You can only select a maximum of {maxfiles} files',
			sel: 'Sel.',
			selectfile: 'Add files',
			status: 'Status',
			startupload: 'Start upload',
			uploaded: 'Uploaded',
			deleting: 'Deleting'
		},

		onAddFiles: function() {
		},
		onBeforeUpload: function() {
		},
		onFileDelete: function(fileindex) {
		},
		onFileProgress: function(fileindex, percent) {
		},
		onFileUpload: function(fileindex, response) {
		},
		onFileUploadError: function(fileindex, response) {
		},
		onFinishUpload: function() {
		},
		onLoad: function() {
		},
		onProgress: function(percent, stats) {
		},
		onSelect: function() {
		},
		onSelectError: function(error, filename, filesize) {
		}
	},

	filelist: new Array(),
	lastinput: undefined,
	uploadspertime: 0,
	uploading: true,
	flashobj: null,
	flashloaded: false,

	filenum: 0,


	/*
	 Constructor: initialize
	 Constructor

	 Add event on formular and perform some stuff, you now, like settings, ...
	 */
	initialize: function(container, options) {

		this.container = document.id(container);

		this.setOptions(options);

		// Extend new events
		Object.append(Element.NativeEvents, {dragenter: 2, dragexit: 2, dragover: 2, drop: 2});

		// Call custom method
		this[this.options.method](this.container);

		this.populateFileList(this.container);

	},


	/*
	 Function: baseHtml
	 Private method

	 Deploy standard html
	 */
	baseHtml: function(subcontainer) {

		var subcontainer_id = subcontainer.get('id');

		// Add buttons container
		var btnContainer = new Element('div', {
			'class': 'mooupload_btncontainer'
		}).inject(subcontainer);


		// Add addfile button
		var btnAddFile = new Element('button', {
			id: subcontainer_id + '_btnAddfile',
			html: this.options.texts.selectfile,
			type: 'button',
			'class': 'addfile'
		}).inject(btnContainer);

		this.newInput(subcontainer);

		// Show start upload button
		if(!this.options.autostart) {

			var btnStart = new Element('button', {
				id: subcontainer_id + '_btnbStart',
				html: this.options.texts.startupload,
				type: 'button',
				'class': 'start'
			}).inject(btnContainer);

			btnStart.addEvent('click', function() {
				this.upload(subcontainer);
			}.bind(this));
		}

		var progresscont = new Element('div', {
			'id': subcontainer_id + '_progresscont',
			'class': 'progresscont'
		}).inject(btnContainer);

		new Element('div', {
			id: subcontainer_id + '_progressbar',
			html: '0%',
			'class': 'mooupload_on mooupload_progressbar'
		}).inject(progresscont);

		// Create file list container
		if(this.options.listview) {
			var listview = new Element('div.mooupload_listview', {
				id: subcontainer_id + '_listView'
			}).inject(subcontainer);

			var ulcontainer = new Element('ul').inject(listview);

			var header = new Element('li.header').inject(ulcontainer).adopt(

				new Element('div.optionsel', {
					html: this.options.texts.sel
				}),

				new Element('div.filename', {
					html: this.options.texts.file
				}),

				/*
				 new Element('div.filetype', {
				 html: this.options.texts.filetype
				 }),
				 */

				new Element('div.filesize', {
					html: this.options.texts.filesize
				}),

				new Element('div.result', {
					html: this.options.texts.status
				})

			);
		}

		this.fireEvent('onLoad');

	},

	htmlAddFile: function(subcontainer) {
		var subcontainer_id = subcontainer.get('id');

		document.id(subcontainer_id + '_btnAddfile').addEvent('click', function(e) {
			e.stop();

			// Check out select max files
			if(this.options.maxfiles && this.countStats().checked >= this.options.maxfiles) {
				this.fireEvent('onSelectError', ['1012', this.filelist[this.filelist.length - 1].name, this.filelist[this.filelist.length - 1].size]);

				return false;
			}

			// Click trigger for input[type=file] only works in FF 4.x, IE and Chrome
			this.lastinput.click();

			this.progressIni(document.id(subcontainer_id + '_progresscont'));

		}.bind(this));
	},

	newInput: function(subcontainer) {

		var subcontainer_id = document.id(subcontainer).get('id');
		var inputsnum = this.countContainers(subcontainer);
		var formcontainer = subcontainer;

		// Hide old input
		if(inputsnum > 0)
			document.id(subcontainer_id + '_tbxFile' + (inputsnum - 1)).setStyle('display', 'none');


		if(this.options.method == 'html4') {
			formcontainer = new Element('form', {
				id: subcontainer_id + '_frmFile' + inputsnum,
				name: subcontainer_id + '_frmFile' + inputsnum,
				enctype: 'multipart/form-data',
				encoding: 'multipart/form-data',  // I hate IE
				method: 'post',
				action: this.options.action,
				target: subcontainer_id + '_frmFile'
			}).inject(subcontainer);

			if(this.options.maxfilesize > 0) {
				new Element('input', {
					name: 'MAX_FILE_SIZE',
					type: 'hidden',
					value: this.options.maxfilesize
				}).inject(formcontainer);
			}
		}

		// Input File
		this.lastinput = new Element('input', {
			id: subcontainer_id + '_tbxFile' + inputsnum,
			name: subcontainer_id + '_tbxFile' + inputsnum,
			type: 'file',
			size: 1,
			styles: {
				position: 'absolute',
				top: 0,
				left: 0,
				border: 0
			},
			multiple: this.options.multiple,
			accept: this.options.accept

		}).inject(formcontainer);


		// Old version of firefox and opera don't support click trigger for input files fields
		// Internet "Exploiter" do not allow trigger a form submit if the input file field was not clicked directly by the user
		if(this.options.method != 'flash' && (Browser.firefox2 || Browser.firefox3 || Browser.opera || Browser.ie)) {
			this.moveInput(subcontainer);
		}
		else
			this.lastinput.setStyle('visibility', 'hidden');

		// Create events
		this.lastinput.addEvent('change', function(e) {

			e.stop();

			if(this.options.method == 'html4') {
				this.addFiles([
					{
						name: this.getInputFileName(this.lastinput, subcontainer),
						type: null,
						size: null
					}
				], subcontainer);

			}
			else {
				this.addFiles(this.lastinput.files, subcontainer);
			}

		}.bind(this));

		// Hide last input if max selected files
		if(this.options.maxfiles && this.countStats().checked >= this.options.maxfiles)
			this.lastinput.setStyle('display', 'none');

	},

	moveInput: function(subcontainer) {

		// Get addFile attributes
		var btn = subcontainer.getElementById(subcontainer.get('id') + '_btnAddfile');
		var btncoords = btn.getCoordinates(btn.getOffsetParent());

		/*
		 this.lastinput.position({
		 relativeTo: document.id(subcontainer_id+'_btnAddfile'),
		 position: 'bottomLeft'
		 });
		 */

		this.lastinput.setStyles({
			top: btncoords.top,
			left: btncoords.left - 1,
			width: btncoords.width + 2, // Extra space for cover button border
			height: btncoords.height,
			opacity: 0.0001,          // Opera opacity ninja trick
			'-moz-opacity': 0
		});

	},

	upload: function(subcontainer) {

		this.uploading = false;

		this.fireEvent('onBeforeUpload');

		var subcontainer_id = document.id(subcontainer).get('id');

		if(this.options.listview) {
			document.id(subcontainer_id + '_listView').getElements('li.item').addClass('mooupload_readonly');
//      document.id(subcontainer_id+'_listView').getElements('a').setStyle('visibility', 'hidden');
		}

		this.progressStep(document.id(subcontainer_id + '_progresscont'));

		this[this.options.method + 'Upload'](subcontainer);

	},

	progressStep: function(progressbar) {

		if(progressbar.getStyle('display') == 'none')
			progressbar.setStyle('display', 'block');

		var progress = progressbar.getChildren('div');
		var stats = this.countStats();

		stats.uploaded++;
		stats.checked++;

		var percent = (stats.uploaded / stats.checked) * 100;

		progress.set('tween', {duration: 'short'});
		progress.tween('width', percent + '%');

		progress.set('html', percent.ceil() + '%');

		if(percent >= 100) {
			this.uploading = false;
			progress.removeClass('mooupload_on');
			progress.addClass('mooupload_off');
			this.fireEvent('onProgress', [100, stats]);
			this.fireEvent('onFinishUpload');
		}
		else {
			this.fireEvent('onProgress', [percent, stats]);
		}

	},


	progressIni: function(progressbar) {

		var progress = progressbar.getChildren('div');

		progress.removeClass('mooupload_off');
		progress.addClass('mooupload_on');

		progressbar.setStyle('display', 'none');

		progress.setStyle('width', 0);
		progress.set('html', '0%');
	},

	populateFileList: function(maincontainer) {
		var subcontainer = document.id(maincontainer.get('id') + '_listView').getElement('ul');
		var maincontainer_id = maincontainer.get('id');
		var options = this.options;

		var size = 0, key;
		for(key in this.options.files) {
			if(this.options.files.hasOwnProperty(key)) size++;
		}

		if(!size) {
			return;
		}

		if(this.options.maxfiles) {
			this.filesCount = size;
		}
		for(var i = 0, file = null; file = this.options.files[i]; i++) {
			this.filelist[i] = {
				id: String.uniqueID(),
				checked: true,
				name: file.filename,
				type: file.ext,
				size: file.size,
				uploaded: true,
				uploading: false,
				error: false
			};
			this.filenum++;

			var liid = file.filename.toLowerCase();
			liid = liid.replace('.' + file.ext.toLowerCase(), '');

			var elementcontainer = new Element('li', {
				'class': 'item mooupload_readonly',
				id: liid
			}).inject(subcontainer);

			var hiddenInput = new Element('input', {
				'type': 'hidden',
				'name': this.options.formname,
				'value': file.filename
			}).inject(elementcontainer);

			var optionsel = new Element('div', {
				'class': 'optionsel'
			}).inject(elementcontainer);

			var f = file;

			if(this.options.canDelete) {
				var optionremove = new Element('a', {
					'class': 'remove'
				}).inject(optionsel);

				var func = function(file, j) {
					if(!confirm(this.options.texts.sure)) {
						return;
					}
					$$('#' + file.filename.replace('.' + file.ext, '') + ' div.result').set('html', this.options.texts.deleting).setStyle('background', 'url( "' + this.options.url_root + '/media/mint/js/mooupload/imgs/load_bg_red.gif")').setStyle('color', 'maroon');

					var req = new Request.JSON({
						url: this.options.action_remove_file,
						method: 'post',
						autoCancel: true,
						data: { filename: file.filename },
						onComplete: function(json) {
							//console.log(json);
							if(json.success == 1) {
								$(json.id).slide('out');
								setTimeout(function() {
									$(json.id).destroy();
								}, 500);
								this.filelist[j].checked = false;
							}
							if(json.success == 0) {
								this.fireEvent('onFileDelete', ['1016', file.filename]);
							}
							if(json.success == 2) {
								this.fireEvent('onFileDelete', ['1017', file.filename]);
							}
						}.bind(this)
					}).send();
				};

				optionremove.addEvent('click', func.pass([file, i], this));
			}

			var title = file.realname;

			if(this.options.allowEditTitle && file.title) {
				title = file.title;
			}

			var css_class = 'filename';
			if(this.options.allowEditTitle) {
				css_class = 'filename  filenameedit';
			}

			var filename_div = new Element('div', {
				'rel': f.id,
				'id': maincontainer.get('id') + '_file' + i,
				'class': css_class,
				'title': this.options.texts.edit_title,
				html: title,
				styles: {
					width: '55%'
//			        width: this.namewidth + 'px',
				}
			}).inject(elementcontainer);

			if(this.options.allowAddDescr) {
				this.addDescriptionInterface(elementcontainer, f.id, f.description);
			}

			if(this.options.allowEditTitle) {
				this.addTitleInterface(filename_div, filename_div.get('rel'));
			}

			new Element('div', {
				'class': 'filesize',
				html: this.formatSize(file.size)
			}).inject(elementcontainer);

			new Element('div', {
				id: maincontainer_id + '_file_' + i,
				'class': 'result',
				'html': this.options.texts.uploaded
			}).inject(elementcontainer);

			elementcontainer.highlight('#FFF', '#E3E3E3');
		}
	},

	/*
	 Function: addFiles
	 Public method

	 Add new files
	 */
	addFiles: function(files, subcontainer) {

		var subcontainer_id = subcontainer.get('id');
		var maxfileserror = false;

		if(this.options.listview && subcontainer !== undefined)
			var listcontainer = document.id(subcontainer.get('id') + '_listView').getElement('ul');

		for(var i = 0, f; f = files[i]; i++) {

			var fname = f.name || f.fileName;
			var fsize = f.size || f.fileSize;
			var fchecked = true

			var pattern = /<(.*)>/;

			function hasHtmlTags(string) {
				return pattern.test(string);
			};
			if (hasHtmlTags(fname)) {
				if (typeof filterXSS === 'function') {
					fname=filterXSS(fname);
				}
				this.fireEvent('onSelectError', ['Html tags not allowed in filename', fname, fsize]);
				fchecked = false;
			}

			// Check out select max files
			if(this.options.maxfiles && this.countStats().checked >= this.options.maxfiles) {
				this.fireEvent('onSelectError', ['1012', fname, fsize]);
				maxfileserror = true;

				fchecked = false;
			}

			var valid = false;
			this.options.exts.each(function(item, index, object) {
				var pat = new RegExp('\.' + item + '$', 'i');
				if(fname.match(pat)) {
					valid = true;
				}
			});
			if(!valid) {
				this.fireEvent('onSelectError', ['1013', fname, fsize]);
				fchecked = false;
				//delete files[i];
				//continue;
			}

			if(fsize != undefined) {

				if(fsize < this.options.minfilesize) {
					this.fireEvent('onSelectError', ['1014', fname, fsize]);
					fchecked = false;
				}

				if(this.options.maxfilesize > 0 && fsize > this.options.maxfilesize) {
					this.fireEvent('onSelectError', ['1015', fname, fsize]);
					fchecked = false;
				}

			}

			this.filelist[this.filelist.length] = {
				id: String.uniqueID(),
				checked: fchecked,
				name: fname,
				type: f.type || f.extension,
				size: fsize,
				uploaded: false,
				uploading: false,
				error: false
			};

			if(this.options.listview && subcontainer !== undefined && fchecked)
			{
				this.addFileList(subcontainer, listcontainer, this.filelist[this.filelist.length - 1]);
			}
		}

//	if (maxfileserror && this.options.texts.maxselect.length > 0)
//			alert(this.options.texts.maxselect.substitute(this.options));

		//console.log(this.filelist);

		this.fireEvent('onAddFiles');

		this.newInput(subcontainer);

		if(this.options.autostart)
			this.upload(subcontainer);

	},


	addFileList: function(maincontainer, subcontainer, file) {

		var maincontainer_id = maincontainer.get('id');

		var elementcontainer = new Element('li', {
			'class': 'item', 'id': file.id
		}).inject(subcontainer);

		var optionsel = new Element('div', {
			'class': 'optionsel'
		}).inject(elementcontainer);


		var optiondelete = new Element('a', {
			id: maincontainer_id + '_delete' + this.filelist.length,
			'class': 'delete'
		}).inject(optionsel);


		var fileindex = this.filelist.length - 1;

		optiondelete.addEvent('click', function(e) {
			e.stop();

			this.filelist[fileindex].checked = false;

			optiondelete.removeEvents('click');
			optiondelete.getParent('li').destroy();

			this[this.options.method + 'Delete'](fileindex);

			// Check max selected files
			var inputsnum = this.countContainers(maincontainer);

			if(inputsnum > 0) {
				document.id(maincontainer_id + '_tbxFile' + (inputsnum - 1)).setStyles({
					visibility: 'hidden',
					display: 'block'
				});
			}

			this.fireEvent('onFileDelete', [ false, fileindex]);
		}.bind(this));


		new Element('div', {
			'class': 'filename',
			html: file.name,
			styles: {
				width: '55%'//this.namewidth + 'px',
			}
		}).inject(elementcontainer);

		/*
		 new Element('div', {
		 'class': 'filetype',
		 html: file.type || file.extension || 'n/a'
		 }).inject(elementcontainer);
		 */

		new Element('div', {
			'class': 'filesize',
			html: this.formatSize(file.size)
		}).inject(elementcontainer);

		new Element('div', {
			id: maincontainer_id + '_file_' + this.filelist.length,
			'class': 'result'
		}).inject(elementcontainer);

		elementcontainer.highlight('#FFF', '#E3E3E3');

	},

	formatSize: function(o) {
		if(o === undefined) {
			return "N/A"
		}
		if(o > 1073741824) {
			return (o / 1073741824).toFixed(2) + " GB"
		}
		if(o > 1048576) {
			return (o / 1048576).toFixed(2) + " MB"
		}
		if(o > 1024) {
			return (o / 1024).toFixed(2) + " KB"
		}
		return o + " b"
	},

	getContainers: function(subcontainer) {
		return subcontainer.getElements('input[type=file]');
	},

	getForms: function(subcontainer) {
		return subcontainer.getElements('form');
	},

	countContainers: function(subcontainer) {
		var containers = this.getContainers(subcontainer);

		return containers.length;
	},

	countStats: function() {
		var stats = {
			checked: 0,
			uploaded: 0,
			uploading: 0,
			error: 0
		};

		for(var i = 0, f; f = this.filelist[i]; i++) {
			if(f.checked) {
				stats.checked++;

				stats.uploaded += f.uploaded ? 1 : 0;
				stats.uploading += f.uploading ? 1 : 0;
				stats.error += f.error ? 1 : 0;
			}

		}

		return stats;
	},


	// ------------------------- Specific methods for auto ---------------------

	/*
	 Function: auto
	 Private method

	 Specific method for auto
	 */

	auto: function(subcontainer) {

		// Check html5 support
		if(window.File && window.FileList && window.FileReader && window.Blob) {
			this.options.method = 'html5';

			// Unfortunally Opera 11.11 have an incomplete Blob support
			if(Browser.opera && Browser.version <= 11.11)
				this.options.method = 'auto';
		}

		// Default to html4 if no Flash support
		if(this.options.method == 'auto')
			this.options.method = Browser.Plugins.Flash && Browser.Plugins.Flash.version >= 9 ? 'flash' : 'html4';

		this[this.options.method](subcontainer);

	},

	// ------------------------- Specific methods for flash ---------------------

	/*
	 Function: flash
	 Private method

	 Specific method for flash
	 */
	flash: function(subcontainer) {
		var subcontainer_id = subcontainer.get('id');

		// Check if Flash is supported
		if(!Browser.Plugins.Flash || Browser.Plugins.Flash.version < 9) {
			subcontainer.set('html', this.options.texts.noflash);
			return false;
		}

		this.baseHtml(subcontainer);

		// Translate file type filter
		var filters = this.flashFilter(this.options.accept);

		var btn = subcontainer.getElementById(subcontainer_id + '_btnAddfile');
		var btnposition = btn.getPosition(btn.getOffsetParent());
		var btnsize = btn.getSize();

		// Create container for flash
		var flashcontainer = new Element('div', {
			id: subcontainer_id + '_flash',
			styles: {
				position: 'absolute',
				top: btnposition.y,
				left: btnposition.x
			}
		}).inject(subcontainer);


		// Prevent IE cache bug
		if(Browser.ie)
			this.options.flash.movie += (this.options.flash.movie.contains('?') ? '&' : '?') + 'mooupload_movie=' + Date.now();


		// Deploy flash movie
		this.flashobj = new Swiff(this.options.flash.movie, {
			container: flashcontainer.get('id'),
			width: btnsize.x,
			height: btnsize.y,
			params: {
				wMode: 'transparent',
				bgcolor: '#000000'
			},
			callBacks: {

				load: function() {

					Swiff.remote(this.flashobj.toElement(), 'xInitialize', {
						multiple: this.options.multiple,
						url: this.options.action,
						method: 'post',
						queued: this.options.maxuploadspertime,
						fileSizeMin: this.options.fileminsize,
						fileSizeMax: this.options.filemaxsize ? this.options.filemaxsize : null,
						maxFiles: this.options.maxfiles,
						typeFilter: filters,
						mergeData: true,
						data: this.cookieData(),
						verbose: this.options.verbose
					});

					this.flashloaded = true;

				}.bind(this),

				select: function(files) {
					this.addFiles(files[0], subcontainer);
					this.progressIni(document.id(subcontainer_id + '_progresscont'));

				}.bind(this),

				complete: function(resume) {
					this.uploading = false;
				}.bind(this),

				fileProgress: function(file) {

					this.fireEvent('onFileProgress', [file[0].id, file[0].progress.percentLoaded]);

					if(this.options.listview) {
						var respcontainer = document.id(subcontainer_id + '_file_' + file[0].id);

						respcontainer.set('html', file[0].progress.percentLoaded + '%');
					}

				}.bind(this),

				fileComplete: function(file) {

					this.filelist[file[0].id - 1].uploaded = true;

					this.fireEvent('onFileProgress', [file[0].id, 100]);

					if(this.options.listview) {

						var respcontainer = document.id(subcontainer_id + '_file_' + file[0].id);

						if(file[0].response.error > 0) {
							respcontainer.addClass('mooupload_error');
							respcontainer.set('html', this.options.texts.error);
						}
						else {
							respcontainer.addClass('mooupload_noerror');
							respcontainer.set('html', this.options.texts.uploaded);
						}
					}

					this.progressStep(document.id(subcontainer_id + '_progresscont'));

					this.fireEvent('onFileUpload', [file[0].id, JSON.decode(file[0].response.text)]);

				}.bind(this),

				maxFilesError: function() {

					this.fireEvent('onSelectError', ['1012', this.filelist[this.filelist.length - 1].name, this.filelist[this.filelist.length - 1].size]);

//			if (this.options.texts.maxselect.length > 0)
//				alert(this.options.texts.maxselect.substitute(this.options));


				}.bind(this)

			}
		});


		// toElement() method doesn't work in IE
		/*
		 var flashElement = this.flashobj.toElement();

		 // Check flash load
		 if (!flashElement.getParent() || flashElement.getStyle('display') == 'none')
		 {
		 subcontainer.set('html', this.options.texts.noflash);
		 return false;
		 }
		 */

	},

	flashUpload: function(subcontainer) {

		if(!this.uploading) {

			this.uploading = true;

			for(var i = 0, f; f = this.filelist[i]; i++) {
				if(!f.uploading) {
					Swiff.remote(this.flashobj.toElement(), 'xFileStart', i + 1);
					this.filelist[i].uploading = true;
				}
			}

		}

	},

	flashDelete: function(fileindex) {
		this.filelist[fileindex].checked = false;
		Swiff.remote(this.flashobj.toElement(), 'xFileRemove', fileindex + 1);
	},

	flashFilter: function(filters) {
		var filtertypes = {}, assocfilters = {};
		var extensions = {
			'image': '*.jpg; *.jpeg; *.gif; *.png; *.bmp;',
			'video': '*.avi; *.mpg; *.mpeg; *.flv; *.ogv; *.webm; *.mov; *.wm;',
			'text': '*.txt; *.rtf; *.doc; *.docx; *.odt; *.sxw;',
			'*': '*.*;'
		};

		filters.split(',').each(function(val) {
			val = val.split('/').invoke('trim');
			filtertypes[val[0]] = (filtertypes[val[0]] ? filtertypes[val[0]] + ' ' : '') + '*.' + val[1] + ';';
		});

		Object.each(filtertypes, function(val, key) {
			var newindex = key == '*' ? 'All Files' : key.capitalize();
			if(val == '*.*;') val = extensions[key];
			assocfilters[newindex + ' (' + val + ')'] = val;
		});

		return assocfilters;
	},

	// appendCookieData based in Swiff.Uploader.js
	cookieData: function() {

		var hash = {};

		document.cookie.split(/;\s*/).each(function(cookie) {

			cookie = cookie.split('=');

			if(cookie.length == 2) {
				hash[decodeURIComponent(cookie[0])] = decodeURIComponent(cookie[1]);
			}
		});

		return hash;
	},

	// ------------------------- Specific methods for html5 ---------------------

	/*
	 Function: html5
	 Private method

	 Specific method for html5
	 */
	html5: function(subcontainer) {

		//console.log(subcontainer);
		// Check html5 File API
		if(!window.File || !window.FileList || !window.FileReader || !window.Blob) {
			subcontainer.set('html', this.options.texts.nohtml5);
			return false;
		}

		this.baseHtml(subcontainer);

		// Trigger for html file input
		this.htmlAddFile(subcontainer);

	},

	html5Upload: function(subcontainer) {

		var filenum = this.filenum;
		this.getContainers(subcontainer).each(function(el) {
			var files = el.files;

			for(var i = 0, f; f = files[i]; i++) {
				if(typeof this.filelist[filenum] == 'undefined'){

				}
				if(this.uploadspertime <= this.options.maxuploadspertime) {

					//console.log(f.name+' = '+this.filelist[this.filenum].name);

					// Upload only checked and new files
					if(this.filelist[filenum].checked && !this.filelist[filenum].uploading) {
						this.uploading = true;
						this.filelist[filenum].uploading = true;
						this.uploadspertime++;
						this.html5send(subcontainer, this.filelist[filenum].id, f, 0, filenum, false);
					}

				}

				filenum++;

			}

		}.bind(this));

	},

	html5send: function(subcontainer, file_id, file, start, filenum, resume) {

		// Prepare request
		//var xhr = Browser.Request();


		var end = this.options.blocksize,
			action = this.options.action,
			chunk;

		var total = start + end;

		var options = this.options;

		//console.log(start+' + '+end+' = '+total);

		/*
		 if (resume)
		 action += (action.contains('?') ? '&' : '?') + 'resume=1';
		 */

		if(total > file.size)
			end = total - file.size;


		// Get slice method
		if(file.mozSlice)          // Mozilla based
			chunk = file.mozSlice(start, total)
		else if(file.webkitSlice)  // Chrome, Safari, Konqueror and webkit based
			chunk = file.webkitSlice(start, total);
		else                        // Opera and other standards browsers
			chunk = file.slice(start, total)

		var xhr = new Request({
			url: action,
			urlEncoded: false,
			noCache: true,
			headers: {
				'Cache-Control': 'no-cache',
				'X-Requested-With': 'XMLHttpRequest',
				'X-File-Name': encodeURIComponent(file.name),
				'X-File-Size': file.size,
				'X-File-Id': file_id,
				'X-File-Resume': resume,
				'Content-type': 'multipart/mixed'
			},
			onSuccess: function(response) {
				response = JSON.decode(response);

				if(this.options.listview)
					var respcontainer = document.id(subcontainer.get('id') + '_file_' + (filenum + 1));

				if(response.error == 0) {

					if(total < file.size) {
						var percent = (total / file.size) * 100;
						this.fireEvent('onFileProgress', [filenum, percent]);

						if(this.options.listview) {
							respcontainer.set('html', percent.ceil() + '%');
						}

						this.html5send(subcontainer, file_id, file, start + response.size.toInt(), filenum, true)  // Recursive upload
					}
					else {
						this.fireEvent('onFileProgress', [filenum, 100]);

						if(this.options.listview) {
							respcontainer.addClass('mooupload_noerror');
							respcontainer.set('html', this.options.texts.uploaded);

							var parent = respcontainer.getParent();
							var sel = parent.getChildren('div.optionsel');
							sel.set('html', '');
							var optionremove = new Element('a', {
								id: 'filecontrol_remove' + this.filelist.length,
								'class': 'remove'
							});
							sel.grab(optionremove);

							optionremove.addEvent('click', function(e) {
								e.stop();
								if(!confirm(this.options.texts.sure)) {
									return;
								}
								this.filelist[filenum].checked = false;

								parent.getElement('div.result').set('html', this.options.texts.deleting).setStyle('background', 'url("' + this.options.url_root + '/media/mint/js/mooupload/imgs/load_bg_red.gif")').setStyle('color', 'maroon');

								var req = new Request.JSON({
									url: this.options.action_remove_file,
									method: 'post',
									autoCancel: true,
									data: {filename: response.upload_name },
									onComplete: function(json) {
										parent.slide('out');
										setTimeout(function() {
											parent.destroy();
										}, 500);
									}
								}).send();

							}.bind(this));

							if(this.options.allowEditTitle) {
								var filename_div = parent.getChildren('div.filename');
								filename_div.addClass('filenameedit');
								this.addTitleInterface(filename_div, response.row_id);
							}

							if(this.options.allowAddDescr) {
								this.addDescriptionInterface(parent, response.row_id);
							}
						}

						var hiddenInput = new Element('input', {
							'type': 'hidden',
							'name': this.options.formname,
							'value': response.upload_name
						}).inject(parent);

						this.uploadspertime--;

						this.filelist[filenum].uploaded = true;
						this.progressStep(document.id(subcontainer.get('id') + '_progresscont'));

						this.fireEvent('onFileUpload', [filenum, response]);

						if(this.uploadspertime <= this.options.maxuploadspertime)
							this.html5Upload(subcontainer);

					}
				}
				else {

					if(this.options.listview) {
						respcontainer.addClass('mooupload_error');
						respcontainer.set('html', this.options.texts.error);
						this.fireEvent('onSelectError', [response.error, response.name, response.size]);
					}

					this.uploadspertime--;

					this.filelist[filenum].uploaded = true;
					this.progressStep(document.id(subcontainer.get('id') + '_progresscont'));

					this.fireEvent('onFileUpload', [filenum, response]);

					this.fireEvent('onFileUploadError', [filenum, response]);

					if(this.uploadspertime <= this.options.maxuploadspertime)
						this.html5Upload(subcontainer);

				}

			}.bind(this)
		});


		xhr.sendBlob(chunk);

	},

	addDescriptionInterface: function(parent, data_id, descr_text) {
		var description_button = new Element('div', {
			'class': 'filedescr_button',
			'title': this.options.texts.edit_descr
		}).inject(parent);

		var description = new Element('div', {
			'class': 'filedescription',
			'style': 'visibility: hidden;'
		}).inject(parent);

		description_button.addEvent('click', function(descr) {
			if(descr.getStyle('visibility') != 'hidden') {
				descr.fade('hide');
			}
			else {
				descr.fade('show');
			}
		}.pass(description));

		var text_div = new Element('div', {
			'class': 'text_div'
		}).inject(description);

		var caption = new Element('h4', {
			html: this.options.texts.edit_descr,
		}).inject(text_div);

		var descr_textarea = new Element('textarea', {
			'class': 'textarea_descr',
			'cols': 25,
			'rows': 4
		}).inject(text_div);
		descr_textarea.set('html', descr_text);


		var buttons_div = new Element('div', {
			'class': 'buttons_div'
		}).inject(description);

		var close_descr = new Element('div', {
			'class': 'filedescrclose'
		}).inject(buttons_div);

		close_descr.addEvent('click', function(descr) {
			descr.fade('hide');
		}.pass(description));

		var save_descr = new Element('div', {
			'class': 'filedescrsave'
		}).inject(buttons_div);

		var func_filename_descr = function(el, opt) {
			var req = new Request.JSON({
				url: Cobalt.field_call_url,
				method: 'post',
				autoCancel: true,
				data: {
					field_id: opt.field_id,
					func: 'onSaveDescr',
					field: 'upload',
					record_id: opt.record_id,
					id: data_id,
					text: el.getElement('.textarea_descr').value
				},
				onComplete: function(json) {
					if(!json.success) {
						alert(json.error);
					}
					else {
						el.getElement('.textarea_descr').set('value', json.result);
						el.fade('out');
					}
				}
			}).send();
		};

		save_descr.addEvent('click', func_filename_descr.pass([description, this.options]));
	},

	addTitleInterface: function(filename_div, data_id) {
		var func_filename_title = function(el, opt) {
			var input_title = new Element('input', {
				'type': 'text',
				'name': 'filetitle',
				'style': 'width:87%;'
			});

			var save_title = new Element('div', {
				'class': 'filetitlesave',
			});

			var save_func = function(el, opt) {
				var req = new Request.JSON({
					url: Cobalt.field_call_url,
					method: 'post',
					autoCancel: true,
					data: {
						field_id: opt.field_id,
						func: 'onSaveTitle',
						field: 'upload',
						record_id: opt.record_id,
						id: data_id,
						text: input_title.value
					},
					onComplete: function(json) {
						if(!json.success) {
							alert(json.error);
						}
						else {
							el.set('html', json.result);
							input_title.destroy;
							save_title.destroy;
							el.addEvent('click', function() {
								input_title.set('value', el.get('html'));
								el.set('html', '');
								el.adopt(input_title);
								el.adopt(save_title);
								el.removeEvents('click');
							});
						}
					}
				}).send();
			};

			save_title.addEvent('click', save_func.pass([el, opt]));

			input_title.removeEvents();
			input_title.addEvent('keydown', function(event) {
				if(event.key == 'enter') {
					save_func.pass([el, opt]);
				}
			});

			input_title.set('value', el.get('html'));
			el.set('html', '');
			el.adopt(input_title);
			el.adopt(save_title);
			el.removeEvents('click');
		};

		filename_div.addEvent('click', func_filename_title.pass([filename_div, this.options]));
	},

	html5Delete: function(fileindex) {
	},

	// ------------------------- Specific methods for html4 ---------------------

	/*
	 Function: html4
	 Private method

	 Specific method for html4
	 */
	html4: function(subcontainer) {

		var subcontainer_id = subcontainer.get('id');

		// Setup some options
		this.options.multiple = false;

		var iframe = new IFrame({
			id: subcontainer_id + '_frmFile',
			name: subcontainer_id + '_frmFile',

			styles: {
				display: 'none'
			}
		});

		iframe.addEvent('load', function() {

			var response = iframe.contentWindow.document.body.innerHTML;

			if(response != '') {
				this.uploading = false;

				this.html4Upload(subcontainer);

				response = JSON.decode(response);

				if(this.options.listview)
					var respcontainer = document.id(subcontainer_id + '_file_' + (response.key + 1));

				if(response.error > 0) {
					if(this.options.listview) {
						respcontainer.addClass('mooupload_error');
						respcontainer.set('html', this.options.texts.error);
					}

					this.fireEvent('onFileUploadError', [response.key, response]);
				}
				else {

					this.filelist[response.key].uploaded = true;

					// Complete file information from server side
					this.filelist[response.key].size = response.size;

					if(this.options.listview) {
						respcontainer.addClass('mooupload_noerror');
						respcontainer.set('html', this.options.texts.uploaded);

						respcontainer.getPrevious('.filesize').set('html', response.size + ' bytes');

						var parent = respcontainer.getParent();
						var sel = parent.getChildren('div.optionsel');
						sel.set('html', '');
						var optionremove = new Element('a', {
							id: 'filecontrol_remove' + this.filelist.length,
							'class': 'remove'
						});
						sel.grab(optionremove);

						optionremove.addEvent('click', function(e) {
							e.stop();
							if(!confirm(this.options.texts.sure)) {
								return;
							}
							this.filesCount--;
							var req = new Request.JSON({
								url: this.options.action_remove_file,
								method: 'post',
								autoCancel: true,
								data: {filename: response.upload_name },
								onComplete: function(json) {
									parent.destroy();
								}
							}).send();

						}.bind(this));
					}

					var hiddenInput = new Element('input', {
						'type': 'hidden',
						'name': this.options.formname,
						'value': response.upload_name
					}).inject(parent);
				}

				this.progressStep(document.id(subcontainer.get('id') + '_progresscont'));

				this.fireEvent('onFileUpload', [response.key, response]);

			}

		}.bind(this)
		).inject(subcontainer);


		this.baseHtml(subcontainer);

		// Trigger for html file input
		this.htmlAddFile(subcontainer);

	},

	html4Upload: function(subcontainer) {

		// var this.filenum = 0;

		if(!this.uploading) {

			this.getForms(subcontainer).each(function(el) {

				var file = this.filelist[this.filenum];

				if(file != undefined && !this.uploading) {
					if(file.checked && !file.uploading) {
						file.uploading = true;
						this.uploading = true;
						var submit = el.submit();
					}
				}

				this.filenum++;

			}.bind(this));

		}

	},

	html4Delete: function(fileindex) {
	},

	getInputFileName: function(element) {
		var pieces = element.get('value').split(/(\\|\/)/g);

		return pieces[pieces.length - 1];
	}

}); // end MooUpload class

