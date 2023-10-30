$(document).ready(function () {
    const selectedUsers = new Set();
    const $submitButton = $("#submitButton"); // Sélectionnez le bouton de soumission une fois
    checkDownloadButtonState(); // Appelez la fonction dès le début
  
    $("input[name='selected_users[]']").on("change", function () {
      const userId = $(this).val();
      if (this.checked) {
        selectedUsers.add(userId);
        const userName = $(this).parent().text(); // Obtenez le texte du label parent
        $("#selectedUsers").append(
          `<span class="selected-user" data-id="${userId}">${userName} <button class="remove-user" data-id="${userId}">x</button></span>`
        );
      } else {
        selectedUsers.delete(userId);
        $(`.selected-user[data-id="${userId}"]`).remove();
      }
  
      $("#selectedUsersInput").val(Array.from(selectedUsers).join(","));
      checkDownloadButtonState();
    });
  
    function checkDownloadButtonState() {
      if (selectedUsers.size === 0) {
        $submitButton.prop("disabled", true); // Désactiver le bouton si aucun utilisateur n'est sélectionné
      } else {
        $submitButton.prop("disabled", false); // Activer le bouton si des utilisateurs sont sélectionnés
      }
    }
  });
  