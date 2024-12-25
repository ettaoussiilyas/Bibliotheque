

function showBookDetails(book) {
    // Remplir le modal avec les données
    document.getElementById("modalTitle").textContent = book.title;
    document.getElementById("modalAuthor").textContent = book.author;
    document.getElementById("modalCategory").textContent = book.category_name;
    document.getElementById("modalSummary").textContent = book.summary;
  
    // Gérer le statut
    var statusText, statusColor;
    switch (book.status) {
      case "available":
        statusText = "Disponible";
        statusColor = "text-green-500";
        break;
      case "borrowed":
        statusText = "Emprunté";
        statusColor = "text-red-500";
        break;
      default:
        statusText = "Réservé";
        statusColor = "text-yellow-500";
    }
  
    var statusElement = document.getElementById("modalStatus");
    statusElement.className = statusColor;
    statusElement.textContent = statusText;
  
    // Gérer l'image
    document.getElementById("modalImage").innerHTML = `
          <img src="${
            book.cover_image ||
            "https://via.placeholder.com/300x400?text=Image+non+disponible"
          }" 
               alt="${book.title}" 
               class="w-full rounded-lg shadow-lg" 
               onerror="this.src='https://via.placeholder.com/300x400?text=Image+non+disponible'">
      `;
  
    // Afficher le modal
    document.getElementById("bookModal").classList.remove("hidden");
  }
  
  function closeModal() {
    document.getElementById("bookModal").classList.add("hidden");
  }
  
  // Fermer le modal si on clique en dehors
  document.getElementById("bookModal").addEventListener("click", function (e) {
    if (e.target === this) {
      closeModal();
    }
  });
  