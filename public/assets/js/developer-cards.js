<script>
// Make developer cards clickable
    document.addEventListener('DOMContentLoaded', function() {
        // Add click handlers to PHP-generated cards
        document.querySelectorAll('#developers-list .project-card').forEach(card => {
            const devId = card.dataset.devId;
            if (devId) {
                card.style.cursor = 'pointer';
                card.addEventListener('click', () => {
                    window.location.href = '/developers/show?id=' + devId;
                });
            }
        });
});
</script>
