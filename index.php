<?php
// index.php
include 'includes/header.php';
$db = Database::getConnection();

// Fetch featured pets (random 3 - including adopted ones to show success stories)
$stmt = $db->query("SELECT p.*, s.shelter_name FROM pets p JOIN shelters s ON p.shelter_id = s.id ORDER BY RAND() LIMIT 3");
$featured_pets = $stmt->fetchAll();
?>

<section class="hero">
    <div class="container">
        <h1>Find Your New Best Friend</h1>
        <p>Connecting shelter animals with loving families. Focused on responsible pet ownership and adoption.</p>
        <div class="hero-btns" style="display:flex; justify-content:center; gap:16px;">
            <a href="pets.php" class="btn btn-primary">Browse Pets</a>
            <a href="register.php" class="btn btn-secondary">Join the Community</a>
        </div>
    </div>
</section>

<section class="features section-padding" style="background-color: var(--white); border-bottom: 1px solid rgba(0,0,0,0.02);">
    <div class="container">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 48px; text-align: center;">
            <div class="feature-box">
                <div style="width:72px; height:72px; background: rgba(42, 157, 143, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px;">
                    <i class="fas fa-search-location" style="font-size: 1.5rem; color: var(--primary-color);"></i>
                </div>
                <h3 style="margin-bottom:12px; color: var(--complementary-color);">Search Easily</h3>
                <p style="color: #636e72;">Filter by species, breed, and size to find the perfect match for your home.</p>
            </div>
            <div class="feature-box">
                <div style="width:72px; height:72px; background: rgba(233, 196, 106, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px;">
                    <i class="fas fa-file-signature" style="font-size: 1.5rem; color: var(--secondary-color);"></i>
                </div>
                <h3 style="margin-bottom:12px; color: var(--complementary-color);">Quick Application</h3>
                <p style="color: #636e72;">Submit your application online and track its status in real-time from your dashboard.</p>
            </div>
            <div class="feature-box">
                <div style="width:72px; height:72px; background: rgba(38, 70, 83, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px;">
                    <i class="fas fa-heartbeat" style="font-size: 1.5rem; color: var(--complementary-color);"></i>
                </div>
                <h3 style="margin-bottom:12px; color: var(--complementary-color);">Expert Care</h3>
                <p style="color: #636e72;">Access professional care guides to help your new friend settle in comfortably.</p>
            </div>
        </div>
    </div>
</section>

<section class="featured-pets section-padding">
    <div class="container">
        <div style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 48px;">
            <h2 style="color: var(--complementary-color); font-size: 2rem; font-weight: 800; letter-spacing:-0.5px;">Featured Pets</h2>
            <a href="pets.php" style="color: var(--primary-color); font-weight: 700; display:flex; align-items:center; gap:8px;">
                View All <i class="fas fa-arrow-right"></i>
            </a>
        </div>
        
        <div class="card-grid">
            <?php if ($featured_pets): ?>
                <?php foreach ($featured_pets as $pet): ?>
                    <div class="card" style="position: relative; overflow: hidden; <?php echo $pet['is_adopted'] ? 'opacity: 0.85;' : ''; ?>">
                        <img src="<?php echo getPetPrimaryImage($db, $pet['id']); ?>" alt="<?php echo $pet['name']; ?>">
                        
                        <?php if ($pet['is_adopted']): ?>
                            <div style="position: absolute; top: 20px; right: -35px; background: #e74c3c; color: white; font-weight: 800; text-transform: uppercase; padding: 8px 40px; transform: rotate(45deg); box-shadow: 0 4px 10px rgba(0,0,0,0.2); font-size: 0.85rem; letter-spacing: 1px; z-index: 2;">
                                Adopted
                            </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <h3 class="card-title"><?php echo $pet['name']; ?></h3>
                            <p style="color:#636e72; margin-bottom: 16px; font-size: 0.9rem;"><i class="fas fa-map-marker-alt" style="color:var(--primary-color)"></i> <?php echo $pet['shelter_name']; ?></p>
                            <div style="display:flex; gap:8px; margin-bottom: 24px;">
                                <span style="background: var(--bg-color); color: var(--complementary-color); padding: 6px 14px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;"><?php echo $pet['species']; ?></span>
                                <span style="background: var(--bg-color); color: var(--complementary-color); padding: 6px 14px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;"><?php echo $pet['size']; ?></span>
                            </div>
                            <?php if ($pet['is_adopted']): ?>
                                <button class="btn btn-secondary" style="width:100%; cursor: not-allowed; opacity: 0.7;" disabled>Already Adopted</button>
                            <?php else: ?>
                                <a href="pet_detail.php?id=<?php echo $pet['id']; ?>" class="btn btn-primary" style="width:100%;">Learn More</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="box" style="grid-column: 1/-1; padding: 60px; text-align: center;">
                    <p style="color:#636e72;">No pets available at the moment. Please check back later!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
