function get_reviews() 
{
  let xhr = new XMLHttpRequest();
  xhr.open("POST", "ajax/reviews.php", true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

  xhr.onload = function () {
    document.getElementById('reviews-data').innerHTML = this.responseText;
  };

  xhr.send('get_reviews');
}

function approve_review(id) {
  if (confirm("Approve this review?")) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/reviews.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function () {
      if (this.responseText == 1) {
        alert('success', 'Review Approved!');
        get_reviews();
      } else {
        alert('error', 'Operation failed!');
      }
    }

    xhr.send('approve_review=' + id);
  }
}

function reject_review(id) {
  if (confirm("Reject this review?")) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/reviews.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function () {
      if (this.responseText == 1) {
        alert('success', 'Review Rejected!');
        get_reviews();
      } else {
        alert('error', 'Operation failed!');
      }
    }

    xhr.send('reject_review=' + id);
  }
}

function delete_review(id) {
  if (confirm("Are you sure you want to delete this review?")) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "ajax/reviews.php", true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function () {
      if (this.responseText == 1) {
        alert('success', 'Review Deleted!');
        get_reviews();
      } else {
        alert('error', 'Deletion failed!');
      }
    }

    xhr.send('delete_review=' + id);
  }
}

window.onload = function () {
  get_reviews();
}
