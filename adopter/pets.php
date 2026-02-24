<?php
// adopter/pets.php
$hide_header = true;
$body_class = 'dashboard-page';
require_once __DIR__ . '/../includes/header.php';
Auth::requireRole('adopter');
$db = Database::getConnection();
?>

<div class="dashboard-wrapper">
    <aside class="dashboard-sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-paw"></i> <span>PAWSOME</span>
        </div>
        <ul class="sidebar-nav">
            <li><a href="dashboard.php"><i class="fas fa-home"></i> <span>Dashboard</span></a></li>
            <li class="active"><a href="pets.php"><i class="fas fa-search"></i> <span>Browse Pets</span></a></li>
            <li><a href="applications.php"><i class="fas fa-file-alt"></i> <span>My Applications</span></a></li>
            <li><a href="care.php"><i class="fas fa-book-medical"></i> <span>Care Guides</span></a></li>
            <li><a href="profile.php"><i class="fas fa-user"></i> <span>Profile</span></a></li>
        </ul>
        <div style="margin-top: auto; padding-top: 20px;">
            <a href="../logout.php" style="color: rgba(255,255,255,0.5); text-decoration: none; font-size: 0.9rem; display: flex; align-items: center; gap: 10px; padding: 10px 15px;">
                <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
            </a>
        </div>
    </aside>

    <main class="dashboard-main">
        <header class="dashboard-header">
            <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                <div>
                    <h2>Browse Adoptable Pets</h2>
                    <p>Find your new best friend from our partner shelters</p>
                </div>
                <div id="results-count" style="background: rgba(42, 157, 143, 0.1); color: var(--primary-color); padding: 8px 16px; border-radius: 20px; font-weight: 700; font-size: 0.9rem;">
                    Loading...
                </div>
            </div>
        </header>

        <div class="dashboard-content">
            <div style="display:flex; gap:30px; align-items: flex-start;" class="search-layout">
                <!-- Sidebar Filters -->
                <aside style="width: 250px; flex-shrink: 0;" class="filters-sidebar">
                    <div class="box" style="position: sticky; top: 20px; padding: 25px;">
                        <h3 style="margin-bottom: 20px; color: var(--complementary-color); font-weight: 800; font-size: 1.1rem; border-bottom: 2px solid #f1f2f6; padding-bottom: 10px;">Filters</h3>
                        
                        <form id="filter-form">
                            <div class="form-group" style="margin-bottom: 15px;">
                                <label style="font-size: 0.85rem; font-weight: 700; color: #8c98a4; text-transform: uppercase;">Species</label>
                                <select name="species" class="form-control filter-input" style="padding: 10px; font-size: 0.9rem; border-radius: 8px;">
                                    <option value="">All Animals</option>
                                    <option value="Dog">Dogs</option>
                                    <option value="Cat">Cats</option>
                                    <option value="Other">Others</option>
                                </select>
                            </div>

                            <div class="form-group" style="margin-bottom: 15px;">
                                <label style="font-size: 0.85rem; font-weight: 700; color: #8c98a4; text-transform: uppercase;">Size</label>
                                <select name="size" class="form-control filter-input" style="padding: 10px; font-size: 0.9rem; border-radius: 8px;">
                                    <option value="">All Sizes</option>
                                    <option value="Small">Small</option>
                                    <option value="Medium">Medium</option>
                                    <option value="Large">Large</option>
                                </select>
                            </div>

                            <div class="form-group" style="margin-bottom: 15px;">
                                <label style="font-size: 0.85rem; font-weight: 700; color: #8c98a4; text-transform: uppercase;">Gender</label>
                                <select name="gender" class="form-control filter-input" style="padding: 10px; font-size: 0.9rem; border-radius: 8px;">
                                    <option value="">Any Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>

                            <div class="form-group" style="margin-bottom: 0;">
                                <label style="font-size: 0.85rem; font-weight: 700; color: #8c98a4; text-transform: uppercase;">Name</label>
                                <input type="text" name="keyword" class="form-control filter-input" placeholder="e.g. Buddy" style="padding: 10px; font-size: 0.9rem; border-radius: 8px;">
                            </div>
                        </form>
                    </div>
                </aside>

                <!-- Main Content -->
                <div style="flex-grow: 1;">
                    <div id="pets-grid" class="card-grid" style="padding:0; gap: 20px;">
                        <!-- Pet cards injected here via AJAX -->
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Modal Container -->
<div id="pet-modal-overlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:9999; backdrop-filter: blur(4px);">
    <div id="pet-modal" style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); width:90%; max-width:900px; max-height:90vh; background:white; border-radius:12px; box-shadow:0 10px 40px rgba(0,0,0,0.2); overflow:hidden; display:flex; flex-direction:column;">
        <button onclick="closePetModal()" style="position:absolute; top:15px; right:15px; background:white; border:none; width:36px; height:36px; border-radius:50%; cursor:pointer; font-size:1.2rem; color:#636e72; z-index:10; box-shadow:0 2px 5px rgba(0,0,0,0.1);"><i class="fas fa-times"></i></button>
        <div id="pet-modal-content" style="padding:40px;">
            <div style="text-align:center; padding:50px;">
                <i class="fas fa-spinner fa-spin" style="font-size:2rem; color:var(--primary-color);"></i>
            </div>
        </div>
    </div>
</div>

<script>
function closePetModal() {
    document.getElementById('pet-modal-overlay').style.display = 'none';
}

function openPetModal(petId) {
    const overlay = document.getElementById('pet-modal-overlay');
    const content = document.getElementById('pet-modal-content');
    
    overlay.style.display = 'block';
    
    // Show spinner
    content.innerHTML = '<div style="text-align:center; padding:50px;"><i class="fas fa-spinner fa-spin" style="font-size:2rem; color:var(--primary-color);"></i></div>';
    
    fetch(`../ajax/get_pet_details.php?id=${petId}`)
        .then(res => res.text())
        .then(html => {
            content.innerHTML = html;
        })
        .catch(err => {
            content.innerHTML = '<p style="text-align:center; color:red;">Error loading details.</p>';
            console.error(err);
        });
}

document.getElementById('pet-modal-overlay').addEventListener('click', function(e) {
    if (e.target === this) closePetModal();
});

document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.getElementById('filter-form');
    const petsGrid = document.getElementById('pets-grid');
    const countLabel = document.getElementById('results-count');

    function fetchPets() {
        const formData = new FormData(filterForm);
        const params = new URLSearchParams(formData).toString();
        
        petsGrid.style.opacity = '0.5';
        
        fetch('../ajax/search_pets.php?' + params)
            .then(res => res.json())
            .then(data => {
                petsGrid.innerHTML = data.html;
                countLabel.innerText = `${data.count} pets found`;
                petsGrid.style.opacity = '1';
                
                // Rewrite URLs and intercept clicks for modal
                const cards = petsGrid.querySelectorAll('a.btn-primary');
                cards.forEach(card => {
                    const href = card.getAttribute('href');
                    if (href.startsWith('pet_detail.php?id=') || href.startsWith('../pet_detail.php?id=')) {
                        // Extract ID
                        const match = href.match(/id=(\d+)/);
                        if (match) {
                            card.setAttribute('href', '#');
                            card.addEventListener('click', function(e) {
                                e.preventDefault();
                                openPetModal(match[1]);
                            });
                        }
                    }
                });
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
    .filters-sidebar .box {
        position: static !important;
    }
}
</style>

<?php include __DIR__ . '/../includes/footer.php'; ?>
