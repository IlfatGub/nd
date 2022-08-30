/**
 * Created by 01gig on 17.08.2017.
 */
// $( function() {
//     $( "#sortable" ).sortable({
//         placeholder: "ui-state-highlight"
//     });
//     $( "#sortable" ).disableSelection();
//
//
//     $('ul').sortable({
//         axis: 'y',
//         stop: function(event,ui) {
//             var data = $(this).sortable("toArray");
//             $('#spans').text(data);
//             $.ajax({
//                 type: "GET",
//                 url: "/adm/podrsort",
//                 data: "a=" + data,
//                 timeout:5000,
//                 cache: false,
//                 dataType: "html",
//                 success: function(data) {
//                     var elem = $("<div id='qwe' class='alert alert-info fade in myRecal' style='display: none'></div>").text("выполнено");
//                     $("#sortable").prepend(elem);
//                     $("#qwe").hide(500);
//                     setTimeout(function () {
//                         $("#qwe").fadeOut(300)
//                     }, 1000);
//                 },
//                 error: function(){
//                     $("#sortable").append('p>Error!</p>');
//                 }
//             });
//         }
//     });
//
// } );