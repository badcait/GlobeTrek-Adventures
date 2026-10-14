    <footer class="site-footer">
        <div class="container footer-grid">
            <div>
                <a class="brand footer-brand" href="<?= $base ?? '' ?>index.php">
                    <img class="footer-logo" src="<?= $base ?? '' ?>assets/images/globe-trek-logo.png" alt="Globe Trek Adventures logo">
                </a>
                <p>Negombo-based travel planning for curated global escapes.</p>
            </div>
            <div>
                <h3>Explore</h3>
                <a href="<?= $base ?? '' ?>packages.php">Tour packages</a>
                <a href="<?= $base ?? '' ?>accommodations.php">Accommodations</a>
                <a href="<?= $base ?? '' ?>planner.php">Custom planner</a>
            </div>
            <div>
                <h3>Support</h3>
                <a href="<?= $base ?? '' ?>contact.php">Submit query</a>
                <a href="<?= $base ?? '' ?>login.php">Customer login</a>
                <a href="<?= $base ?? '' ?>docs/test-plan.md">Testing notes</a>
            </div>
            <form class="subscribe" action="<?= $base ?? '' ?>contact.php" method="get">
                <h3>Plan smarter</h3>
                <p>Ask our team about trips, hotels, vehicles, or guides.</p>
                <button class="btn btn-primary" type="submit">Contact Us</button>
            </form>
        </div>
        <div class="footer-bottom container">
            <span>Coursework prototype for CSE5009 Web Application Development.</span>
            <span>Secure payments are simulated for assessment purposes.</span>
        </div>
    </footer>
    <script src="<?= $base ?? '' ?>assets/js/app.js"></script>
</body>
</html>
