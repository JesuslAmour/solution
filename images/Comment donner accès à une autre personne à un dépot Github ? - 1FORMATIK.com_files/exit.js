if($(window).width() > 800)
{ 

var exit = (function($){

	var defaults = {delayregister:0, delayshow:200, hideaftershow:true, displayfreq: 'always', persistcookie: 'exit_shown', fxclass: 'rubberBand', mobileshowafter: 3000, onexit:function(){}}
	//var animatedcssclasses = ["bounce","flash","pulse","rubberBand","shake","swing","tada","wobble","jello","bounceIn","bounceInDown","bounceInLeft","bounceInRight","bounceInUp","fadeIn","fadeInDown","fadeInDownBig","fadeInLeft","fadeInLeftBig","fadeInRight","fadeInRightBig","fadeInUp","fadeInUpBig","flipInX","flipInY","lightSpeedIn","rotateIn","rotateInDownLeft","rotateInDownRight","rotateInUpLeft","rotateInUpRight","slideInUp","slideInDown","slideInLeft","slideInRight","zoomIn","zoomInDown","zoomInLeft","zoomInRight","zoomInUp","rollIn"]
	var animatedcssclasses = ["fadeInUp"]

	var isTouch = (('ontouchstart' in window) || (navigator.msMaxTouchPoints > 0));
	var crossdeviceclickevt = isTouch? 'touchstart' : 'click'

	function getCookie(Name){ 
		var re=new RegExp(Name+"=[^;]+", "i");
		if (document.cookie.match(re))
			return document.cookie.match(re)[0].split("=")[1]
		return null
	}

	function setCookie(name, value, duration){
		var expirestr='', expiredate=new Date()
		if (typeof duration!="undefined"){
			var offsetmin=parseInt(duration) * (/hr/i.test(duration)? 60 : /day/i.test(duration)? 60*24 : 1)
			expiredate.setMinutes(expiredate.getMinutes() + offsetmin)
			expirestr="; expires=" + expiredate.toUTCString()
		}
		document.cookie = name+"="+value+"; path=/"+expirestr
	}

	function makeajaxfriendly(url){
		if (/^http/i.test(url)){
			var dummyurl = document.createElement('a')
			dummyurl.href = url
			return dummyurl.href.replace(RegExp(dummyurl.hostname, 'i'), location.hostname)
		}
		else{
			return url
		}
	}

	var exit = {

		wrappermarkup: '<div id="exitwrapper"><div class="veil"></div></div>',
		$wrapperref: null,
		$contentref: null,
		displaypopup: true,
		delayshowtimer: null,
		settings: null,

		ajaxrequest: function(filepath){
			var ajaxfriendlyurl = makeajaxfriendly(filepath)
			$.ajax({
				url: ajaxfriendlyurl,
				dataType: 'html',
				error:function(ajaxrequest){
					alert('Error fetching content.<br />Server Response: '+ajaxrequest.responseText)
				},
				success:function(content){
					exit.$contentref = $(content).appendTo(document.body)
					exit.setup(exit.$contentref)
				}
			})
		},

		detectexit: function(e){
			if( e.clientY < 60 ){
				this.delayshowtimer = setTimeout(function(){
					exit.showpopup()
					exit.settings.onexit(exit.$contentref)
				}, this.settings.delayshow)
			}
		},

		detectenter: function(e){
			if( e.clientY < 60 ){
				clearTimeout(this.delayshowtimer)
			}
		},

		showpopup: function(){
			if (this.$contentref != null && this.displaypopup == true){
				document.getElementById('exitmodal').style.display='block';
				if (this.settings.randomizefxclass === true){
					this.settings.fxclass = animatedcssclasses[Math.floor(Math.random()*animatedcssclasses.length)]
				}
				this.$wrapperref.addClass('open')
				this.$contentref.addClass(this.settings.fxclass)
				this.displaypopup = false
				if (this.settings.hideaftershow){
					$(document).off('mouseleave.registerexit')
				}
			}
		},

		hidepopup: function(){
			document.getElementById('exitads').style.display='none';
			this.$wrapperref.removeClass('open')
			this.$contentref.removeClass(this.settings.fxclass)
			this.displaypopup = true
		},

		setup: function($content){
			this.$contentref.addClass('animated')
			this.$wrapperref = $(this.wrappermarkup).appendTo(document.body)
			this.$wrapperref.append(this.$contentref)
			this.$wrapperref.find('.veil').on(crossdeviceclickevt, function(){
				exit.hidepopup()
			})
			if (this.settings.displayfreq != 'always'){
				if (this.settings.displayfreq == 'session'){
					setCookie(this.settings.persistcookie, 'yes')
				}
				else if (/\d+(hr|day)/i.test(this.settings.displayfreq)){
					setCookie(this.settings.persistcookie, 'yes', this.settings.displayfreq)
					setCookie(this.settings.persistcookie + '_duration', this.settings.displayfreq, this.settings.displayfreq)
				}
			}
		},

		init: function(options){

			var s = $.extend({}, defaults, options)

			var persistduration = getCookie(s.persistcookie + '_duration')
			if (persistduration && (s.displayfreq == 'session' || s.displayfreq != persistduration)){
				setCookie(s.persistcookie, 'yes', -1)
				setCookie(s.persistcookie + '_duration', '', -1)
			}
			if (s.displayfreq != 'always' && getCookie(s.persistcookie)){ 
				return
			}	

			if (s.fxclass == 'random'){
				s.randomizefxclass = true
			}
			this.settings = s
			if (s.contentsource[0] == 'ajax'){
				this.ajaxrequest(s.contentsource[1])
			}
			else if (s.contentsource[0] == 'id'){
				this.$contentref = $('#' + s.contentsource[1]).appendTo(document.body)
				this.setup(this.$contentref)
			}
			else if (s.contentsource[0] == 'inline'){
				this.$contentref = $(s.contentsource[1]).appendTo(document.body)
				this.setup(this.$contentref)
			}
			setTimeout(function(){
				$(document).on('mouseleave.registerexit', function(e){
					exit.detectexit(e)
				})
				$(document).on('mouseenter.registerenter', function(e){
					exit.detectenter(e)
				})
			}, s.delayregister)

			if (s.mobileshowafter > 0){
				$(document).one('touchstart', function(){
					setTimeout(function(){
						exit.showpopup()
					}, s.mobileshowafter)			
				})
			}
		}
	}

	return exit


})(jQuery);

}