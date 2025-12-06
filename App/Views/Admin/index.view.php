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
            <div class="table-scroll-wrapper">
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <?php
                    if (!empty($bezci)) {
                        $cols = array_filter(array_keys($bezci[0]), 'is_string');
                        foreach($cols as $col): ?>
                            <th><?= htmlspecialchars($col) ?></th>
                        <?php endforeach;
                    }
                    ?>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($bezci as $bezc): ?>
                    <tr>
                        <?php foreach ($cols as $col): ?>
                            <td><?= htmlspecialchars($bezc[$col]) ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            </div>
            <h3>Roky konania</h3>
            <div class="table-scroll-wrapper">
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <?php
                    if (!empty($roky)) {
                        $colsRoky = array_filter(array_keys($roky[0]), 'is_string');
                        foreach($colsRoky as $col): ?>
                            <th><?= htmlspecialchars($col) ?></th>
                        <?php endforeach;
                    }
                    ?>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($roky as $rok): ?>
                    <tr>
                        <?php foreach ($colsRoky as $col): ?>
                            <td><?= htmlspecialchars($rok[$col]) ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            </div>
            <h3>Stanoviská</h3>
            <div class="table-scroll-wrapper">
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <?php
                    if (!empty($stanoviska)) {
                        $colsStan = array_filter(array_keys($stanoviska[0]), 'is_string');
                        foreach($colsStan as $col): ?>
                            <th><?= htmlspecialchars($col) ?></th>
                        <?php endforeach;
                    }
                    ?>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($stanoviska as $stan): ?>
                    <tr>
                        <?php foreach ($colsStan as $col): ?>
                            <td><?= htmlspecialchars($stan[$col]) ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>
</div>