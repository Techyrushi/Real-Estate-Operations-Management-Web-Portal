<!-- /.content-wrapper -->
<footer class="main-footer text-center">
    &copy;
    <?php echo date('Y'); ?> <a href="#">Real Estate Admin Dashboard</a>. All Rights Reserved.  
</footer>

<!-- Add the sidebar's background. This div must be placed immediately after the control sidebar -->
<div class="control-sidebar-bg"></div>

</div>
<!-- ./wrapper -->

<!-- Page Content overlay -->


<!-- Vendor JS -->
<script src="js/vendors.min.js"></script>
<script src="js/pages/chat-popup.js"></script>
<script src="../assets/icons/feather-icons/feather.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="../assets/vendor_components/chart.js-master/Chart.min.js"></script>
<script src="../assets/vendor_components/apexcharts-bundle/irregular-data-series.js"></script>
<script src="../assets/vendor_components/apexcharts-bundle/dist/apexcharts.js"></script>
<script src="../assets/vendor_components/Flot/jquery.flot.js"></script>
<script src="../assets/vendor_components/Flot/jquery.flot.resize.js"></script>
<script src="../assets/vendor_components/Flot/jquery.flot.pie.js"></script>
<script src="../assets/vendor_components/Flot/jquery.flot.categories.js"></script>

<!-- Master Admin App -->
<script src="js/template.js"></script>

<script>
    // Failsafe to hide preloader in case of JS errors
    (function () {
        function hideLoader() {
            var loader = document.getElementById('loader');
            if (loader) {
                loader.style.opacity = '0';
                setTimeout(function () {
                    loader.style.display = 'none';
                }, 500);
            }
        }

        // Try on DOMContentLoaded
        document.addEventListener("DOMContentLoaded", function () {
            setTimeout(hideLoader, 1000); // Give normal scripts a chance first
        });

        // Force hide on window load (failsafe)
        window.addEventListener("load", function () {
            setTimeout(hideLoader, 500);
        });

        // Ultimate fallback if everything hangs
        setTimeout(hideLoader, 3000);
    })();
</script>

<?php if (!isset($hide_dashboard_js) || !$hide_dashboard_js): ?>
    <script src="js/pages/dashboard.js"></script>
<?php endif; ?>

<?php if (isset($extra_js)): ?>
    <?php echo $extra_js; ?>
<?php endif; ?>

</body>

<!-- Mirrored from master-admin-template.multipurposethemes.com/bs5/real-estate/ by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 02 Feb 2026 09:55:44 GMT -->

</html>