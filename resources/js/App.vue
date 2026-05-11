<template>
    <div class="app-shell">
        <header class="topbar">
            <RouterLink to="/" class="brand">
                <span class="brand-mark">ᮜ</span>

                <span class="brand-text">
                    <strong>LARAS</strong>
                    <small>Ladang Aksara Siliwangi</small>
                </span>
            </RouterLink>

            <nav class="nav-menu">
                <RouterLink to="/">Home</RouterLink>
                <RouterLink to="/chapters">Chapter</RouterLink>
                <RouterLink to="/leaderboard">Leaderboard</RouterLink>
                <RouterLink to="/profile">Profile</RouterLink>
            </nav>
        </header>

        <main class="main-content">
            <RouterView />
        </main>
    </div>
</template>