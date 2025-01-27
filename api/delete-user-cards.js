document.addEventListener('DOMContentLoaded', function() {
    
    const deleteCardForms = document.querySelectorAll('.delete-card-form');
    
    let currentForm = null;
    
    deleteCardForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            
            e.preventDefault();
            
            addConfirmationModal();
            
            const confirmationModal = document.querySelector('.confirmation-modal');
            
            confirmationModal.style.display = 'flex';
            currentForm = form;
                    
            const yesButton = document.querySelector('.confirmation-modal button.yes');
            const noButton = document.querySelector('.confirmation-modal button.no');
        
            yesButton.addEventListener('click', function(e) {
                e.preventDefault();
                if (currentForm) {
                    currentForm.submit();
                } else {
                    return;
                }
            });
            
            noButton.addEventListener('click', function(e) {
                e.preventDefault();
                const confirmationModal = document.querySelector('.confirmation-modal');
                confirmationModal.style.display = 'none';
            });

        });
    });
});


function addConfirmationModal() {
    
    if (document.querySelector('.confirmation-modal')) {
        console.log('El modal ya existe en el body.');
        return;
    }

    const modalHTML = `
        <div class="confirmation-modal" style="display:none;">
            <div class="confirmation-modal-container">
                <img src="${basePath}/assets/images/danger_icon_image.svg" alt="Danger icon" />
                <h2>¿Estás seguro de <span>eliminar tu tarjeta</span>?</h2>
                <div class="modal-ctas">
                    <button class="yes">Si, deseo eliminarla</button>
                    <button class="no">No</button>
                </div>
            </div>
        </div>
    `;

    const body = document.querySelector('body');
    body.insertAdjacentHTML('beforeend', modalHTML);
}
