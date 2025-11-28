</main>
<footer>
    <div class="container">
        <p>&copy; <?= date('Y') ?> GenevaSkills. Tous droits réservés.</p>
    </div>
</footer>
<?php
use App\Config\AppConfig;
$isLocal = AppConfig::getInstance()->isLocal();
$basePath = $isLocal ? '' : '/public';
?>
<script src="<?= $basePath ?>/assets/js/main.js"></script>
</body>

</html>