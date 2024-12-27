function showModal(bookId) {
    document.getElementById('modal_' + bookId).classList.remove('hidden');
}

function closeModal(bookId) {
    document.getElementById('modal_' + bookId).classList.add('hidden');
}

// Fermer le modal si on clique en dehors
document.querySelectorAll('[id^="modal_"]').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal(this.id.replace('modal_', ''));
        }
    });
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


