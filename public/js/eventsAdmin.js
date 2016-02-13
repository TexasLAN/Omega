function assertTwoDigit(num) {
  var result = '' + num;
  while(result.length < 2) {
    result = '0' + result;
  }
  return result;
}

$('#eventMutator').on('show.bs.modal', function(event) {
  // Get the data from the button
  var button = $(event.relatedTarget);
  var method = button.data('method');
  var type = button.data('type');
  var id = button.data('id');
  var name = button.data('name');
  var location = button.data('location');
  var startDateStr = button.data('startdate');
  var endDateStr = button.data('enddate');
  var startDate = new Date(startDateStr);
  if(isNaN(startDate.getTime())) startDate = new Date();
  var endDate = new Date(endDateStr);
  if(isNaN(endDate.getTime())) endDate = new Date();
  var description = button.data('description');

  var modal = $(this);

  // Set the title
  modal.find('#eventName').text((method === 'create') ? 'Creating an event' : 'Updating an event');

  modal.find('#method').val(method);
  modal.find('#type').val(type);
  modal.find('#id').val(id);

  // Set form attributes to button values
  modal.find('#name').val(name);
  modal.find('#location').val(location);
  modal.find('#start_date').val(startDate.toISOString().slice(0,10));
  modal.find('#start_time').val(assertTwoDigit(startDate.getUTCHours()) + ":" + assertTwoDigit(startDate.getMinutes()) + ":" + assertTwoDigit(startDate.getSeconds()));
  modal.find('#end_date').val(endDate.toISOString().slice(0,10));
  modal.find('#end_time').val(assertTwoDigit(endDate.getUTCHours()) + ":" + assertTwoDigit(endDate.getMinutes()) + ":" + assertTwoDigit(endDate.getSeconds()));
  modal.find('#description').val(description);
});

$('#submit').click(function() {
  $('#eventMutator').find('form').submit();
});

$('head').append('<link rel="stylesheet" type="text/css" href="/css/bootstrap-sortable.css" />');
