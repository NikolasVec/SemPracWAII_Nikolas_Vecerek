<?php

?>

<h1>
    <br>
    <br>
</h1>

<div class="container mt-4">
    <h1>Výsledky behu</h1>

    <?php if (empty($resultsYear)): ?>
        <div class="alert alert-warning">Výsledky momentálne nie sú k dispozícii.</div>
    <?php else: ?>
        <p>Zobrazujú sa výsledky pre rok: <strong><?= htmlspecialchars((string)($resultsYearLabel ?? $resultsYear)) ?></strong></p>

        <div class="row">
            <div class="col-md-6">
                <h2>Muži (M)</h2>
                <?php if (!empty($maleResults)): ?>
                    <?php
                    // keep only those with a non-empty time
                    $finishedM = array_filter($maleResults, function($r) { return !empty($r['cas_dobehnutia']); });
                    ?>
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
                <h2>Ženy (Ž)</h2>
                <?php if (!empty($femaleResults)): ?>
                    <?php
                    $finishedF = array_filter($femaleResults, function($r) { return !empty($r['cas_dobehnutia']); });
                    ?>
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
