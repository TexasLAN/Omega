$('#editRoles').on('show.bs.modal', function(event) {
  // Get the data from the button
  var button = $(event.relatedTarget);
  var roles = button.data('roles');
  var name = button.data('name');
  var id = button.data('id');

  var modal = $(this)

  // Set the title
  modal.find('.modal-title').text(name);

  modal.find(':hidden').val(id);

  // Reset all checked boxes
  modal.find(':checkbox').each(function(checkbox) {
    $(this).prop("checked", false);
  });

  // Set all boxes to checked based off the user's roles
  roles.forEach(function(role) {
    modal.find('#' + role).prop("checked", true);
  });
});

$('#submit').click(function() {
  $('#editRoles').find('form').submit();
});

$('#deleteConfirm').on('show.bs.modal', function(event) {
  // Get the data from the button
  var button = $(event.relatedTarget);
  var delete_type = button.data('type');
  var id = button.data('id');

  var modal = $(this)

  // Set the info
  modal.find('#delete_id').val(id);
  modal.find('#delete_type').val(delete_type);
});

$('#delete-submit').click(function() {
  $('#deleteConfirm').find('form').submit();
});

$('head').append('<link rel="stylesheet" type="text/css" href="/css/bootstrap-sortable.css" />');


new Clipboard('#alum .btn-clipboard');
new Clipboard('#members .btn-clipboard');
new Clipboard('#inactive .btn-clipboard');
new Clipboard('#pledges .btn-clipboard');
new Clipboard('#applicants .btn-clipboard');