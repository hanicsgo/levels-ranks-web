function liveUp(e)
{
	$.ajax({
		type:"POST",
		url:window.location.href,
		data:{live:e}
	})
}

function to_sale(e)
{
	$.ajax({
		type:"POST",
		url:window.location.href,
		data:{sale:e},
		success:function(e){
			let t=jQuery.parseJSON(e.trim());
			t.bal&&($(".material-balance").html(t.bal),Swal.close())
		}
	})
}

function pick_up(e)
{
	$.ajax({
		type:"POST",
		url:window.location.href,
		data:{up:e},
		success:function(){Swal.close()}
	})
}

function pick_up_wins(a){
	$.ajax({
		type:"POST",
		url:window.location.href,
		data:{up:a},
		success:function(e){
			let t=jQuery.parseJSON(e.trim());
			if(t.allow)
			{
				Swal.fire({allowOutsideClick: !1,allowEscapeKey: !1,allowEnterKey: !1, showConfirmButton:!1,background:t.style,html:t.html});
			}
			else Swal.fire({showConfirmButton:!1,background:t.style,html:t.html});

			$("#rem"+a).html('<a onclick="my_wins('+a+')">WIN</a>')
		}
	})
}
function to_sale_wins(a)
{
	$.ajax({
		type:"POST",
		url:window.location.href,
		data:{sale:a},
		success:function(e){
			let t=jQuery.parseJSON(e.trim());
			t.bal&&($("#rem"+a).html("<a>ПРОДАНО</a>"),
				$(".material-balance").html(t.bal))
		}
	})
}
function my_wins(e)
{
	$.ajax({
		type:"POST",
		url:window.location.href,
		data:{wins:e},
		success:function(e){
			let t=jQuery.parseJSON(e.trim());
			Swal.fire({showConfirmButton:!1,background:t.style,html:t.html})
		}
	})
}
function load_roulette(e){
	$.ajax({
		type:"POST",
		url:window.location.href,
		data:{case_id:e},
		success:function(e){
			jQuery.parseJSON(e.trim()).forEach(function(e){
				$(".roulette-inner").prepend($(rouletteHtml(e.style,e.data,e.img,e.desc,e.name)))}),
			options={},
			rouletter=$("div#roulette"),
			rouletter.roulette(options)
		}
	})
}
function rouletteHtml(e,t,a,i,o)
{
	return'<div class="subject-block '+e+'" data-value="'+t+'"><div class="b-top"></div><div class="b-bottom"></div><div class="b-left"></div><div class="b-right"></div><div class="subject-services"><div class="subject-fix"><div class="subject-image-wrapper"><img width="120" class="subject-image" src="'+a+'" alt="'+o+" "+i+'"></div><div class="subject"><span>'+o+"</span><span>"+i+"</span></div></div></div></div>"
}
function get_random(e,t)
{
	return Math.floor(Math.random()*(t-e+1))+e
}
function open_case(e){$.ajax({type:"POST",url:window.location.href,data:{case_id_open:e},success:function(e){$("#open-case").attr("disabled","true"),$("#open-case-fast").attr("disabled","true");var t=jQuery.parseJSON(e.trim());return t.error?(Swal.fire("",t.error,""),$("#open-case-fast").removeAttr("disabled"),void $("#open-case").removeAttr("disabled")):t.message?(Swal.fire({showConfirmButton:!1,width:400,background:t.style,html:t.message}),t.date&&jQuery(document).ready(function(){jQuery(".eTimer").eTimer({etType:0,etDate:t.date,etTitleText:"",etTitleSize:10,etShowSign:1,etSep:":",etFontFamily:"Arial Black",etTextColor:"black",etPaddingTB:0,etPaddingLR:0,etBackground:"transparent",etBorderSize:0,etBorderRadius:0,etBorderColor:"white",etShadow:" 0px 0px 0px 0px #333333",etLastUnit:4,etNumberFontFamily:"Arial Black",etNumberSize:20,etNumberColor:"white",etNumberPaddingTB:0,etNumberPaddingLR:3,etNumberBackground:"var(--span-color)",etNumberBorderSize:0,etNumberBorderRadius:0,etNumberBorderColor:"white",etNumberShadow:"inset 0px 0px 9px 0px rgba(0, 0, 0, 0.5)"})}),$("#open-case-fast").removeAttr("disabled"),void $("#open-case").removeAttr("disabled")):($(".material-balance").html(t.ubal),options={stopImageNumber:t.win,duration:get_random(1,2),startCallback:function(){$("#open-case").attr("disabled","true"),$("#open-case-fast").attr("disabled","true")},stopCallback:function(e){setTimeout(function(){$("#open-case-fast").removeAttr("disabled"),$("#open-case").removeAttr("disabled"),play_case_sound("drop"),Swal.fire({allowOutsideClick:!1,allowEscapeKey:!1,allowEnterKey:!1,showConfirmButton:!1,width:400,background:t.style,html:t.html}),t.wcash&&($(".material-balance").html(t.wcash),setTimeout(function(){Swal.close()},2e3)),audio_i=0,liveUp(t.live),live_refresh()},600)}},rouletter.roulette("option",options),void rouletter.roulette("start"))}})}function open_case_fast(e){$.ajax({type:"POST",url:window.location.href,data:{case_id_open:e},success:function(e){$("#open-case").attr("disabled","true"),$("#open-case-fast").attr("disabled","true");var t=jQuery.parseJSON(e.trim());return t.error?(Swal.fire("",t.error,""),$("#open-case").removeAttr("disabled"),void $("#open-case-fast").removeAttr("disabled")):t.message?(Swal.fire({showConfirmButton:!1,width:400,background:t.style,html:t.message}),t.date&&jQuery(document).ready(function(){jQuery(".eTimer").eTimer({etType:0,etDate:t.date,etTitleText:"",etTitleSize:10,etShowSign:1,etSep:":",etFontFamily:"Arial Black",etTextColor:"black",etPaddingTB:0,etPaddingLR:0,etBackground:"transparent",etBorderSize:0,etBorderRadius:0,etBorderColor:"white",etShadow:" 0px 0px 0px 0px #333333",etLastUnit:4,etNumberFontFamily:"Arial Black",etNumberSize:20,etNumberColor:"white",etNumberPaddingTB:0,etNumberPaddingLR:3,etNumberBackground:"#b04311",etNumberBorderSize:0,etNumberBorderRadius:0,etNumberBorderColor:"white",etNumberShadow:"inset 0px 0px 9px 0px rgba(0, 0, 0, 0.5)"})}),$("#open-case").removeAttr("disabled"),void $("#open-case-fast").removeAttr("disabled")):($(".material-balance").html(t.ubal),$("#open-case").removeAttr("disabled"),$("#open-case-fast").removeAttr("disabled"),play_case_sound("drop"),Swal.fire({allowOutsideClick:!1,allowEscapeKey:!1,allowEnterKey:!1,showConfirmButton:!1,width:400,background:t.style,html:t.html}),t.wcash&&($(".material-balance").html(t.wcash),setTimeout(function(){Swal.close()},2e3)),audio_i=0,liveUp(t.live),void live_refresh())}})}function live_entry_create(e,t,a,i,o,r,s){return'<li class="live-item"><a class="live-link live-link-'+s+'" href="?page=cases&case='+e+'"><div class="live-inner-wrap"><div class="live-inner"><div class="live-image-wrap"><img src="'+o+'" class="live-image"></div><div class="live-title">'+a+'</div></div><div class="live-hover"><div class="live-case-image"><img src="'+r+'" alt=""></div><div class="live-username">'+i+"</div></div></div></a></li>"}var live={};function live_refresh(){$.ajax({type:"POST",url:window.location.href,data:{liveLoad:!0},success:function(e){jQuery.parseJSON(e.trim()).forEach(function(e){if(0==live.hasOwnProperty(e.liveid)){$("#live_content").prepend($(live_entry_create(e.id,e.cname,e.sname,e.uname,e.simg,e.cimg,e.style))),live[e.liveid]=!0;var t=document.getElementById("live_content"),a=t.children;for(i=a.length-1;0<=i&&!(i<=14);i--)t.removeChild(a[i])}})}})}function live_load(){live_refresh(),setInterval(live_refresh,5e3)}function set_cookie(e,t,a,i,o,r){document.cookie=e+"="+escape(t)+(a?"; expires="+a:"")+(i?"; path="+i:"")+(o?"; domain="+o:"")+(r?"; secure":"")}function get_cookie(e){var t=" "+document.cookie,a=" "+e+"=",i=null,o=0,r=0;return 0<t.length&&-1!=(o=t.indexOf(a))&&(o+=a.length,-1==(r=t.indexOf(";",o))&&(r=t.length),i=unescape(t.substring(o,r))),i}var audio=[];function roulette_sound(){$("#sound-point").hasClass("sound-on")?($("#sound-point").removeClass("sound-on"),$("#sound-point").addClass("sound-off"),set_cookie("roulette_sound","off"),cases_roulette_sound=2):$("#sound-point").hasClass("sound-off")&&($("#sound-point").removeClass("sound-off"),$("#sound-point").addClass("sound-on"),set_cookie("roulette_sound","on"),cases_roulette_sound=1)}function play_case_sound(e){audio_i++,1==cases_roulette_sound&&(audio[audio_i]=new Audio,audio[audio_i].volume=.06,"scroll"==e&&(audio[audio_i].src="app/modules/module_page_open_case/assets/sounds/scroll.wav"),"drop"==e&&(audio[audio_i].src="app/modules/module_page_open_case/assets/sounds/drop.wav"),audio[audio_i].play())}audio_i=0,cases_roulette_sound=1;

function pick_up_wins_to_server()
{
	$.ajax({
        type: 'POST',
        url: window.location.href,
        data: {
        	win_up_server: $('select[name="wins_to_server"]').val()
        },
        success: function(reuslt)
        {   
            var t = jQuery.parseJSON(reuslt.trim());
            if(t.allow)
			{
				Swal.fire({allowOutsideClick: !1,allowEscapeKey: !1,allowEnterKey: !1, showConfirmButton:!1,background:t.style,html:t.html});
			}
			else Swal.fire({showConfirmButton:!1,background:t.style,html:t.html});
        }
    });
}

function pick_up_wins_accept(a)
{
	$.ajax({
        type: 'POST',
        url: window.location.href,
        data: {
        	win_up_confirm: a
        },
        success: function(reuslt)
        {   
            var t = jQuery.parseJSON(reuslt.trim());
            if(t.allow)
			{
				Swal.fire({allowOutsideClick: !1,allowEscapeKey: !1,allowEnterKey: !1, showConfirmButton:!1,background:t.style,html:t.html});
			}
			else Swal.fire({showConfirmButton:!1,background:t.style,html:t.html});
        }
    });
}

;(function($) {
  var units = {
      en: ['Days', 'Hours', 'Minutes', 'Seconds'],
      ru: ['дней', 'часов', 'минут', 'секунд'],
      sec: [86400, 3600, 60, 1]
    },
    defaults = {
      etType: 1,
      etDate: '0',
      etTitleText: '',
      etTitleSize: 14,
      etShowSign: 'EN',
      etSep: ':',
      etFontFamily: 'Arial',
      etTextColor: 'black',
      etPaddingTB: 0,
      etPaddingLR: 0,
      etBackground: 'transparent',
      etBorderSize: 0,
      etBorderRadius: 0,
      etBorderColor: 'transparent',
      etShadow: '',
      etLastUnit: 4,
      etNumberFontFamily: 'Arial',
      etNumberSize: 32,
      etNumberColor: 'black',
      etNumberPaddingTB: 0,
      etNumberPaddingLR: 0,
      etNumberBackground: 'transparent',
      etNumberBorderSize: 0,
      etNumberBorderRadius: 0,
      etNumberBorderColor: 'transparent',
      etNumberShadow: ''
    };

  $.fn.eTimer = function(options) {
    var config = $.extend({}, defaults, options);

    return this.each(function() {
      var element = $(this),
        date = config.etDate,
        dayNum = 2;

      element.date = function() {
        var now = new Date();
        if (config.etType == 1) {
          date = new Date(now.getFullYear(), now.getMonth(), now.getDate() + 1);
        } else if (config.etType == 2) {
          var day = now.getDay();
          if (day == 0) day = 7;
          date = new Date(now.getFullYear(), now.getMonth(), now.getDate() + 8 - day);
        } else if (config.etType == 3) {
          date = new Date(now.getFullYear(), now.getMonth() + 1, 1);
        } else {
          date = date.split('.');
          date = new Date(date[2], date[1] - 1, date[0], date[3], date[4]);
          if (Math.floor((date - now) / units.sec[0] / 1000) >= 100) dayNum = 3;
        }
      };

      element.layout = function() {
        var unit,
          elClass = element.attr('class').split(' ')[0];
        element.html('').addClass('eTimer').append('<div class="etTitle">' + config.etTitleText + '</div>');
        $.each(units.en, function(i) {
          if (i < config.etLastUnit) {
            unit = $('<div class="etUnit et' + this + '"></div>').appendTo(element).append('<div class="etNumber">0</div>').append('<div class="etNumber">0</div>').after('<div class="etSep">' + config.etSep + '</div>');
            if (i == 0 && dayNum == 3) unit.append('<div class="etNumber">0</div>');
            if (config.etShowSign === 'RU'){
              unit.append('<div class="etSign">' + units.ru[i] + '</div>');
            }
            else {
              unit.append('<div class="etSign">' + units.en[i].toLowerCase() + '</div>');
            }
          }
        });
        element.append('<style type="text/css">.' + elClass + ' {display: inline-block; line-height: normal; font-family: ' + config.etFontFamily + '; color: ' + config.etTextColor + '; padding: ' + config.etPaddingTB + 'px ' + config.etPaddingLR + 'px; background: ' + config.etBackground + '; border: ' + config.etBorderSize + 'px solid ' + config.etBorderColor + '; -webkit-border-radius: ' + config.etBorderRadius + 'px; -moz-border-radius: ' + config.etBorderRadius + 'px; border-radius: ' + config.etBorderRadius + 'px; -webkit-box-shadow: ' + config.etShadow + '; -moz-box-shadow: ' + config.etShadow + '; box-shadow: ' + config.etShadow + ';} .' + elClass + ' .etTitle {margin-bottom: 10px; font-size: ' + config.etTitleSize + 'px;} .' + elClass + ' .etUnit {display: inline-block;} .' + elClass + ' .etUnit .etNumber {display: inline-block; margin: 1px; text-align: center; font-family: ' + config.etNumberFontFamily + '; font-size: ' + config.etNumberSize + 'px; color: ' + config.etNumberColor + '; padding: ' + config.etNumberPaddingTB + 'px ' + config.etNumberPaddingLR + 'px; background: ' + config.etNumberBackground + '; border: ' + config.etNumberBorderSize + 'px solid ' + config.etNumberBorderColor + '; -webkit-border-radius: ' + config.etNumberBorderRadius + 'px; -moz-border-radius: ' + config.etNumberBorderRadius + 'px; border-radius: ' + config.etNumberBorderRadius + 'px; -webkit-box-shadow: ' + config.etNumberShadow + '; -moz-box-shadow: ' + config.etNumberShadow + '; box-shadow: ' + config.etNumberShadow + ';} .' + elClass + ' .etUnit .etSign {text-align: center; font-size: ' + (+config.etNumberSize / 2.5) + 'px;} .' + elClass + ' .etSep {display: inline-block; vertical-align: top; font-size: ' + config.etNumberSize + 'px; padding: ' + (+config.etNumberPaddingTB + +config.etNumberBorderSize) + 'px 5px;} .' + elClass + ' .etSep:last-of-type {display: none;}</style>').append('<style type="text/css">.' + elClass + ' .etUnit .etNumber {width: ' + $('.etNumber:visible').eq(0).css('width') + ';}</style>');
      };

      element.tick = function() {
        var timeLeft = Math.floor((date - new Date()) / 1000),
          unit;
        if (timeLeft < 0) clearInterval(element.data('interval'));
        else {
          $.each(units.en, function(i) {
            if (i < config.etLastUnit) {
              unit = Math.floor(timeLeft / units.sec[i]);
              timeLeft -= unit * units.sec[i];
              if (i == 0 && dayNum == 3) {
                element.find('.et' + this).find('.etNumber').eq(0).text(Math.floor(unit / 100) % 10);
                element.find('.et' + this).find('.etNumber').eq(1).text(Math.floor(unit / 10) % 10);
                element.find('.et' + this).find('.etNumber').eq(2).text(unit % 10);
                if ((Math.floor(unit / 100) % 10) == 0) {
                  dayNum = 2;
                  element.find('.et' + this).find('.etNumber').eq(0).remove();
                }
              } else {
                element.find('.et' + this).find('.etNumber').eq(0).text(Math.floor(unit / 10) % 10);
                element.find('.et' + this).find('.etNumber').eq(1).text(unit % 10);
              }
            }
          });
        }
      };

      clearInterval(element.data('interval'));
      element.date();
      element.layout();
      element.tick();
      element.data('interval', setInterval(function() {
        element.tick()
      }, 1000));
    });
  };
})(jQuery);