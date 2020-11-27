/*
admin fce
super secret
lady gaga
*/



// import
import {page} from './router.js';



/*
login form
*/
$(document).on('submit', '#loginForm', function(e){

  // disable form posting
  e.preventDefault();

  var pass = $('input[name="login"]').val();

  // send password for check
  $.post('/php/login.php', {login: pass}, function(res){

    if (res == 1) {
      page('/admin');
    } else {
      alert('wrong');
    }

  });

});


/*
remove page
*/
$(document).on('click', '.remove', function(e){

  var title = $(this).closest('div').find('.theTitle').html(),
      ask = confirm('smazat stránku "'+title+'"?');

  if (!ask) {

    e.preventDefault();
    return false;

  }

});



/*
upload video
*/
// on button click open upload dialog
$(document).on('click', '.videoUp', function(e){

  e.preventDefault();
  $(this).closest('.row').find('.uploadVideoForm input[type="file"]').click();

});
// change input => trigger form submit
$(document).on('change', '.uploadVideoForm input[type="file"]', function(e){

  $(this).closest('.uploadVideoForm').submit();

});
// form submit
$(document).on('submit', '.uploadVideoForm', function(e){

  // disable form posting
  e.preventDefault();

    var pId = $(this).attr('pId'),
        fileDiv = $(this).closest('div').find('.videoUrl');

    fileDiv.html('[<span class="wait"> <span>.</span> <span>.</span> <span>.</span> </span>]');

    $.ajax({
            url: '/php/uploadVideo.php?id='+pId,
            type: "POST",
            data: new FormData(this),
            contentType: false,
            cache: false,
            processData: false,
            success: function(res) {

             if (res.indexOf('error') != -1) {

                console.log(res);
                alert('chyba uploadu');

             } else {

               switch (res) {
                 case 'invalid':
                    alert('video musí být ve formátu .mp4');
                 break;
                 default:
                    if (res) {
                      fileDiv.attr('href', '/data/video/'+res).html(res);
                    } else {
                      alert('chyba v uložení souboru');
                    }
                 break;
               }

             }

            },
            error: function(e) {console.log(e); alert('error');}
    });

  // return f*cking false!
  return false;

});



/*
select page type = show content
*/
setTimeout(function(){
  if ($('#adminPageForm').length) {
    $('select[name="type"]').trigger('change');
  }
}, 500);
$(document).on('change', 'select[name="type"]', function(e){

  var select = $(this);
  switch (select.val()) {

    case 'textContent':
      $('#textContent').slideDown();
      $('#scriptToScreen').slideUp();
    break;

    case 'scriptToScreen':
      $('#textContent').slideUp();
      $('#scriptToScreen').slideDown();
    break;

  }

});



/*
saving content from form
*/
// saving function
function save(pId, active, type, link, title, title_en, content, content_en) {

  content = normJson(content);
  content_en = normJson(content_en);

  $.post('/php/saveForm.php', {pId, active, type, link, title, title_en, content, content_en}, function(res){

      console.log(res);

    if (res == 1) {
      page('/admin');
    } else {
      // console.log(res);
      alert('error');
    }

  });

}
// strigify json
function normJson(json) {
  var str = JSON.stringify(json);
  str = str.replace(/\\"/g, "'");
  return str;
}
// form submit listener
$(document).on('submit', '#adminPageForm', function(e){

    e.preventDefault();

    // inputs => vars
    var form = $(this),
        pId = form.attr('pId'),
        active = $('input[name="active"]').is(':checked')?1:0,
        type = $('select[name="type"]').val(),
        link = $('input[name="link"]').val(),
        title = $('input[name="title"]').val(),
        title_en = $('input[name="title_en"]').val();

    // must do it like this, the editor api gives me no other choice
    switch (type) {
      case 'textContent':

        editor_1.save().then((output_1) => {
          var content = output_1;
          editor_2.save().then((output_2) => {
            var content_en = output_2;
            save(pId, active, type, link, title, title_en, content, content_en);
          });
        });

      break;
      case 'scriptToScreen':

        editor_3.save().then((output_3) => {
          var content = output_3;
          editor_4.save().then((output_4) => {
            var content_en = output_4;
            save(pId, active, type, link, title, title_en, content, content_en);
          });
        });

      break;
    }

    return false;

});
