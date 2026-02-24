<?php
// pets.php
include 'includes/header.php';
$db = Database::getConnection();
?>

<div class="container py-4">
    <div style="display:flex; gap:40px;" class="search-layout">
        <!-- Sidebar Filters -->
        <aside style="width: 280px; flex-shrink: 0;" class="filters-sidebar">
            <div class="card" style="padding: 32px; position: sticky; top: 100px;">
                <h3 style="margin-bottom: 24px; color: var(--complementary-color); font-weight: 800; font-size: 1.25rem;">Filters</h3>
                
                <form id="filter-form">
                    <div class="form-group">
                        <label>Species</label>
                        <select name="species" class="form-control filter-input">
                            <option value="">All Animals</option>
                            <option value="Dog">Dogs</option>
                            <option value="Cat">Cats</option>
                            <option value="Other">Others</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Size</label>
                        <select name="size" class="form-control filter-input">
                            <option value="">All Sizes</option>
                            <option value="Small">Small</option>
                            <option value="Medium">Medium</option>
                            <option value="Large">Large</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Gender</label>
                        <select name="gender" class="form-control filter-input">
                            <option value="">Any Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom:0;">
                        <label>Search Name</label>
                        <input type="text" name="keyword" class="form-control filter-input" placeholder="Buddy...">
                    </div>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div style="flex-grow: 1;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 32px; padding-bottom: 20px; border-bottom: 1px solid var(--light-grey);">
                <h2 style="color: var(--complementary-color); font-weight: 800; font-size: 1.75rem;">Adoptable Pets</h2>
                <div id="results-count" style="color: #636e72; font-weight: 600; font-size: 0.9rem;">Loading...</div>
            </div>

            <div id="pets-grid" class="card-grid" style="padding:0;">
                <!-- Pet cards injected here via AJAX -->
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filter-form');
    const petsGrid = document.getElementById('pets-grid');
    const countLabel = document.getElementById('results-count');

    function fetchPets() {
        const formData = new FormData(filterForm);
        const params = new URLSearchParams(formData).toString();
        
        petsGrid.style.opacity = '0.5';
        
        fetch('ajax/search_pets.php?' + params)
            .then(res => res.json())
            .then(data => {
                petsGrid.innerHTML = data.html;
                countLabel.innerText = `${data.count} pets found`;
                petsGrid.style.opacity = '1';
                // Trigger animations if any
            })
            .catch(err => console.error(err));
    }

    // Event listeners
    document.querySelectorAll('.filter-input').forEach(input => {
        input.addEventListener('change', fetchPets);
        input.addEventListener('keyup', (e) => {
            if (e.key === 'Enter') fetchPets();
            else if (input.type === 'text') fetchPets(); // Live search
        });
    });

    fetchPets();
});
</script>

<style>
@media (max-width: 992px) {
    .search-layout {
        flex-direction: column !important;
    }
    .filters-sidebar {
        width: 100% !important;
    }
    .filters-sidebar .card {
        position: static !important;
    }
}
</style>

<?php include 'includes/footer.php'; ?>
