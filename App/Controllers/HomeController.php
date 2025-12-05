<?php

namespace App\Controllers;

use Framework\Core\BaseController;
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
     * Displays the contact page.
     *
     * This action serves the HTML view for the contact page, which is accessible to all users without any
     * authorization.
     *
     * @return Response The response object containing the rendered HTML for the contact page.
     */
    public function contact(Request $request): Response
    {
        return $this->html();
    }
    public function galleryPage(Request $request): Response
    {
        return $this->html();
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
                        $stmt = $conn->prepare('INSERT INTO rokKonania (rok, datum_konania, pocet_ucastnikov) VALUES (?, ?, 0)');
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
        return $this->html();
    }
}
