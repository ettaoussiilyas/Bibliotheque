function showModal(bookId) {
    document.getElementById('modal_' + bookId).classList.remove('hidden');
}

function closeModal(bookId) {
    document.getElementById('modal_' + bookId).classList.add('hidden');
}

function showBookDetails(book) {
    
    document.getElementById('modalTitle').textContent = book.title;
    document.getElementById('modalAuthor').textContent = book.author;
    document.getElementById('modalCategory').textContent = book.category_name;
    
   
    const statusElement = document.getElementById('modalStatus');
    let statusText, statusClass;
    switch (book.status) {
        case 'available':
            statusText = 'Disponible';
            statusClass = 'text-green-500';
            break;
        case 'borrowed':
            statusText = 'Emprunté';
            statusClass = 'text-red-500';
            break;
        default:
            statusText = 'Réservé';
            statusClass = 'text-yellow-500';
    }
    statusElement.textContent = statusText;
    statusElement.className = statusClass;

    document.getElementById('modalSummary').textContent = book.summary || 'Aucun résumé disponible';
    
    document.getElementById('modalImage').innerHTML = `
        <img src="${book.cover_image || 'https://via.placeholder.com/300x400?text=Image+non+disponible'}" 
             alt="${book.title}" 
             class="w-full rounded-lg shadow-lg"
             onerror="this.src='https://via.placeholder.com/300x400?text=Image+non+disponible'">
    `;

    // Mettre à jour le lien d'emprunt avec l'ID du livre
    const borrowButton = document.getElementById('borrowButton');
    if (borrowButton) {
        borrowButton.href = 'reservation.php?book_id=' + book.id;
    }

    document.getElementById('bookModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('bookModal').classList.add('hidden');
}

document.getElementById('bookModal').addEventListener('click', function(e) {
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


