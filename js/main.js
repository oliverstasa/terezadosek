/*
* TERMS OF USE main.js
* Open source under the BSD License.
* Copyright 2020 Oliver Stasa. All rights reserved.
*/


/*
*
*
export function SCRIPT TO SCREEN :-*
event listeners
+ lady gaga 4 ever
*
*
*/


/*
import
*/
import {page} from './router.js';



/*
def vars
*/
window.playState = 'stop';
window.muteState = 'mute';
window.scriptAbilities = false;
window.loadingPage = false;
window.reqVideo = false;
window.moveTimeout;
window.kbps;



/*
on content load or popstate (browser navig)
*/
$(window).on('load popstate', function(){


  // adress from bar
  var url = window.location.pathname;
  // page function
  page(url);


  // trigger mousemove to hide arrow in its timeout
  $(document).trigger('mousemove');


});



/*
on mouse move make buttons visible
*/
$(document).on('mousemove touch touchmove', function(e){

  var next = $('#next'),
      controls = $('.controls'),
      scrollMe = $('#scrollMe');

    // each time cursor moves, timeout is cleared
    clearTimeout(window.moveTimeout);

    // if controls are hidden, show
    if (next.hasClass('away')) {

      next.removeClass('away');
      controls.fadeIn();
      scrollMe.stop(true, false).fadeIn();

    // if cursor is not on controls or next button, set timeout to hide them
    } else if ($(e.target).parent().attr('class') != 'controls' && $(e.target).parent().attr('id') != 'next') {

      window.moveTimeout = setTimeout(function(){
        next.addClass('away');
        controls.fadeOut();
        scrollMe.stop(true, false).fadeOut();
      }, 1500);

    }

});



/*
buttons and actions: play, stop, repeat, mute, sound;
*/
// PLAY ▶
$(document).on('click touch', '#play', function(){
  scriptToScreen('play');
});
// STOP ◼
$(document).on('click touch', '#stop', function(){
  scriptToScreen('stop');
});
// END ↺
$(document).on('click touch', '#end', function(){
  document.querySelector('video').currentTime = 0;
  $('#script').scrollTop(0);
  progressBar(100, 0);
  $('#end').hide();
  $('#play').show();
});
// SOUND 🕪
$(document).on('click touch', '#sound', function(){
  window.muteState = 'sound';
  document.querySelector('video').muted = false;
  $('#sound').hide();
  $('#mute').show();
});
// MUTE 🕨
$(document).on('click touch', '#mute', function(){
  window.muteState = 'mute';
  document.querySelector('video').muted = true;
  $('#mute').hide();
  $('#sound').show();
});



/*
showing title for div with title
*/
$(document).on('mouseenter', '.title', function(e){

  var div = $(this),
      title = div.attr('dataTitle'),
      win = {'w': $(window).width(),
             'h': $(window).height()},
      pos = div.offset();

      if (!$('#title').length) {
        $('body').append('<div id="title"></div>');
      } else {
        $('#title').html('');
      }

      $('#title').removeClass()
                 .css({top: pos.top+win.h/100*7.5, left: pos.left})
                 .html(title)
                 .stop(true, false)
                 .fadeIn();


});
$(document).on('mouseleave wheel', '.title', function(e){

  $('#title').stop(true, false).fadeOut(500, function(){
    $(this).remove();
  });

});



/*
keyboard eventy
control over scriptToScreen actions
*/
$(document).on('keydown', function(e){

  // only for page with #script
  if ($('#script').length) {

    var keyCode = (e.keyCode?e.keyCode:e.which);
    switch (keyCode) {
      // spacebar
      case 32:
        // prevent space from scrolling script
        e.preventDefault();
        // toggle start/stop
        scriptToScreen('toggle');
      break;
      // M, m
      case 77: case 109:
        // toggle mute/unmute video
        switch(window.muteState) {
          case 'mute': $('#sound').trigger('click'); break;
          case 'sound': $('#mute').trigger('click'); break;
        }
      break;
      // arrow down, arrow up, page up, page down, home, end
      case 40: case 38: case 33: case 34: case 36: case 35:
        // dont want any key interuptions = they should work, but dont for some reason
        e.preventDefault();
      break;
    }

  }

  // global prevent
  // e.preventDefault();

});



/*
control over script playback
*/
export function scriptToScreen(state){

  // if video is loaded
  if (window.scriptAbilities) {

  switch (state) {

    // start script rolling and play video
    case 'play':

      // global play status
      window.playState = 'play';

      var script = $('#script'),
          firstP = script.find('p').first().position().top, // :eq(1)
          scriptLen = script[0].scrollHeight-script.height(),
          scriptPos = script.scrollTop(),
          // videoPosTime is not calculated acuuratly
          videoPosTime = window.videoDur/100*(scriptPos/scriptLen*100),
          scrollFirst = 0;

      // button display/hide
      $('#play, #end').hide();
      $('#stop').show();

      // remove loading
      $('.loadingStep').stop(true, false).clearQueue().fadeOut();

      // prevents function to intercepting video playback with
      window.selfHandle = false;

      // set video to same position (thats discutable...)
      document.querySelector('video').currentTime = videoPosTime;
      // start video playback
      document.querySelector('video').play();

      // if scrollPos is above "first scene line", scroll to it
      if (script.scrollTop() < firstP/2-scriptPos) {
        scrollFirst = 1000;
        script.animate({scrollTop: firstP/2},
                       {duration: scrollFirst,
                        step: function(){
                           // move the progress bar
                           progressBar(scriptLen, $(this).scrollTop());
                        },
                        easing: 'easeInSine'
                       });
      }

      // starts animating div according to video playback position
      setTimeout(function(){

        script.stop(true, true)
              .animate({scrollTop: scriptLen},
                       {duration: (window.videoDur-videoPosTime)*1000-scrollFirst,
                         step: function(){
                           // move the progress bar
                           progressBar(scriptLen, $(this).scrollTop());
                         },
                         easing: 'linear',
                         complete: function(){
                           // make the last step for progress bar
                           progressBar(scriptLen, $(this).scrollTop());
                           // stop the fun
                           scriptToScreen('end');
                         }
                       });

      }, scrollFirst);

    break;

    // stop script rolling and pause video
    case 'stop':

      // global play status
      window.playState = 'stop';
      // allows browsing through video, diallows other events
      window.selfHandle = true;

      // button display/hide
      $('#play').show();
      $('#stop, #end').hide();

      // pause video playback
      document.querySelector('video').pause();
      // pause script playback
      $('#script, .loadingStep').stop(true, false).clearQueue();

    break;
    case 'end':

      // global play status
      window.playState = 'end';
      // allows browsing through video, diallows other events
      window.selfHandle = true;

      // make sure video is stopped at the end
      document.querySelector('video').pause();
      document.querySelector('video').currentTime = window.videoDur;

      // button display/hide
      $('.controls').fadeIn();
      $('#end').show();
      $('#stop, #play').hide();

      // show all controls
      $(document).trigger('mousemove');

    break;

    // auto-toggle between states
    case 'toggle':

      switch(window.playState) {
        case 'end':
          $('#end').trigger('click');
          setTimeout(function(){$('#play').trigger('click');}, 100);
        break;
        case 'stop': $('#play').trigger('click'); break;
        case 'play': $('#stop').trigger('click'); break;
      }

    break;

  }

  // endif video is loaded
  }

}



/*
sets width of "progress bar" for other functions
*/
function progressBar(scriptLen, scriptPos) {

  if (scriptLen == 'auto' || scriptPos == 'auto') {

    var script = $('#script'),
        scriptLen = script[0].scrollHeight-script.height(),
        scriptPos = script.scrollTop();

  }

  var scriptPercent = scriptPos/scriptLen*100;

  $('.progress').width(scriptPercent+'%');

}



/*
main function for event listening of #script and video actions
on start of interacting with the #script element
*/

$(document).on('wheel mousedown touchstart', '#script', function(){


  // pauses the video and script
  if (window.playState != 'stop') {
    scriptToScreen('stop');
  }


});


$(document).on('scroll wheel touchmove', '#script', function(e){


  if (window.selfHandle) {


      // script handeling and calculations
      var script = $(this),
          scriptLen = script[0].scrollHeight - script.height(),
          scriptPos = script.scrollTop(),
          scriptPercent = scriptPos/scriptLen*100,
          // video handling
          videoLen = window.videoDur,
          timeSetup = videoLen/100*scriptPercent,
          decimal = 1000, // 10 = 0.1, 100 = 0.01
          timeSetupFloor = Math.floor(timeSetup*decimal)/decimal;


      // console.log(scriptLen, scriptPos, scriptPercent, videoLen, timeSetup, timeSetupFloor);

      // if there is scrollme button, remove it
      if ($('#scrollMe').length && !$('#scrollMe').hasClass('onMyWay')) {

        var scrollMe = $('#scrollMe');
        scrollMe.addClass('onMyWay');

        // remove from session
        $.post('/php/scrollMe.php', {'scrolled': 'true'}, function(res){

          // remove from html
          scrollMe.fadeOut(500, function(){
            scrollMe.remove();
          });

        });

      }



      // set width of "progress bar"
      progressBar(scriptLen, scriptPos);


      // video is not playing = can be handled, set frame to according position
      // video has to contain keyframes at each 5th or 10th frame, videos with 50+ will result in lag
      // more about that: https://docs.microsoft.com/cs-cz/windows/win32/wmformat/configuring-video-streams-for-seeking-performance
      document.querySelector('video').currentTime = timeSetupFloor;


      // button setup
      // if div is on its scrolling end, display END(repeat) button
      if (scriptPos > scriptLen-1) {
        $('#end').show();
        $('#stop, #play').hide();
      // display pause button
      } else if ($('#play').is(':hidden')) {
        $('#play').show();
        $('#stop, #end').hide();
      }


  } else {

    // nothing ever happens
    e.preventDefault();

  }


});



/*
navigation = on #next click listener
*/
$(document).on('click touch', '#next', function(e){ 

  // send href to the page() function
  var link = $(this);
  page(link.attr('href'));


  // make some animation
  changeNextButton('out');


  // prevents default a:href action
  e.preventDefault();

});



/*
change color of next
craft random hex color
*/
export function changeNextButton(action) {

  var next = $('#next');

  switch (action) {
    case 'out':

      // animates next button away
      next.css({right: '20vh', opacity: 0}); // 'transform': 'scale(0.9)'

    break;
    case 'in':

      // make new colors in next button
      // $('#next').css({'background-image': 'radial-gradient(circle at 100%, '+newHex('dark')+' 0%, '+newHex('random')+' 51%, '+newHex('light')+' 100%)'});
                                        // linear-gradient to right                 light                                             dark

      // animates next button back in
      next.css({right: 0, opacity: 1}); // 'transform': 'scale(1)'

    break;

  }

}
// creates random hex color
function newHex(type){

  // random, light or dark color
  switch (type) {
    case 'random':
      // return '#'+Math.floor(Math.random()*16777215).toString(16);
      return 'blue';
    break;
    case 'light':
      var letters = '9ABCDEF'.split('');
    break;
    case 'dark':
      var letters = '2345678'.split('');
    break;
  }

  // if light or dark
  if (type != 'random') {
    var color = '';
    for (var i = 0; i < 6; i++) {
        // ?? keep / delete ??
        if (i == 2 || i == 3) {
          color += Math.floor(Math.random()*2);
        } else {
          color += letters[Math.floor(Math.random()*letters.length)];
        }
    }
    return '#'+color;
  }

}



/*
loading
*/
export function loading(e) {

  switch (e) {

    // start loading bar
    case 'on':

      // if no loading has started
      if (!window.loadingPage) {

        // create and show loading bar
        $('body').prepend('<div id="loading"></div>');
          $('#loading').stop(true, false).fadeIn(100);

          // set safety time for page reload to end the misery
          window.loadingPage = setTimeout(function(){
            window.location.reload;
          }, 60*1000);

      }

    break;

    // end loading
    case 'off':

      // clear safety timeout
      clearTimeout(window.loadingPage);
      window.loadingPage = false;

      // remove loading bar
      $('#loading').stop(true, false).fadeOut(500,
      function() {
        $('#loading').remove();
      });

    break;

  }

}



/*
switch language
*/
export function lang(cs, en) {

  switch($('html').attr('lang')) {
    case 'cs': return cs; break;
    default: case 'en': return en; break;
  }

}



/*
copy email
*/
$(document).on('click touch', '.mailto', function(e){
  
  // dont use a:href
  e.preventDefault();

  // vars
  var mail = $(this).html(),
      $temp = $("<input>");

      // copy the mail to clipboard
      $('body').append($temp);
      $temp.val(mail).select();
      document.execCommand("copy");
      $temp.remove();

  // show confirmation
  $('#title').stop(true, true).html('→ '+lang('Zkopírováno!', 'Copied!')).addClass('hop'); // .delay(500).queue(function(){$(this).removeClass('hop').dequeue();});

});


/*
cool effect for next button
disabled for no obvious reason
*/
/*
$(document).on('mousemove', '#next', function(e){

  var button = $(this),
      curPos = {'x': e.pageX,
                'y': e.pageY},
      buttonCords = {'top': button.position().top},
      pxFromTop = curPos.y-buttonCords.top;

      //button.find('div').css({'background-position-y': pxFromTop+'px'});
      console.log(pxFromTop);

});
*/