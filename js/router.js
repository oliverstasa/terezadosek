/*
ou shit router!
lady gaga would aprove
*/
import {scriptToScreen, loading, changeNextButton, lang} from './main.js';



export function page(url) {



  // vars
      // 500ms time to fadeout in css
  var delay = 500;



  // start some loading bar?
  loading('on');



  // stop everything
  $('.loadingStep').stop(true, false).clearQueue().fadeOut();
  // calcel request if any
  if (window.reqVideo) {
    // cancel request
    window.reqVideo.abort();
    // set request to false, so this doesnt go arround
    window.reqVideo = false;
    // and cancel loading interval
    clearInterval(window.longLoadingText);
  }



  /*
  content fetch
  */
  $.post('/php/fetchContent.php', {url: url}, function(res){


    /*
    try parsing the result
    */
    try {



    // JSON the result
    var obj = JSON.parse(res);
    console.log(obj);



    // check for errors
    if (obj.error.length) {

      console.log(res, obj.error);

    }



      /*
      creates url to adress bar and title of website
      */
      // craft new url adress
      var newUrl = location.protocol+'//'+location.host+''+url;

      // set tab title
      document.title = 'Tereza Dosek — '+obj.pageTitle;
      // set url in adress bar
      window.history.pushState("object or string", obj.pageTitle, newUrl);



      /*
      place fetched content
      ...also known as
      contentinatolinant
      */



        // mark old page div so that it can be removed later
        $('.pg').addClass('oldPage');


        // create HTML
        $('body').append(obj.contentHtml);


        // type of content
        switch(obj.pageType) {


          /*
          basic text page
          */
          case 'textContent':



              // disable any video-lasting animations
              $('.loadingStep, #script').stop(true, false).clearQueue().fadeOut();
              window.scriptAbilities = false;
              window.selfHandle = false;


          break;


          /*
          script to screen page
          */
          case 'scriptToScreen':



              // if abilities are still disabled, preload video
              // thats because if they are enabled means video has been loaded
              // so this function doesnt happen twice (for some reason it sometimes did)
              if (!window.scriptAbilities) {




                  // get internet speed coeficient to window.downloadSpeed
                  var dump = new Image(),
                      startTime = (new Date()).getTime(),
                      // the t=time is important so that the file doesnt get cached
                      //file = '/data/4kb.png?t='+startTime,
                      //size = 4096,
                      file = '/data/28kb.png?t='+startTime,
                      size = 28672;

                      // set dump image src
                      dump.src = file;
                      // when dump image is finaly after 20ms loaded :D
                      dump.onload = function() {

                          // get time difference
                          var endTime = (new Date()).getTime(),
                              downloadTime = (endTime-startTime)/1000;

                          // calculate amount of kilobites you can download per sec
                          // size*8/1024 = size of file in kbit, 1/downloadTime = fractions in 1s
                          window.kbps = (size*8/1024*(1/downloadTime)).toFixed(2);

                          // destroy the image
                          dump = null;


                          // now start loading the video...



                          // video preload - create request
                          window.reqVideo = new XMLHttpRequest();

                          // note when download started
                          var videoStartTime = (new Date()).getTime();

                          // load whole video in the request
                          window.reqVideo.open('GET', obj.videoUrl+'?t='+videoStartTime, true);
                          window.reqVideo.responseType = 'blob';

                          // when loaded
                          window.reqVideo.onload = function() {
                             // stat is 200 = ok
                             if (this.status === 200) {



                                // get new video url
                                var videoBlob = this.response,
                                    vid = URL.createObjectURL(videoBlob),
                                    video = document.querySelector('video');



                                // set loaded video to video element
                                video.src = vid;
                                // video duration global var
                                $(video).one('loadedmetadata', function(){
                                  window.videoDur = video.duration;
                                });



                                      // check if video can 100% play through and fade it in
                                      $(video).one('canplaythrough', function(){

                                        // note when video downloading ended
                                        var videoEndTime = (new Date()).getTime(),
                                            videoLoadTime = (videoEndTime-videoStartTime)/1000;

                                        console.log('load time: '+videoLoadTime+'s\naccuracy: '+(obj.videoSize/window.kbps/videoLoadTime));

                                        // show video, hide loading
                                        $('#countdown').css({height: 0, background: 'linear-gradient(blue, transparent)'}).delay(1000).queue(function(){$(this).remove().dequeue();});
                                        $('.loadingText').fadeOut(500, function(){$(this).remove();});
                                        $('video').removeClass('fadeOut');

                                        // unlock abilities
                                        $('#script').addClass('scrollable grabber');
                                        window.scriptAbilities = true;

                                        // show controls + scrollme info-button
                                        $('.controls, #scrollMe').fadeIn();

                                        // cancel interval for changing loading text-lines
                                        clearInterval(window.longLoadingText);


                                        // auto-hide icons when video starts to play
                                        if (!$('#next').hasClass('away')) {
                                          $(document).trigger('mousemove');
                                        }


                                            // countdown before animation + playback starts
                                            $('.loadingStep').removeClass('loading').animate({width: 0}, 2000, 'linear', function(){
                                              if (!window.selfHandle) {

                                                // play script+video
                                                scriptToScreen('play');

                                              }
                                            });



                                      });

                             }
                          }

                          // if error
                          window.reqVideo.onerror = function(e) {
                            console.log(e);
                            // this sometimes happens, when page skipping, so i decided to ignore the error, because it works anyway
                            //alert('error while loading video, sorry not my fault');
                          }

                          // make preload happen
                          window.reqVideo.send();



                          // loading = toggle messages
                          var itt = 0,
                              downloadTimeCalc = Math.round(obj.videoSize/window.kbps), //.toFixed(0)
                              tooLongText = lang('…to je moc dobrý, na to, aby to byla pravda…<br>pomalé připojení, může to trvat věčně', '…this is too good to be true…<br>slow connection, it may take forever'),
                              kecy = lang(['chytám fráze – vyčkejte', 'co je, sakra, sous-vide?', 'vypadá to dobře, že jo', 'dokud toho není kýbl, není to kafe'],
                                          ['catching phrases – bear with me', 'what the hell is sous-vide?', 'looks cool doesn’t it', 'unless it comes in a bucket it’s not a coffee']);

                              console.log('download time\nmy estimate: '+downloadTimeCalc+'s');

                              window.longLoadingText = setInterval(function(){

                                // each 5 sec change the text line
                                if (itt % 5 == 0 && itt > 0) {

                                  // pick randomly new text line
                                  var rand = false,
                                      kec = false;
                                  // diferent than before
                                  while (!rand || $('#bearWithMe').hasClass('k'+rand)) {
                                    rand = Math.floor(Math.random()*kecy.length);
                                  }

                                  // not waiting too long = rand kec
                                  if (itt < 20*4) {
                                    kec = kecy[rand]
                                  // yes, throw an error
                                  } else {
                                    kec = tooLongText;
                                    clearInterval(window.longLoadingText);
                                  }

                                  // set new text line
                                  $('.loadingText').fadeOut(250, function(){

                                    $('#bearWithMe').html(kec).removeAttr('class').addClass('k'+rand);
                                    $(this).fadeIn();

                                  });

                                }

                                // calc height of coundown
                                if ((downloadTimeCalc-itt) > 0) {
                                  var countdownSec = downloadTimeCalc-itt;
                                } else {
                                  var countdownSec = 1;
                                }
                                // set height of coundown
                                $('#countdown').css({height: ((countdownSec/downloadTimeCalc)*50)+'vh'});

                                // add a itteration
                                itt++;

                              }, 1000);



                      // end of onload dump image speed checker
                      }



              } else {

                // this happens when scriptToScreen has still set window.scriptAbilities to true
                // but never the less - it seems to have no impact, so i leave the error in comment
                // alert('error, scriptToScreen has not ended properly');

              }


          break;

        // end of type-of-content switch
        // content is now in place
        }



        /*
        animating the page switch
        */
        // remove current content
        $('.pg.oldPage').addClass('fadeOut');

        // animate in new page
        setTimeout(function(){

            // end loading
            loading('off');

            // remove old page div
            $('.oldPage').remove();
            // animate in the new page with css
            $('.pg').removeClass('fadeIn');

            // set next href destination for next button
            $('#next').attr('href', obj.hrefTo);
            // make next button come back from abys
            changeNextButton('in');

            if (obj.headder == 'admin') {
              $('.pg').addClass('admin');
            }

        // end of timeout
        }, delay);



      // if there was data error during fetching (php)
      // took care of this other way
      /*
      } else {

        console.log(res);
        alert('data error');

      }
      */



    // some error during content fetching, probably parsing json
    } catch (e) {

      console.log(res, e);
      alert('content error, go home');

    }



  // end of $.post content fetching
  });



// end of function page
}
