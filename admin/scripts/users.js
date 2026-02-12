function get_users() {
  let xhr = new XMLHttpRequest();

  xhr.open("POST", "ajax/users.php", true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

  xhr.onload = function () {
    document.getElementById('users-data').innerHTML = this.responseText;
  };

  xhr.send('get_users');
}

// function toggle_status(id, val) {
//   let xhr = new XMLHttpRequest();

//   xhr.open("POST", "ajax/rooms.php", true);
//   xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

//   xhr.onload = function () {
//     if (this.responseText == 1) {
//       alert('success', 'Status Toggled!');
//       get_all_rooms();
//     } else {
//       alert('fail', 'Server Down!');
//     }
//   };

//   xhr.send('toggle_status=' + id + '&value=' + val);
// }

// function remove_room(room_id) {
//   if (confirm("Are you sure you want to delete this room?")) {
//     let data = new FormData();

//     data.append('room_id', room_id);
//     data.append('remove_room', '');

//     let xhr = new XMLHttpRequest();
//     xhr.open("POST", "ajax/rooms.php", true);

//     xhr.onload = function () {
//       if (this.responseText == 1) {
//         alert('success', 'Room removal success!');
//         get_all_rooms();
//       } else {
//         alert('error', 'Room removal failed!');
//       }
//     };

//     xhr.send(data);
//   }
// }

window.onload = function () {
  get_users();
}