<?php

/** @var \Framework\Support\LinkGenerator $link */
/** @var \Framework\Auth\AppUser $user */
?>

<div class="container-fluid">
    <div class="row">
        <div class="col">
            <div>
                <br><br><br>
                Welcome, <strong><?= $user->getName() ?></strong>!<br><br>
                This part of the application is accessible only after logging in.
            </div>
            <hr>
            <h3>Bežci</h3>
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <?php if (!empty($bezci)) foreach(array_keys($bezci[0]) as $col): ?>
                        <th><?= htmlspecialchars($col) ?></th>
                    <?php endforeach; ?>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($bezci as $bezc): ?>
                    <tr>
                        <?php foreach ($bezc as $val): ?>
                            <td><?= htmlspecialchars($val) ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <h3>Roky konania</h3>
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <?php if (!empty($roky)) foreach(array_keys($roky[0]) as $col): ?>
                        <th><?= htmlspecialchars($col) ?></th>
                    <?php endforeach; ?>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($roky as $rok): ?>
                    <tr>
                        <?php foreach ($rok as $val): ?>
                            <td><?= htmlspecialchars($val) ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <h3>Stanoviská</h3>
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <?php if (!empty($stanoviska)) foreach(array_keys($stanoviska[0]) as $col): ?>
                        <th><?= htmlspecialchars($col) ?></th>
                    <?php endforeach; ?>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($stanoviska as $stan): ?>
                    <tr>
                        <?php foreach ($stan as $val): ?>
                            <td><?= htmlspecialchars($val) ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>