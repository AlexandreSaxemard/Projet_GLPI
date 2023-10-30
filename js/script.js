$(document).ready(function () {
  const selectedUsers = new Set();
  const $submitButton = $("#submitButton");

  // Initialisez la liste déroulante comme une liste Select2
  $("#userSelect").select2();

  // Gérez les événements de changement
  $("#userSelect").on("change", function () {
    const selectedOptions = $(this).val() || [];
    selectedUsers.clear();

    selectedOptions.forEach(function (userId) {
      selectedUsers.add(userId);
    });

    $("#selectedUsersInput").val(Array.from(selectedUsers).join(","));
    checkDownloadButtonState();
  });

  function checkDownloadButtonState() {
    if (selectedUsers.size === 0) {
      $submitButton.prop("disabled", true);
    } else {
      $submitButton.prop("disabled", false);
    }
  }
});
