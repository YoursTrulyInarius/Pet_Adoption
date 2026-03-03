/* assets/js/admin_pet_modal.js */

document.addEventListener('DOMContentLoaded', function () {
    console.log('Admin Pet Modal Handler Loaded');

    // Handle clicks on pet names with the .pet-detail-trigger class
    document.body.addEventListener('click', function (e) {
        const trigger = e.target.closest('.pet-detail-trigger');
        if (trigger) {
            e.preventDefault();
            const petId = trigger.getAttribute('data-id');
            showPetDetails(petId);
        }
    });

    function showPetDetails(petId) {
        // Show loading state
        Swal.fire({
            title: 'Loading Pet Details...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Fetch details via AJAX
        fetch(`../ajax/get_pet_details.php?id=${petId}`)
            .then(res => res.text())
            .then(html => {
                if (html.includes('Pet not found')) {
                    Swal.fire('Error', 'Pet not found', 'error');
                } else {
                    Swal.fire({
                        title: '', // Name is inside the HTML
                        html: html,
                        width: '900px',
                        showConfirmButton: false,
                        showCloseButton: true,
                        customClass: {
                            container: 'pet-modal-container',
                            popup: 'pet-modal-popup'
                        }
                    });
                }
            })
            .catch(err => {
                console.error('Error fetching pet details:', err);
                Swal.fire('Error', 'Failed to load pet details', 'error');
            });
    }
});
