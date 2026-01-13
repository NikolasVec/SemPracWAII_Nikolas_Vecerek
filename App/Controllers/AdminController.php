<?php

namespace App\Controllers;

use Framework\Core\BaseController;
use Framework\DB\Connection;
use Framework\Http\Request;
use Framework\Http\Responses\Response;

/**
 * Class AdminController
 *
 * This controller manages admin-related actions within the application.It extends the base controller functionality
 * provided by BaseController.
 *
 * @package App\Controllers
 */
class AdminController extends BaseController
{
    /**
     * Authorizes actions in this controller.
     *
     * This method checks if the user is logged in, allowing or denying access to specific actions based
     * on the authentication state.
     *
     * @param string $action The name of the action to authorize.
     * @return bool Returns true if the user is logged in; false otherwise.
     */
    public function authorize(Request $request, string $action): bool
    {
        // Only allow access to admin controller for logged-in users with admin privileges.
        return $this->user->isLoggedIn() && $this->user->isAdmin();
    }

    /**
     * Displays the index page of the admin panel.
     *
     * This action requires authorization. It returns an HTML response for the admin dashboard or main page.
     *
     * @return Response Returns a response object containing the rendered HTML.
     */
    public function index(Request $request): Response
    {
        $conn = Connection::getInstance();
        $bezci = $conn->query('SELECT * FROM Bezec')->fetchAll();
        $roky = $conn->query('SELECT * FROM rokKonania')->fetchAll();
        $stanoviska = $conn->query('SELECT * FROM Stanovisko')->fetchAll();

        // Try to load currently selected year for results from settings table (if exists)
        $currentResultsYear = null;
        try {
            // ensure settings table may not exist; select safely
            $stmt = $conn->query("SHOW TABLES LIKE 'settings'");
            $tableExists = $stmt->fetchColumn() !== false;
            if ($tableExists) {
                $s = $conn->prepare('SELECT v FROM settings WHERE k = ? LIMIT 1');
                $s->execute(['results_year']);
                $val = $s->fetchColumn();
                if ($val !== false && $val !== null && $val !== '') {
                    $currentResultsYear = $val;
                }
            }
        } catch (\Throwable $e) {
            // ignore - settings table may not exist
            $currentResultsYear = null;
        }

        return $this->html([
            'bezci' => $bezci,
            'roky' => $roky,
            'stanoviska' => $stanoviska,
            'currentResultsYear' => $currentResultsYear
        ]);
    }

    /**
     * Handles AJAX add requests for Bezec, rokKonania, Stanovisko.
     * @param Request $request
     * @return Response
     */
    public function add(Request $request): Response
    {
        // simple debug helper (optional)
        // file_put_contents(__DIR__ . '/debug.log', print_r($_POST, true) . PHP_EOL, FILE_APPEND);

        $section = $_GET['section'] ?? null;
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $conn = Connection::getInstance();

        if ($method !== 'POST') {
            return $this->json(['success' => false, 'message' => 'Nesprávna metóda.']);
        }

        try {
            if ($section === 'bezci') {
                $meno = trim($_POST['meno'] ?? '');
                $priezvisko = trim($_POST['priezvisko'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $pohlavie = trim($_POST['pohlavie'] ?? '');
                $ID_roka = $_POST['ID_roka'] ?? null;
                $cas = trim($_POST['cas_dobehnutia'] ?? '');

                // Normalize time: accept H:MM or H:MM:SS and convert to HH:MM:SS; empty -> null
                if ($cas === '') {
                    $casTime = null;
                } else {
                    if (preg_match('/^(\d{1,2}):(\d{2})(?::(\d{2}))?$/', $cas, $m)) {
                        $h = str_pad($m[1], 2, '0', STR_PAD_LEFT);
                        $i = $m[2];
                        $s = isset($m[3]) ? $m[3] : '00';
                        $casTime = "$h:$i:$s";
                    } else {
                        return $this->json(['success' => false, 'message' => 'Neplatný formát času (očakávané H:MM alebo H:MM:SS).']);
                    }
                }

                if ($meno === '' || $priezvisko === '' || $email === '' || $pohlavie === '' || !$ID_roka) {
                    return $this->json(['success' => false, 'message' => 'Chýbajúce údaje.']);
                }

                $stmt = $conn->prepare('INSERT INTO Bezec (meno, priezvisko, email, pohlavie, cas_dobehnutia, ID_roka) VALUES (?, ?, ?, ?, ?, ?)');
                $stmt->execute([$meno, $priezvisko, $email, $pohlavie, $casTime, $ID_roka]);

                // update participant count for the year
                $count = $conn->prepare('SELECT COUNT(*) FROM Bezec WHERE ID_roka = ?');
                $count->execute([$ID_roka]);
                $pocet = $count->fetchColumn();
                $upd = $conn->prepare('UPDATE rokKonania SET pocet_ucastnikov = ? WHERE ID_roka = ?');
                $upd->execute([$pocet, $ID_roka]);

                return $this->json(['success' => true]);
            } elseif ($section === 'roky') {
                $rok = trim($_POST['rok'] ?? '');
                $datum = trim($_POST['datum_konania'] ?? '');

                if ($rok === '' || $datum === '') {
                    return $this->json(['success' => false, 'message' => 'Chýbajúce údaje.']);
                }

                $stmt = $conn->prepare('INSERT INTO rokKonania (rok, datum_konania, pocet_ucastnikov) VALUES (?, ?, 0)');
                $stmt->execute([$rok, $datum]);
                $ID_roka = $conn->query('SELECT LAST_INSERT_ID()')->fetchColumn();

                // compute participants count (should be zero just after insert, but kept for consistency)
                $count = $conn->prepare('SELECT COUNT(*) FROM Bezec WHERE ID_roka = ?');
                $count->execute([$ID_roka]);
                $pocet = $count->fetchColumn();
                $upd = $conn->prepare('UPDATE rokKonania SET pocet_ucastnikov = ? WHERE ID_roka = ?');
                $upd->execute([$pocet, $ID_roka]);

                return $this->json(['success' => true]);
            } elseif ($section === 'stanoviska') {
                $nazov = trim($_POST['nazov'] ?? '');
                $poloha = trim($_POST['poloha'] ?? '');
                $popis = trim($_POST['popis'] ?? '');
                $ID_roka = $_POST['ID_roka'] ?? null;

                if ($nazov === '' || !$ID_roka) {
                    return $this->json(['success' => false, 'message' => 'Chýbajúce údaje.']);
                }

                $stmt = $conn->prepare('INSERT INTO Stanovisko (nazov, poloha, popis, ID_roka) VALUES (?, ?, ?, ?)');
                $stmt->execute([$nazov, $poloha, $popis, $ID_roka]);

                return $this->json(['success' => true]);
            } else {
                return $this->json(['success' => false, 'message' => 'Neznáma sekcia.']);
            }
        } catch (\Throwable $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Delete a record by section and id
     */
    public function delete(Request $request): Response
    {
        $section = $_GET['section'] ?? null;
        $id = $_GET['id'] ?? null;

        if (!$section || !$id) {
            return $this->json(['success' => false, 'message' => 'Chýba sekcia alebo ID.']);
        }

        $conn = Connection::getInstance();

        try {
            if ($section === 'bezci') {
                $stmt = $conn->prepare('DELETE FROM Bezec WHERE ID_bezca = ?');
            } elseif ($section === 'roky') {
                $stmt = $conn->prepare('DELETE FROM rokKonania WHERE ID_roka = ?');
            } elseif ($section === 'stanoviska') {
                $stmt = $conn->prepare('DELETE FROM Stanovisko WHERE ID_stanoviska = ?');
            } else {
                return $this->json(['success' => false, 'message' => 'Neznáma sekcia.']);
            }

            $stmt->execute([$id]);

            // If deleted a rok, optionally update participant counts or cascade as DB schema requires
            return $this->json(['success' => true]);
        } catch (\Throwable $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Get a single record for edit
     */
    public function get(Request $request): Response
    {
        $section = $_GET['section'] ?? null;
        $id = $_GET['id'] ?? null;

        if (!$section || !$id) {
            return $this->json(['success' => false, 'message' => 'Chýba sekcia alebo ID.']);
        }

        $conn = Connection::getInstance();

        try {
            if ($section === 'bezci') {
                $stmt = $conn->prepare('SELECT * FROM Bezec WHERE ID_bezca = ?');
            } elseif ($section === 'roky') {
                $stmt = $conn->prepare('SELECT * FROM rokKonania WHERE ID_roka = ?');
            } elseif ($section === 'stanoviska') {
                $stmt = $conn->prepare('SELECT * FROM Stanovisko WHERE ID_stanoviska = ?');
            } else {
                return $this->json(['success' => false, 'message' => 'Neznáma sekcia.']);
            }

            $stmt->execute([$id]);
            $item = $stmt->fetch();

            if (!$item) {
                return $this->json(['success' => false, 'message' => 'Záznam neexistuje.']);
            }

            return $this->json(['success' => true, 'item' => $item]);
        } catch (\Throwable $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Update a record by section
     */
    public function update(Request $request): Response
    {
        $section = $_GET['section'] ?? null;
        $id = $_POST['id'] ?? null;

        if (!$section || !$id) {
            return $this->json(['success' => false, 'message' => 'Chýba sekcia alebo ID.']);
        }

        $conn = Connection::getInstance();

        try {
            if ($section === 'bezci') {
                $meno = trim($_POST['meno'] ?? '');
                $priezvisko = trim($_POST['priezvisko'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $pohlavie = trim($_POST['pohlavie'] ?? '');
                $ID_roka = $_POST['ID_roka'] ?? null;
                $cas = trim($_POST['cas_dobehnutia'] ?? '');

                if ($cas === '') {
                    $casTime = null;
                } else {
                    if (preg_match('/^(\d{1,2}):(\d{2})(?::(\d{2}))?$/', $cas, $m)) {
                        $h = str_pad($m[1], 2, '0', STR_PAD_LEFT);
                        $i = $m[2];
                        $s = isset($m[3]) ? $m[3] : '00';
                        $casTime = "$h:$i:$s";
                    } else {
                        return $this->json(['success' => false, 'message' => 'Neplatný formát času (očakávané H:MM alebo H:MM:SS).']);
                    }
                }

                if ($meno === '' || $priezvisko === '' || $email === '' || $pohlavie === '' || !$ID_roka) {
                    return $this->json(['success' => false, 'message' => 'Chýbajúce údaje.']);
                }

                $stmt = $conn->prepare('UPDATE Bezec SET meno=?, priezvisko=?, email=?, pohlavie=?, cas_dobehnutia=?, ID_roka=? WHERE ID_bezca=?');
                $stmt->execute([$meno, $priezvisko, $email, $pohlavie, $casTime, $ID_roka, $id]);

                // update participant counts for affected years (simple approach: update the target year)
                $count = $conn->prepare('SELECT COUNT(*) FROM Bezec WHERE ID_roka = ?');
                $count->execute([$ID_roka]);
                $pocet = $count->fetchColumn();
                $upd = $conn->prepare('UPDATE rokKonania SET pocet_ucastnikov = ? WHERE ID_roka = ?');
                $upd->execute([$pocet, $ID_roka]);

            } elseif ($section === 'roky') {
                $rok = trim($_POST['rok'] ?? '');
                $datum = trim($_POST['datum_konania'] ?? '');

                if ($rok === '' || $datum === '') {
                    return $this->json(['success' => false, 'message' => 'Chýbajúce údaje.']);
                }

                $stmt = $conn->prepare('UPDATE rokKonania SET rok=?, datum_konania=? WHERE ID_roka=?');
                $stmt->execute([$rok, $datum, $id]);

            } elseif ($section === 'stanoviska') {
                $nazov = trim($_POST['nazov'] ?? '');
                $poloha = trim($_POST['poloha'] ?? '');
                $popis = trim($_POST['popis'] ?? '');
                $ID_roka = $_POST['ID_roka'] ?? null;

                if ($nazov === '' || !$ID_roka) {
                    return $this->json(['success' => false, 'message' => 'Chýbajúce údaje.']);
                }

                $stmt = $conn->prepare('UPDATE Stanovisko SET nazov=?, poloha=?, popis=?, ID_roka=? WHERE ID_stanoviska=?');
                $stmt->execute([$nazov, $poloha, $popis, $ID_roka, $id]);

            } else {
                return $this->json(['success' => false, 'message' => 'Neznáma sekcia.']);
            }

            return $this->json(['success' => true]);
        } catch (\Throwable $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // New action to set which year is used on the public results page
    public function setResultsYear(Request $request): Response
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->json(['success' => false, 'message' => 'Nesprávna metóda.']);
        }

        $id = $_POST['id'] ?? null; // ID_roka or empty to clear
        try {
            $conn = Connection::getInstance();
            // create settings table if not exists
            $conn->exec("CREATE TABLE IF NOT EXISTS settings (k VARCHAR(64) PRIMARY KEY, v VARCHAR(255) NULL)");

            if ($id === null || $id === '') {
                // remove the setting
                $stmt = $conn->prepare('DELETE FROM settings WHERE k = ?');
                $stmt->execute(['results_year']);
            } else {
                // upsert setting
                // Try update first
                $u = $conn->prepare('UPDATE settings SET v = ? WHERE k = ?');
                $u->execute([$id, 'results_year']);
                if ($u->rowCount() === 0) {
                    $i = $conn->prepare('INSERT INTO settings (k, v) VALUES (?, ?)');
                    $i->execute(['results_year', $id]);
                }
            }

            return $this->json(['success' => true]);
        } catch (\Throwable $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
