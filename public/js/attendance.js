$('head').append('<link rel="stylesheet" type="text/css" href="/css/bootstrap-sortable.css" />');

$(function(){
  var userList = JSON.parse($('#user_list').val());
  
  // setup autocomplete function pulling from currencies[] array
  $('#autocomplete').autocomplete({
    lookup: userList,
    onSelect: function (suggestion) {
      $('#user_id').val(suggestion.data);
    }
  });
});
