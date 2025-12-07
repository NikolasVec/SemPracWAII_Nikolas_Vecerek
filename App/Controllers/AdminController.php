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
        return $this->user->isLoggedIn();
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
        return $this->html([
            'bezci' => $bezci,
            'roky' => $roky,
            'stanoviska' => $stanoviska
        ]);
    }

    /**
     * Handles AJAX add requests for Bezec, rokKonania, Stanovisko.
     * @param Request $request
     * @return Response
     */
    public function add(Request $request): Response
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $section = $_GET['section'] ?? null;
        $conn = Connection::getInstance();

        if ($method !== 'POST') {
            return $this->json(['success' => false, 'message' => 'Nesprávna metóda.']);
        }

        try {
            if ($section === 'bezci') {
                $meno = $_POST['meno'] ?? null;
                $priezvisko = $_POST['priezvisko'] ?? null;
                $email = $_POST['email'] ?? null;
                $pohlavie = $_POST['pohlavie'] ?? null;
                $ID_roka = $_POST['ID_roka'] ?? null;

                if (!$meno || !$priezvisko || !$email || !$pohlavie || !$ID_roka) {
                    return $this->json(['success' => false, 'message' => 'Chýbajúce údaje.']);
                }

                $stmt = $conn->prepare('INSERT INTO Bezec (meno, priezvisko, email, pohlavie, ID_roka) VALUES (?, ?, ?, ?, ?)');
                $stmt->execute([$meno, $priezvisko, $email, $pohlavie, $ID_roka]);

                // Aktualizuj pocet_ucastnikov v rokKonania
                $count = $conn->prepare('SELECT COUNT(*) FROM Bezec WHERE ID_roka = ?');
                $count->execute([$ID_roka]);
                $pocet = (int)$count->fetchColumn();
                $upd = $conn->prepare('UPDATE rokKonania SET pocet_ucastnikov = ? WHERE ID_roka = ?');
                $upd->execute([$pocet, $ID_roka]);

                return $this->json(['success' => true]);

            } elseif ($section === 'roky') {
                $rok = $_POST['rok'] ?? null;
                $datum = $_POST['datum_konania'] ?? null;

                if ($rok === null || $datum === null || $rok === '' || $datum === '') {
                    return $this->json(['success' => false, 'message' => 'Chýbajúce údaje.']);
                }

                $stmt = $conn->prepare('INSERT INTO rokKonania (rok, datum_konania, pocet_ucastnikov) VALUES (?, ?, 0)');
                $stmt->execute([$rok, $datum]);
                $ID_roka = $conn->query('SELECT LAST_INSERT_ID()')->fetchColumn();

                // Spočítaj bežcov a aktualizuj
                $count = $conn->prepare('SELECT COUNT(*) FROM Bezec WHERE ID_roka = ?');
                $count->execute([$ID_roka]);
                $pocet = (int)$count->fetchColumn();
                $upd = $conn->prepare('UPDATE rokKonania SET pocet_ucastnikov = ? WHERE ID_roka = ?');
                $upd->execute([$pocet, $ID_roka]);

                return $this->json(['success' => true, 'ID_roka' => $ID_roka]);

            } elseif ($section === 'stanoviska') {
                $nazov = $_POST['nazov'] ?? null;
                $poloha = $_POST['poloha'] ?? null;
                $popis = $_POST['popis'] ?? null;
                $ID_roka = $_POST['ID_roka'] ?? null;

                if (!$nazov || !$ID_roka) {
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
     * Vymaže záznam podľa sekcie a ID
     */
    public function delete(Request $request): Response
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $section = $_GET['section'] ?? null;
        $id = $_GET['id'] ?? null;
        $conn = Connection::getInstance();

        if (!$section || !$id) {
            return $this->json(['success' => false, 'message' => 'Chýba sekcia alebo ID.']);
        }

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
            return $this->json(['success' => true]);
        } catch (\Throwable $e) {
            return $this->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Získa záznam podľa sekcie a ID (pre editáciu)
     */
    public function get(Request $request): Response
    {
        $section = $_GET['section'] ?? null;
        $id = $_GET['id'] ?? null;
        $conn = Connection::getInstance();

        if (!$section || !$id) {
            return $this->json(['success' => false, 'message' => 'Chýba sekcia alebo ID.']);
        }

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
     * Upraví záznam podľa sekcie a ID
     */
    public function update(Request $request): Response
    {
        $section = $_GET['section'] ?? null;
        $id = $_POST['id'] ?? null;
        $conn = Connection::getInstance();

        if (!$section || !$id) {
            return $this->json(['success' => false, 'message' => 'Chýba sekcia alebo ID.']);
        }

        try {
            if ($section === 'bezci') {
                $meno = $_POST['meno'] ?? null;
                $priezvisko = $_POST['priezvisko'] ?? null;
                $email = $_POST['email'] ?? null;
                $pohlavie = $_POST['pohlavie'] ?? null;
                $ID_roka = $_POST['ID_roka'] ?? null;

                if (!$meno || !$priezvisko || !$email || !$pohlavie || !$ID_roka) {
                    return $this->json(['success' => false, 'message' => 'Chýbajúce údaje.']);
                }

                $stmt = $conn->prepare('UPDATE Bezec SET meno=?, priezvisko=?, email=?, pohlavie=?, ID_roka=? WHERE ID_bezca=?');
                $stmt->execute([$meno, $priezvisko, $email, $pohlavie, $ID_roka, $id]);

                // Aktualizuj pocet_ucastnikov pre prípad zmeny roka
                $count = $conn->prepare('SELECT COUNT(*) FROM Bezec WHERE ID_roka = ?');
                $count->execute([$ID_roka]);
                $pocet = (int)$count->fetchColumn();
                $upd = $conn->prepare('UPDATE rokKonania SET pocet_ucastnikov = ? WHERE ID_roka = ?');
                $upd->execute([$pocet, $ID_roka]);

            } elseif ($section === 'roky') {
                $rok = $_POST['rok'] ?? null;
                $datum = $_POST['datum_konania'] ?? null;
                if ($rok === null || $datum === null || $rok === '' || $datum === '') {
                    return $this->json(['success' => false, 'message' => 'Chýbajúce údaje.']);
                }
                $stmt = $conn->prepare('UPDATE rokKonania SET rok=?, datum_konania=? WHERE ID_roka=?');
                $stmt->execute([$rok, $datum, $id]);

            } elseif ($section === 'stanoviska') {
                $nazov = $_POST['nazov'] ?? null;
                $poloha = $_POST['poloha'] ?? null;
                $popis = $_POST['popis'] ?? null;
                $ID_roka = $_POST['ID_roka'] ?? null;

                if (!$nazov || !$ID_roka) {
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
}
