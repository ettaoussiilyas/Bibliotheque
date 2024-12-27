

function showBookDetails(book) {
    
    document.getElementById("modalTitle").textContent = book.title;
    document.getElementById("modalAuthor").textContent = book.author;
    document.getElementById("modalCategory").textContent = book.category_name;
    document.getElementById("modalSummary").textContent = book.summary;
  
    
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
  
    
    document.getElementById("modalImage").innerHTML = `
          <img src="${
            book.cover_image ||
            "https://via.placeholder.com/300x400?text=Image+non+disponible"
          }" 
               alt="${book.title}" 
               class="w-full rounded-lg shadow-lg" 
               onerror="this.src='https://via.placeholder.com/300x400?text=Image+non+disponible'">
      `;
  
   
    document.getElementById("bookModal").classList.remove("hidden");
  }
  
  function closeModal() {
    document.getElementById("bookModal").classList.add("hidden");
  }
  
  
  document.getElementById("bookModal").addEventListener("click", function (e) {
    if (e.target === this) {
      closeModal();
    }
  });


//search part ajax handling
  $(document).ready(function(){
      let searchTimeout;
      
      $("#search").on("keyup", function(){
          clearTimeout(searchTimeout);
          
          const query = $(this).val();
          
          searchTimeout = setTimeout(function() {
              if (query.length >= 2) {
                  $.ajax({
                      url: window.location.href,
                      method: 'POST',
                      data: {query: query},
                      headers: {
                          'X-Requested-With': 'XMLHttpRequest'
                      },
                      success: function(data) {
                          $("#result").html(data);
                      },
                      error: function() {
                          $("#result").html("An error occurred while searching.");
                      }
                  });
              } else {
                  $("#result").html("");
              }
          }, 300);
      });
  });

  