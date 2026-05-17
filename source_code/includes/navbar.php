<?php checkLogin(); ?>
<nav class="navbar navbar-dark navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="dashboard.php">
            <i class="fas fa-users-cog me-2"></i>Customer MS
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>"
                       href="dashboard.php">
                        <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>"
                       href="index.php">
                        <i class="fas fa-users me-1"></i>Customers
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'quotation.php' ? 'active' : '' ?>"
                       href="quotation.php">
                        <i class="fas fa-file-invoice me-1"></i>Quotation
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'quotation_list.php' ? 'active' : '' ?>"
                       href="quotation_list.php">
                        <i class="fas fa-list me-1"></i>Quotation History
                    </a>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item me-3">
                    <span class="nav-link text-warning">
                        <i class="fas fa-user-circle me-1"></i>
                        <?= htmlspecialchars($_SESSION['name']) ?>
                        <span class="badge bg-warning text-dark ms-1"><?= ucfirst($_SESSION['role']) ?></span>
                    </span>
                </li>
                <li class="nav-item">
                    <a class="btn btn-outline-light btn-sm" href="logout.php">
                        <i class="fas fa-sign-out-alt me-1"></i>Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
