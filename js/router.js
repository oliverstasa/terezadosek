/*
ou shit router!
lady gaga would aprove
*/
import {scriptToScreen, changeNextButton} from './main.js';



export function page(url) {



  // vars
      // 500ms time to fadeout in css
  var delay = 500;



  // start some loading bar?



  // stop everything
  $('.loadingStep').stop(true, false).clearQueue().fadeOut();



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
    if (!obj.error.length) {



      /*
      creates url to adress bar and title of website
      */
      // craft new url adress
      var newUrl = location.protocol+'//'+location.host+''+url;

      // set tab title
      document.title = obj.pageTitle;
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



                      // video preload - create request
                      var req = new XMLHttpRequest();

                      // load whole video in the request
                      req.open('GET', obj.videoUrl, true);
                      req.responseType = 'blob';

                      // when loaded
                      req.onload = function() {
                        // stat is 200 = ok
                         if (this.status === 200) {


                           // get new video url
                            var videoBlob = this.response,
                                vid = URL.createObjectURL(videoBlob),
                                video = document.querySelector('video');

                              // set loaded video to video element, and fade it in
                              video.src = vid;
                              // video duration global var
                              $(video).on('loadedmetadata', function(){
                                window.videoDur = video.duration;
                              });



                                  // check if video can 100% play through
                                  $(video).on('canplaythrough', function(){

                                    // show video, hide loading
                                    $('.loadingText').remove();
                                    $('video').removeClass('fadeOut');

                                    // unlock abilities
                                    $('#script').addClass('grabber');
                                    window.scriptAbilities = true;



                                        // countdown before animation + playback starts
                                        $('.loadingStep').removeClass('loading').animate({width: 0}, 2000, 'linear', function(){
                                          if (!window.selfHandle) {
                                            scriptToScreen('play');
                                          }
                                        });



                                  });

                         }
                      }

                      // if error
                      req.onerror = function() {
                        alert('error while loading video, sorry not my fault');
                      }

                      // make preload happen
                      req.send();


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

            // remove old page div
            $('.oldPage').remove();
            // animate in the new page with css
            $('.pg').removeClass('fadeIn');

            // set next href destination for next button
            $('#next').attr('href', obj.hrefTo);
            // make next button come back from abys
            changeNextButton('in');

        // end of timeout
        }, delay);



      // if there was data error during fetching (php)
      } else {

        console.log(res);
        alert('data error');

      }



    // some error during content fetching, probably parsing json
    } catch (e) {

      console.log(res, e);
      alert('content error, go home');

    }



  // end of $.post content fetching
  });



// end of function page
}
