<template>
    <section class="page-section">
        <div>
            <p class="eyebrow">Story Game</p>
            <h1>Level {{ id }}</h1>
        </div>

        <div class="game-placeholder">
            <p>
                Area typing game akan dibangun pada tahap berikutnya.
                Untuk sekarang, halaman ini memastikan route dinamis
                <strong>/story/levels/:id</strong> sudah berjalan.
            </p>

            <RouterLink to="/chapters" class="btn btn-secondary">
                Kembali ke Chapter
            </RouterLink>
        </div>
    </section>
</template>

<script setup>
defineProps({
    id: {
        type: String,
        required: true,
    },
});
</script>