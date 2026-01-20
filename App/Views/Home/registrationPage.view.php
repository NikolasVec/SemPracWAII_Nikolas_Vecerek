<?php

?>

<h1>
</h1>
<br><br>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card registration-card shadow">
                <div class="card-body">
                    <h2 class="card-title text-center mb-4">Registrácia na beh</h2>
                    <?php
                    if (!empty($success)) {
                        echo '<div class="alert alert-success">' . htmlspecialchars($success) . '</div>';
                    }
                    if (!empty($error)) {
                        echo '<div class="alert alert-danger">' . htmlspecialchars($error) . '</div>';
                    }
                    ?>
                    <form method="post" action="">
                        <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string)($csrfToken ?? '')) ?>">
                        <div class="mb-3">
                            <label for="meno" class="form-label">Meno:</label>
                            <input type="text" id="meno" name="meno" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="priezvisko" class="form-label">Priezvisko:</label>
                            <input type="text" id="priezvisko" name="priezvisko" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email:</label>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="pohlavie" class="form-label">Pohlavie:</label>
                            <select id="pohlavie" name="pohlavie" class="form-select" required>
                                <option value="M">Muž</option>
                                <option value="Ž">Žena</option>
                            </select>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-warning btn-block">Registrovať</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
