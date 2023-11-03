$(document).ready(function () {
  const selectedUsers = new Set();
  const $submitButton = $("#submitButton");
  const $mainForm = $("#mainForm");

  // Appel initial pour griser le bouton si nécessaire
  checkDownloadButtonState();

  $("#userSelect").select2();

  $("#userSelect").on("change", function () {
    const selectedOptions = $(this).val() || [];
    selectedUsers.clear();
    selectedOptions.forEach(function (userId) {
      selectedUsers.add(userId);
    });
    $("#selectedUsersInput").val(Array.from(selectedUsers).join(","));
    checkDownloadButtonState();
  });

  $("#selectAllButton").click(function () {
    if ($(this).text() === "Sélectionner tous") {
      $("#userSelect > option").prop("selected", true);
      $("#userSelect").trigger("change");
      $(this).text("Désélectionner tout");
    } else {
      $("#userSelect > option").prop("selected", false);
      $("#userSelect").trigger("change");
      $(this).text("Sélectionner tout");
    }
  });

  function checkDownloadButtonState() {
    if (selectedUsers.size === 0) {
      $submitButton.prop("disabled", true);
    } else {
      $submitButton.prop("disabled", false);
    }
  }

  // Écoutez les événements de changement sur les boutons radio
  $("#excel").change(function () {
    if ($("#excel").is(":checked")) {
      $mainForm.attr("action", "process_excel.php");
    }
  });

  $("#text").change(function () {
    if ($("#text").is(":checked")) {
      $mainForm.attr("action", "process_txt.php");
    }
  });
});
