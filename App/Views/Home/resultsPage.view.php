<?php

?>

<h1>
    <br>
    <br>
</h1>

<div class="container mt-4">

    <?php if (empty($resultsYear)): ?>
        <div class="alert alert-warning">Výsledky momentálne nie sú k dispozícii.</div>
    <?php else: ?>
        <H1>Výsledky behu pre rok: <strong><?= htmlspecialchars((string)($resultsYearLabel ?? $resultsYear)) ?></strong></H1>
        <br>
        <div class="row">
            <div class="col-md-6">
                <h2>Muži</h2>
                <?php
                // presentation-only: use prepared variables from controller, fallback to safe defaults
                $finishedM = $finishedM ?? [];
                $maleResults = $maleResults ?? [];
                ?>

                <?php if (!empty($maleResults)): ?>
                    <?php if (!empty($finishedM)): ?>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Poradie</th>
                                <th>Meno</th>
                                <th>Priezvisko</th>
                                <th>Čas</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $rank = 1; foreach ($finishedM as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars((string)$rank) ?></td>
                                <td><?= htmlspecialchars((string)($row['meno'] ?? '')) ?></td>
                                <td><?= htmlspecialchars((string)($row['priezvisko'] ?? '')) ?></td>
                                <td><?= htmlspecialchars((string)($row['cas_dobehnutia'] ?? '')) ?></td>
                            </tr>
                        <?php $rank++; endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                        <p class="text-muted">Žiadne dokončené výsledky pre mužov v tomto roku.</p>
                    <?php endif; ?>
                <?php else: ?>
                    <p class="text-muted">Žiadne záznamy pre mužov v tomto roku.</p>
                <?php endif; ?>
            </div>

            <div class="col-md-6">
                <h2>Ženy </h2>
                <?php
                // presentation-only: use prepared variables from controller, fallback to safe defaults
                $finishedF = $finishedF ?? [];
                $femaleResults = $femaleResults ?? [];
                ?>

                <?php if (!empty($femaleResults)): ?>
                    <?php if (!empty($finishedF)): ?>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Poradie</th>
                                <th>Meno</th>
                                <th>Priezvisko</th>
                                <th>Čas</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $rank = 1; foreach ($finishedF as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars((string)$rank) ?></td>
                                <td><?= htmlspecialchars((string)($row['meno'] ?? '')) ?></td>
                                <td><?= htmlspecialchars((string)($row['priezvisko'] ?? '')) ?></td>
                                <td><?= htmlspecialchars((string)($row['cas_dobehnutia'] ?? '')) ?></td>
                            </tr>
                        <?php $rank++; endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                        <p class="text-muted">Žiadne dokončené výsledky pre ženy v tomto roku.</p>
                    <?php endif; ?>
                <?php else: ?>
                    <p class="text-muted">Žiadne záznamy pre ženy v tomto roku.</p>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

</div>
