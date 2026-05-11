<template>
    <section class="hero-section">
        <div class="hero-copy">
            <p class="eyebrow">Web-based Educational Typing Game</p>

            <h1>Ladang Aksara Siliwangi</h1>

            <p class="hero-description">
                LARAS adalah game edukatif mengetik berbasis cerita.
                Pemain akan menyelesaikan level, meningkatkan WPM,
                menjaga akurasi, dan membuka perjalanan aksara dari BAB ke BAB.
            </p>

            <div class="hero-actions">
                <RouterLink to="/chapters" class="btn btn-primary">
                    Mulai Story Mode
                </RouterLink>

                <RouterLink to="/leaderboard" class="btn btn-secondary">
                    Lihat Leaderboard
                </RouterLink>
            </div>
        </div>

        <div class="hero-card">
            <div class="avatar-placeholder">ᮞ</div>

            <h2>Sang Juru Aksara</h2>

            <p>
                Karakter pendamping akan bereaksi saat pemain mengetik,
                melakukan combo, menang, atau gagal.
            </p>
        </div>
    </section>
</template>