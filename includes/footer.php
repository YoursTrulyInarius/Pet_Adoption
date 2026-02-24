<?php
// includes/footer.php
?>
    <?php if (!isset($hide_header) || !$hide_header): ?>
    </main>
    <?php endif; ?>
    <footer style="padding: 30px 0; background: var(--white); border-top: 1px solid var(--light-grey); margin-top: auto;">
        <div class="container" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
            <div style="display: flex; align-items: center; gap: 10px; color: var(--complementary-color); font-weight: 700;">
                <i class="fas fa-paw" style="color: var(--primary-color);"></i> Pawsome Connections
            </div>
            
            <div style="display: flex; gap: 30px; font-size: 0.85rem; font-weight: 600;">
                <a href="<?php echo SITE_URL; ?>/pets.php" style="color: #8c98a4; text-decoration: none;">Browse Pets</a>
                <a href="<?php echo SITE_URL; ?>/care" style="color: #8c98a4; text-decoration: none;">Care Guides</a>
                <a href="#" style="color: #8c98a4; text-decoration: none;">Privacy Policy</a>
            </div>

            <div style="display: flex; gap: 15px;">
                <a href="#" style="color: #8c98a4;"><i class="fab fa-facebook-f"></i></a>
                <a href="#" style="color: #8c98a4;"><i class="fab fa-instagram"></i></a>
                <a href="#" style="color: #8c98a4;"><i class="fab fa-twitter"></i></a>
            </div>

            <div style="width: 100%; text-align: center; margin-top: 10px; border-top: 1px solid #f1f2f6; pt: 15px;">
                <p style="color: #b2bec3; font-size: 0.75rem;">&copy; <?php echo date('Y'); ?> Pawsome Connections. Standardizing pet adoption.</p>
            </div>
        </div>
    </footer>
    <script src="<?php echo SITE_URL; ?>/assets/js/main.js?v=<?php echo time(); ?>"></script>
</body>
</html>
