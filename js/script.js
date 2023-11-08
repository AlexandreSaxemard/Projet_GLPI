$(document).ready(function () {

  // On récupère les données du formulaire
  const selectedUsers = new Set();
  const $submitButton = $("#submitButton");
  const $mainForm = $("#mainForm");

  // Appel initial pour griser le bouton si nécessaire
  checkDownloadButtonState();

  // On initialise le plugin Select2
  $("#userSelect").select2();

  // On écoute les événements de changement sur le select
  $("#userSelect").on("change", function () {

    // On récupère les valeurs sélectionnées
    const selectedOptions = $(this).val() || [];

    // On vide le Set et on le remplit avec les nouvelles valeurs
    selectedUsers.clear();

    selectedOptions.forEach(function (userId) {
      selectedUsers.add(userId);
    });

    // On met à jour le champ caché
    $("#selectedUsersInput").val(Array.from(selectedUsers).join(","));
    checkDownloadButtonState();
  });

  // On écoute les événements de clic sur le bouton "Sélectionner tout"
  $("#selectAllButton").click(function () {

    // On récupère le texte du bouton
    if ($(this).text() === "Sélectionner tout") {

      // On sélectionne toutes les options
      $("#userSelect > option").prop("selected", true);

      // On déclenche l'événement de changement
      $("#userSelect").trigger("change");

      // On change le texte du bouton
      $(this).text("Désélectionner tous");
    } else {

      // On déselectionne toutes les options
      $("#userSelect > option").prop("selected", false);

      // On déclenche l'événement de changement
      $("#userSelect").trigger("change");

      // On change le texte du bouton
      $(this).text("Sélectionner tout");
    }
  });

  // On écoute les événements de clic sur le bouton "Déselectionner tous"
  function checkDownloadButtonState() {

    // On récupère le texte du bouton
    if (selectedUsers.size === 0) {

      // On désactive le bouton
      $submitButton.prop("disabled", true);
    } else {

      // On active le bouton
      $submitButton.prop("disabled", false);
    }
  }

  // Écoutez les événements de changement sur les boutons radio
  $("#excel").change(function () {

    // Si le bouton radio "Excel" est coché, changez l'action du formulaire
    if ($("#excel").is(":checked")) {
      $mainForm.attr("action", "process_excel.php");
    }
  });

  // Écoutez les événements de changement sur les boutons radio
  $("#text").change(function () {

    // Si le bouton radio "Texte" est coché, changez l'action du formulaire
    if ($("#text").is(":checked")) {
      $mainForm.attr("action", "process_txt.php");
    }
  });
});
