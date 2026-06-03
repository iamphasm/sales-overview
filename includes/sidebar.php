<?php $current_page = $_GET['page'] ?? 'home'; ?>
<nav class="sidebar">
    <div class="sidebar-logo">
        <span class="logo-emoji">🤝</span>
        <span class="logo-text">Sales Overview v1.0</span>
    </div>
    <ul class="sidebar-menu">
        <li>
            <a href="?page=home" class="<?= $current_page === 'home' ? 'active' : '' ?>">
                <i class="fas fa-home"></i>
                <span>Home</span>
            </a>
        </li>
        <li>
            <a href="?page=live" class="<?= $current_page === 'live' ? 'active' : '' ?>">
                <i class="fas fa-gavel"></i>
                <span>Live Sales</span>
            </a>
        </li>
        <li>
            <a href="?page=finished" class="<?= $current_page === 'finished' ? 'active' : '' ?>">
                <i class="fas fa-check-circle"></i>
                <span>Finished Sales</span>
            </a>
        </li>
        <li>
            <a href="?page=statistics" class="<?= $current_page === 'statistics' ? 'active' : '' ?>">
                <i class="fas fa-chart-bar"></i>
                <span>Statistics</span>
            </a>
        </li>
        <li>
            <a href="?page=add" class="<?= $current_page === 'add' ? 'active' : '' ?>">
                <i class="fas fa-plus-circle"></i>
                <span>Add new Sale</span>
            </a>
        </li>
    </ul>
    <div class="sidebar-divider"></div>
    <ul class="sidebar-menu">
        <li>
            <a href="?page=about" class="<?= $current_page === 'about' ? 'active' : '' ?>">
                <i class="fas fa-info-circle"></i>
                <span>About</span>
            </a>
        </li>
        <li>
            <a href="?page=settings" class="<?= $current_page === 'settings' ? 'active' : '' ?>">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>
        </li>
        <li>
            <a href="api/logout.php" class="logout-link">
                <i class="fas fa-sign-out-alt"></i>
                <span>Log Out</span>
            </a>
        </li>
    </ul>
</nav>
