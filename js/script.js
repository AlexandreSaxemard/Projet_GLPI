$(document).ready(function () {
  // Créer un ensemble pour stocker les utilisateurs sélectionnés
  const selectedUsers = new Set();

  // Sélectionner le bouton de soumission
  const $submitButton = $("#submitButton");

  // Initialiser la liste déroulante comme une liste Select2
  $("#userSelect").select2();

  // Gérer les événements de changement dans la liste déroulante
  $("#userSelect").on("change", function () {
    // Récupérer les options sélectionnées ou un tableau vide
    const selectedOptions = $(this).val() || [];

    // Vider l'ensemble des utilisateurs sélectionnés
    selectedUsers.clear();

    // Ajouter les ID des utilisateurs sélectionnés à l'ensemble
    selectedOptions.forEach(function (userId) {
      selectedUsers.add(userId);
    });

    // Mettre à jour la valeur d'un champ d'entrée avec les utilisateurs sélectionnés
    $("#selectedUsersInput").val(Array.from(selectedUsers).join(","));

    // Vérifier l'état du bouton de téléchargement
    checkDownloadButtonState();
  });

  // Bouton pour sélectionner tous les utilisateurs
  $("#selectAllButton").click(function () {
    if ($(this).text() === "Sélectionner tous") {
      $("#userSelect > option").prop("selected", true);
      $("#userSelect").trigger("change");
      $(this).text("Désélectionner tous");
    } else {
      $("#userSelect > option").prop("selected", false);
      $("#userSelect").trigger("change");
      $(this).text("Sélectionner tous");
    }
  });

  // Fonction pour vérifier l'état du bouton de téléchargement
  function checkDownloadButtonState() {
    // Si aucun utilisateur n'est sélectionné, désactiver le bouton
    if (selectedUsers.size === 0) {
      $submitButton.prop("disabled", true);
    } else {
      // Sinon, activer le bouton
      $submitButton.prop("disabled", false);
    }
  }
});
