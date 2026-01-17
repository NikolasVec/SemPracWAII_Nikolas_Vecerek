<?php

namespace App\Controllers;

use Framework\Core\BaseController;
use Framework\DB\Connection;
use Framework\Http\Request;
use Framework\Http\Responses\Response;

/**
 * Class HomeController
 * Handles actions related to the home page and other public actions.
 *
 * This controller includes actions that are accessible to all users, including a default landing page and a contact
 * page. It provides a mechanism for authorizing actions based on user permissions.
 *
 * @package App\Controllers
 */
class HomeController extends BaseController
{
    /**
     * Authorizes controller actions based on the specified action name.
     *
     * In this implementation, all actions are authorized unconditionally.
     *
     * @param string $action The action name to authorize.
     * @return bool Returns true, allowing all actions.
     */
    public function authorize(Request $request, string $action): bool
    {
        return true;
    }

    /**
     * Displays the default home page.
     *
     * This action serves the main HTML view of the home page.
     *
     * @return Response The response object containing the rendered HTML for the home page.
     */
    public function index(Request $request): Response
    {
        return $this->html();
    }

    /**
     * Displays the informacie page.
     *
     * This action serves the HTML view for the informacie page, which is accessible to all users without any
     * authorization.
     *
     * @return Response The response object containing the rendered HTML for the informacie page.
     */
    public function informacie(Request $request): Response
    {
        return $this->html();
    }

    /**
     * Displays the contact page.
     *
     * This action serves the HTML view for the contact page, which is accessible to all users without any
     * authorization.
     *
     * @return Response The response object containing the rendered HTML for the contact page.
     */
    public function mapa(Request $request): Response
    {
        $conn = \Framework\DB\Connection::getInstance();
        $stanoviska = [];
        $error = null;
        try {
            $stmt = $conn->query('SELECT * FROM Stanovisko');
            $stanoviska = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            // capture error for debugging in the view
            $stanoviska = [];
            $error = $e->getMessage();
        }

        return $this->html(['stanoviska' => $stanoviska, 'mapa_error' => $error]);
    }
    public function galleryPage(Request $request): Response
    {
        $conn = Connection::getInstance();
        $albums = [];
        try {
            $stmt = $conn->query("SHOW TABLES LIKE 'albums'");
            $tableExists = $stmt->fetchColumn() !== false;
            if ($tableExists) {
                $stmtA = $conn->query('SELECT * FROM albums ORDER BY created_at DESC');
                $rows = $stmtA->fetchAll();
                foreach ($rows as $row) {
                    $stmtP = $conn->prepare('SELECT * FROM photos WHERE album_id = ? ORDER BY created_at ASC');
                    $stmtP->execute([$row['ID_album']]);
                    $photos = $stmtP->fetchAll();
                    $albums[] = [
                        'album' => $row,
                        'photos' => $photos
                    ];
                }
            }
        } catch (\Throwable $e) {
            // ignore DB errors and show empty gallery
            $albums = [];
        }

        return $this->html(['albums' => $albums]);
    }
    public function registrationPage(Request $request): Response
    {
        $success = null;
        $error = null;
        if ($request->isPost()) {
            $meno = $request->post('meno');
            $priezvisko = $request->post('priezvisko');
            $email = $request->post('email');
            $pohlavie = $request->post('pohlavie');
            $rok = date('Y');
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Zadaný email nemá správny formát.';
            } else {
                try {
                    $conn = \Framework\DB\Connection::getInstance();
                    // Zisti ID_roka pre aktuálny rok
                    $stmt = $conn->prepare('SELECT ID_roka FROM rokKonania WHERE rok = ? LIMIT 1');
                    $stmt->execute([$rok]);
                    $row = $stmt->fetch();
                    if ($row) {
                        $id_roka = $row['ID_roka'];
                    } else {
                        // Ak neexistuje, vytvor nový záznam
                        $stmt = $conn->prepare('INSERT INTO rokKonania (rok, datum_konania, pocet_ucastnikov, pocet_stanovisk) VALUES (?, ?, 0, 0)');
                        $stmt->execute([$rok, date('Y-m-d')]);
                        $id_roka = $conn->lastInsertId();
                    }
                    // Ulož bežca
                    $stmt = $conn->prepare('INSERT INTO Bezec (meno, priezvisko, email, pohlavie, ID_roka) VALUES (?, ?, ?, ?, ?)');
                    $stmt->execute([$meno, $priezvisko, $email, $pohlavie, $id_roka]);
                    // Aktualizuj pocet_ucastnikov v rokKonania
                    $stmt = $conn->prepare('SELECT COUNT(*) AS pocet FROM Bezec WHERE ID_roka = ?');
                    $stmt->execute([$id_roka]);
                    $pocet = $stmt->fetch()['pocet'];
                    $stmt = $conn->prepare('UPDATE rokKonania SET pocet_ucastnikov = ? WHERE ID_roka = ?');
                    $stmt->execute([$pocet, $id_roka]);
                    $success = 'Registrácia prebehla úspešne!';
                } catch (\Exception $e) {
                    $error = 'Chyba pri registrácii: ' . $e->getMessage();
                }
            }
        }
        return $this->html([
            'success' => $success,
            'error' => $error
        ]);
    }
    public function resultsPage(Request $request): Response
    {
        $conn = \Framework\DB\Connection::getInstance();
        $resultsYear = null;
        try {
            $stmt = $conn->query("SHOW TABLES LIKE 'settings'");
            $tableExists = $stmt->fetchColumn() !== false;
            if ($tableExists) {
                $s = $conn->prepare('SELECT v FROM settings WHERE k = ? LIMIT 1');
                $s->execute(['results_year']);
                $val = $s->fetchColumn();
                if ($val !== false && $val !== null && $val !== '') {
                    $resultsYear = $val;
                }
            }
        } catch (\Throwable $e) {
            $resultsYear = null;
        }

        $maleResults = [];
        $femaleResults = [];
        $resultsYearLabel = null;

        if ($resultsYear !== null) {
            // fetch human-readable year value from rokKonania
            try {
                $sr = $conn->prepare('SELECT rok FROM rokKonania WHERE ID_roka = ? LIMIT 1');
                $sr->execute([$resultsYear]);
                $row = $sr->fetch();
                if ($row && isset($row['rok'])) {
                    $resultsYearLabel = $row['rok'];
                }
            } catch (\Throwable $e) {
                $resultsYearLabel = $resultsYear; // fallback to ID
            }

            $stmtM = $conn->prepare('SELECT meno, priezvisko, cas_dobehnutia FROM Bezec WHERE pohlavie = ? AND ID_roka = ? ORDER BY (cas_dobehnutia IS NULL), cas_dobehnutia ASC');
            $stmtM->execute(['M', $resultsYear]);
            $maleResults = $stmtM->fetchAll();

            $stmtF = $conn->prepare('SELECT meno, priezvisko, cas_dobehnutia FROM Bezec WHERE pohlavie = ? AND ID_roka = ? ORDER BY (cas_dobehnutia IS NULL), cas_dobehnutia ASC');
            $stmtF->execute(['Ž', $resultsYear]);
            $femaleResults = $stmtF->fetchAll();


            // --- credit matching user accounts: removed ---
            // The previous implementation updated Pouzivatelia.zabehnute_kilometre and Pouzivatelia.vypite_piva here.
            // That logic has been intentionally removed so it can be redesigned and implemented elsewhere.
            // No database writes are performed in this controller action.

        }

        return $this->html([
            'maleResults' => $maleResults,
            'femaleResults' => $femaleResults,
            'resultsYear' => $resultsYear,
            'resultsYearLabel' => $resultsYearLabel
        ]);
    }

    /**
     * Displays the user profile page for the currently logged-in user.
     * Requires the user to be logged in; otherwise redirects to the login page.
     */
    public function profile(Request $request): Response
    {
        if (!$this->user->isLoggedIn()) {
            return $this->redirect(\App\Configuration::LOGIN_URL);
        }

        $identity = $this->user->getIdentity();
        return $this->html(compact('identity'));
    }
}
