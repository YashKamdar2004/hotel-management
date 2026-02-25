function get_bookings() 
{
  let xhr = new XMLHttpRequest();

  xhr.open("POST", "ajax/bookings.php", true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

  xhr.onload = function () {
    document.getElementById('bookings-data').innerHTML = this.responseText;
  };

  xhr.send('get_bookings');
}

function confirm_booking(id) {
  if (confirm("Approve this booking?")) {
    let xhr = new XMLHttpRequest();

    xhr.open("POST", "ajax/bookings.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function () {
      if (this.responseText == 1) {
        alert('success', 'Booking Approved!');
        get_bookings();
      } else {
        alert('error', 'Operation failed!');
      }
    }

    xhr.send('confirm_booking=' + id);
  }
}

function cancel_booking(id) {
  if (confirm("Reject this booking?")) {
    let xhr = new XMLHttpRequest();

    xhr.open("POST", "ajax/bookings.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function () {
      if (this.responseText == 1) {
        alert('success', 'Booking Rejected!');
        get_bookings();
      } else {
        alert('error', 'Operation failed!');
      }
    }

    xhr.send('cancel_booking=' + id);
  }
}

function complete_booking(id) {
  if (confirm("Mark this booking as completed?")) {
    let xhr = new XMLHttpRequest();

    xhr.open("POST", "ajax/bookings.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function () {
      if (this.responseText == 1) {
        alert('success', 'Booking Completed!');
        get_bookings();
      } else {
        alert('error', 'Operation failed!');
      }
    }

    xhr.send('complete_booking=' + id);
  }
}

function accept_refund(id) {
  if (confirm("Accept this refund request? Amount will be refunded to customer.")) {
    let xhr = new XMLHttpRequest();

    xhr.open("POST", "ajax/process_refund.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function () {
      if (this.responseText == 1) {
        alert('success', 'Refund Accepted!');
        get_bookings();
      } else {
        alert('error', 'Operation failed!');
      }
    }

    xhr.send('accept_refund=' + id);
  }
}

function reject_refund(id) {
  if (confirm("Reject this refund request?")) {
    let xhr = new XMLHttpRequest();

    xhr.open("POST", "ajax/process_refund.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function () {
      if (this.responseText == 1) {
        alert('success', 'Refund Request Rejected!');
        get_bookings();
      } else {
        alert('error', 'Operation failed!');
      }
    }

    xhr.send('reject_refund=' + id);
  }
}

function search_booking(username)
{
  let xhr = new XMLHttpRequest();

  xhr.open("POST", "ajax/bookings.php", true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

  xhr.onload = function () {
    document.getElementById('bookings-data').innerHTML = this.responseText;
  }

  xhr.send('search_booking&name='+username);
}

window.onload = function () {
  get_bookings();
}
