<?php

/** @var \Framework\Support\LinkGenerator $link */
/** @var \Framework\Support\View $view */

$view->setLayout('auth');
?>

<div class="container">
    <div class="row">
        <div class="col-sm-9 col-md-7 col-lg-5 mx-auto">
            <div class="card card-signin my-5">
                <div class="card-body">
                    <h5 class="card-title text-center">Registrácia</h5>
                    <form class="form-signin" method="post" action="<?= $link->url('auth.registerUser') ?>">
                        <div class="form-label-group mb-3">
                            <label for="firstName" class="form-label">Meno</label>
                            <input name="firstName" type="text" id="firstName" class="form-control" placeholder="Meno" required>
                        </div>

                        <div class="form-label-group mb-3">
                            <label for="lastName" class="form-label">Priezvisko</label>
                            <input name="lastName" type="text" id="lastName" class="form-control" placeholder="Priezvisko" required>
                        </div>

                        <div class="form-label-group mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input name="email" type="email" id="email" class="form-control" placeholder="Email" required>
                        </div>

                        <div class="form-label-group mb-3">
                            <label for="password" class="form-label">Heslo</label>
                            <input name="password" type="password" id="password" class="form-control" placeholder="Heslo" required>
                        </div>

                        <div class="form-label-group mb-3">
                            <label for="confirmPassword" class="form-label">Potvrďte heslo</label>
                            <input name="confirmPassword" type="password" id="confirmPassword" class="form-control" placeholder="Potvrďte heslo" required>
                        </div>

                        <div class="form-label-group mb-3">
                            <label for="birthDate" class="form-label">Dátum narodenia</label>
                            <input name="birthDate" type="date" id="birthDate" class="form-control" required>
                        </div>

                        <div class="form-label-group mb-3">
                            <label for="gender" class="form-label">Pohlavie</label>
                            <select name="gender" id="gender" class="form-control" required>
                                <option value="M">Muž</option>
                                <option value="Z">Žena</option>
                            </select>
                        </div>

                        <div class="text-center">
                            <button class="btn btn-primary" type="submit">Registrovať</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/js/script.js"></script>
