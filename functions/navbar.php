<img src="../assets/logo.png" alt="marinmart icon" height="40px" width="40px">
<div class="hamburger-menu" id="hamburger-menu">
    <div class="bar"></div>
    <div class="bar"></div>
    <div class="bar"></div>
</div>
<nav class="nav" id="main-nav">
    <a href="dashboard.php">DASHBOARD</a>
    <?php 
        if($_SESSION['role'] == 'admin'){
            echo '<a href="add_form.php">CREATE</a>';
        }
        if($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'manager'){
            echo '
                <a href="tables.php">TABLES</a>
                <a href="queries.php">QUERIES</a>
            ';
        }
        if($_SESSION['role'] == 'user'){
            echo '
                <a href="products.php">PRODUCTS</a>
                <a href="categories.php">CATEGORIES</a>
            ';
        }
    ?>
</nav>
<div class="profile">
    <a href="#" class="user-dropdown-toggle">
        <img src="../assets/user.png" alt="profile icon" height="40px" width="40px">
    </a>
    <div class="user-dropdown">
        <?php 
            switch ($_SESSION['role']) {
                case 'admin':
                    echo '<p>Hi '. $username .' (admin)</p>';
                    break;
                case 'manager':
                    echo '<p>Hi '. $username .' (manager)</p>';
                    break;
                case 'user':
                    echo '<p>Hi '. ($contactPerson ? $contactPerson : $username) .' (user)</p>';
                    break;
                default:
                    echo '<p>Hi, Guest</p>';
                    break;
            }
        ?>
        
        <!-- Dropdown Links -->
        <?php 
        if($_SESSION['role'] == 'user'){
            echo '
                <a href="profile.php">Profile</a>
                <a href="settings.php">Settings</a>
            ';
        }
        ?>
        <a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . '?logout=true'; ?>">Logout</a>
        <?php
        if (isset($_GET['logout']) && $_GET['logout'] === 'true') {
        session_destroy();
        header('Location: ../index.php'); 

        exit; 
        }
        ?>
    </div>
</div>