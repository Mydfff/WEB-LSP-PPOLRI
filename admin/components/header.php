<?php
// ==========================================================
// HEADER ADMIN - LSP PPPOLRI
// File: admin/components/header.php
// ==========================================================

$pageTitle = $pageTitle ?? "Dashboard";
$pageSubtitle = $pageSubtitle ?? "Panel Administrasi LSP PPPOLRI";
?>

<header class="admin-topbar">

    <!-- =====================================================
         HAMBURGER
    ====================================================== -->
    <button
        type="button"
        class="sidebar-toggle"
        id="sidebarToggle"
        aria-label="Buka menu"
        aria-expanded="false"
    >
        <i class="bi bi-list"></i>
    </button>


    <!-- =====================================================
         PAGE TITLE
    ====================================================== -->
    <div class="topbar-title">

        <h2>
            <?php echo htmlspecialchars($pageTitle); ?>
        </h2>

        <span>
            <?php echo htmlspecialchars($pageSubtitle); ?>
        </span>

    </div>


    <!-- =====================================================
         TOPBAR ACTIONS
    ====================================================== -->
    <div class="topbar-actions">


        <!-- Notification -->
        <button
            type="button"
            class="notification-btn"
            aria-label="Notifikasi"
        >
            <i class="bi bi-bell"></i>

            <span class="notification-badge">
                3
            </span>
        </button>


        <!-- Admin Profile -->
        <a
            href="../profile.php"
            class="admin-profile"
            title="Profil Admin"
        >

            <div class="admin-avatar">
                <i class="bi bi-person-fill"></i>
            </div>

            <div class="admin-profile-info">

                <strong>
                    <?php echo htmlspecialchars($adminNama ?? "Administrator"); ?>
                </strong>

                <span>
                    <?php echo htmlspecialchars($adminRole ?? "Administrator"); ?>
                </span>

            </div>

            <i class="bi bi-chevron-down profile-arrow"></i>

        </a>

    </div>

</header>